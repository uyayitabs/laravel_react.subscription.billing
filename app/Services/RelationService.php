<?php

namespace App\Services;

use App\Models\BillingRun;
use App\DataViewModels\RelationSummary;
use App\Models\PaymentCondition;
use App\Models\RelationsPerson;
use Logging;
use App\Models\Relation;
use App\Models\Person;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Subscription;
use App\Models\Plan;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\RelationResource;
use App\Http\Resources\RelationCsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\RelationPrimaryPersonSortFilter;
use App\Http\Resources\PortalCustomerResource;
use App\Mail\OrderCustSubscrCreatedMail;
use App\Models\Order;

class RelationService
{
    protected $bankAccountService, $addressService, $personService, $subscriptionService, $userService;

    public function __construct()
    {
        $this->bankAccountService = new BankAccountService();
        $this->addressService = new AddressService();
        $this->personService = new PersonService();
        $this->subscriptionService = new SubscriptionService();
        $this->userService = new UserService();
    }

    public function list()
    {
        $query = \Querying::for(Relation::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('id')
            ->make()
            ->getQuery()
            ->where('tenant_id', currentTenant('id'));

        return $query;
    }

    public function summary()
    {
        $query = \Querying::for(RelationSummary::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('id')
            ->make()
            ->getQuery()
            ->where('tenant_id', currentTenant('id'));

        return $query;
    }

    public function saveRelation(array $data)
    {
        if (!isset($data['tenant_id'])) {
            $data['tenant_id'] = currentTenant('id');
        }
        $attributes = filterArrayByKeys($data, Relation::$fields);
        $attributes['customer_number'] = generateNumberFromNumberRange($data['tenant_id'], 'customer_number');
        Logging::information('Create Relation', $attributes, 1, 1);

        $relation = new Relation($attributes);

        if ($relation->save()) {
            return $relation;
        }

        return false;
    }

    public function create(array $params)
    {
        $data = filterArrayByKeys($params, Relation::$fields);
        $data['tenant_id'] = currentTenant('id');
        $relation = $this->saveRelation($data);

        if ($relation) {
            $bankAccountdata = request([
                'iban',
                'bic',
                'bank_account'
            ]);
            if (isset($bankAccountdata['iban'])) {
                $bankAccountdata['dd_default'] = 1;
                $bankAccountdata['status'] = 1;
                $bankAccountdata['mndt_id'] = $this->bankAccountService->nextMndtId($relation);
                $bankAccountdata['dt_of_sgntr'] = now();
                $relation->bankAccounts()->create($bankAccountdata);
            }
        }

        // return QueryBuilder::for(Relation::where('id', $relation->id))->allowedIncludes(Relation::$scopes);
        return [
            'data' => $this->show($relation->id),
            'Customer created successfully.'
        ];
    }

    public function createCs(array $params)
    {
        if (Person::where('email', $params['email'])->exists()) {
            return [
            'success' => false,
            'message' => 'Email is already taken by another Person.'
            ];
        }
        $bankAccountService = $this->bankAccountService;
        $addressService = $this->addressService;
        $personService = $this->personService;
        $subscriptionService = $this->subscriptionService;

        $tenantId = currentTenant('id');
        $paymentCondition = PaymentCondition::where([['tenant_id', $tenantId],['default', 1]])->first();

        // save relation
        $relationAttributes = filterArrayByKeys($params, Relation::$fields);
        $relationAttributes['status'] = 1;
        $relationAttributes['relation_type_id'] = 1;
        $relationAttributes['tenant_id'] = $tenantId;
        $relationAttributes['payment_condition_id'] = $paymentCondition ? $paymentCondition->id : null;
        $relation = $this->saveRelation($relationAttributes);

        if ($relation) {
            // save bank account
            $isDDDefault = $paymentCondition && strtolower($paymentCondition->description) === "automatisch incasso";
            $bankAccountAttributes = filterArrayByKeys($params, BankAccount::$fields);
            $bankAccountAttributes['description'] = $params['account_holder'];
            $bankAccountAttributes['dd_default'] = $isDDDefault;
            $bankAccountAttributes['status'] = 1;
            $bankAccountAttributes['mndt_id'] = $bankAccountService->nextMndtId($relation);
            $bankAccountAttributes['dt_of_sgntr'] = now();
            $bankAccountService->create($relation, $bankAccountAttributes);

            // save address billing
            $addressAttributes = filterArrayByKeys($params, Address::$fields);
            $addressAttributes['relation_id'] = $relation->id;
            $addressAttributes['address_type_id'] = 3;
            $addressBilling = $addressService->saveAddress($addressAttributes);

            // save address shipping
            $addressAttributes['address_type_id'] = 4;
            $addressService->saveAddress($addressAttributes);

            // save address provisioning
            $addressAttributes['address_type_id'] = 2;
            $addressProvisioning = $addressService->saveAddress($addressAttributes);

            // save person
            $personAttributes = filterArrayByKeys($params, Person::$fields);
            $personAttributes['status'] = 1;
            $personAttributes['person_type_id'] = 1;
            $personAttributes['language'] = 'nl-NL';
            $personAttributes['relation_id'] = $relation->id;
            $personAttributes['primary'] = 1;
            $person = $personService->savePerson($personAttributes);

            if ($person) {
                $attributes = [
                    'username' => $person->email,
                    'person_id' => $person->id,
                    'tenant_id' => $relation->tenant_id,
                    'relation_id' => $relation->id
                ];
                $this->userService->saveUser($attributes);
            }

            $plan = Plan::find($params['plan']);

            if ($plan) {
                // save subscription
                $subscriptionAttributes = filterArrayByKeys($params, Subscription::$fields);
                $subscriptionAttributes['type'] = 3;
                $subscriptionAttributes['plan_id'] = $plan->id;
                $subscriptionAttributes['description'] = $plan->description;
                $subscriptionAttributes['status'] = 0;
                $subscriptionAttributes['relation_id'] = $relation->id;
                $subscriptionAttributes['billing_address'] = $addressBilling->id;
                $subscriptionAttributes['provisioning_address'] = $addressProvisioning->id;
                $subscriptionAttributes['billing_person'] = $person->id;
                $subscriptionAttributes['provisioning_person'] = $person->id;
                $subscriptionAttributes['contract_period_id'] = $params['contract_period'];
                $subscriptionAttributes['network_operator_id'] = $params['network_operator'];
                $subscription = $subscriptionService->saveSubscription($subscriptionAttributes);


                // update order status / send freshdesk email
                if (array_key_exists('order_id', $params)) {
                    $log = [];
                    $order = Order::find($params['order_id']);
                    $log['old_values'] = $order->getRawDBData();
                    $statusService = new StatusService();
                    $processedStatusId = $statusService->getStatusId('order', 'processed');
                    if (!blank($order)) {
                        $order->update(['status_id' => $processedStatusId]);

                        // Send email to freshdesk about the new customer + subscription created
                        $freshdeskEmailRecipient = config('app.freshdesk_email_recipient');
                        $tenant = $relation->tenant;
                        if (array_key_exists('order_freshdesk_email', $tenant->settings)) {
                            $freshdeskEmailRecipient = $tenant->settings['order_freshdesk_email'];
                        }
                        Mail::to($freshdeskEmailRecipient)
                            ->send((new OrderCustSubscrCreatedMail(
                                [
                                    "order_id" => $order->id,
                                    "order_address_city" => $order->data['address']['city'],
                                    "order_address_status" => $order->data['address']['status'],
                                    "order_details" => json_encode($order->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                                    "customer_number" => $relation->customer_number,
                                    "customer_name" => $person->getAttribute('full_name'),
                                    "customer_email" => $relation->email,
                                    "customer_url" => config('app.front_url') . "/#/relations/$relation->id/details",
                                    "subscription_url" => $subscription instanceof Subscription ? config('app.front_url') . "/#/relations/$relation->id/$subscription->id/subscriptions" : '',
                                ]
                            )));


                        $log['new_values'] = $order->getRawDBData();
                        $log['changes'] = $order->getChanges();

                        Logging::information('Update Orders', $log, 1, 1);
                    }
                }

                return new RelationCsResource(
                    $relation,
                    'Customer created successfully.',
                    true,
                    $subscription
                );
            }
        }

        return [
            'data' => $this->show($relation->id),
            'message' => 'Customer created successfully.'
            ];
    }

    public function show($id)
    {
        return new RelationResource(Relation::find($id));
    }

    public function update(array $params, Relation $relation)
    {
        $data = filterArrayByKeys($params, Relation::$fields);
        if (array_key_exists("tenant_id", $data) && empty($data['tenant_id'])) {
            $data['tenant_id'] = currentTenant('id');
        }
        $log['old_values'] = $relation->getRawDBData();
        $relation->update($data);

        $log['new_values'] = $relation->getRawDBData();
        $log['changes'] = $relation->getChanges();

        Logging::information('Update Relation', $log, 1, 1);

        return [
            'data' => $this->show($relation->id),
            'Customer updated successfully.'
        ];
    }

    public function optionList(Request $request)
    {
        $query = Relation::where('tenant_id', currentTenant('id'))
            ->select(['id', 'customer_number as name']);

        if ($request->has('filter') && isset($request->filter['keyword'])) {
            $query->search($request->filter['keyword']);
        } else {
            $query->limit(50);
        }

        $data = $query->get();
        $return = [];
        foreach ($data as $i => $value) {
            $return[$i]['id'] = $value->id;
            $return[$i]['name'] = $value->name;
            if ($value->primary_person_full_name) {
                $return[$i]['name'] .= " | " . $value->primary_person_full_name;
            }
            if ($value->billing_address) {
                $return[$i]['name'] .= " | " . $value->billing_address;
            }
            $return[$i]['customer_number'] = $value->name;
            $return[$i]['primary_person_full_name'] = $value->primary_person_full_name;
            $return[$i]['billing_address'] = $value->billing_address;
            $return[$i]['iban'] = $value->iban;
        }

        return $return;
    }

    public function subscriptions($id)
    {
        $subscriptionService = new SubscriptionService();
        return $subscriptionService->list($id);
    }

    public function addresses($id, $option)
    {
        $addressService = new AddressService();
        return $addressService->list($id);
    }

    public function persons(Relation $relation, $option)
    {
        return $this->personService->listRelationsPersons($relation->id);
    }

    /**
     *
     * @param mixed $relationId
     * @param mixed $billingAddressId
     * @param mixed $billingPersonId
     * @param mixed $provisioningAddressId
     * @param mixed $provisioningPersonId
     *
     * @return array
     */
    public static function validateBillingProvisioningAddressPerson(
        $inputRelationId,
        $inputBillingAddressId,
        $inputBillingPersonId,
        $inputProvisioningAddressId,
        $inputProvisioningPersonId
    ) {
        $proceed = false;
        $errorMessage = "";

        $invoiceAddressIds = $shippingAddressIds = $relationPersonIds = $errors = [];
        $relation = Relation::find($inputRelationId);

        if ($relation) {
            $invoiceAddressIds = $relation->billingAddresses()
                ->pluck("id")
                ->toArray();
            $relationPersonIds = $relation->persons()
                ->pluck("id")
                ->toArray();

            $allowedAddressIds = $relation->provisioningAddresses()
                ->pluck("id")
                ->toArray();

            $isInputAddressIdExisting = in_array(
                $inputBillingAddressId,
                $invoiceAddressIds
            );
            if (!$isInputAddressIdExisting) {
                $errors[] = "billing address";
            }

            $isInputInvoicePersonIdExisting = in_array(
                $inputBillingPersonId,
                $relationPersonIds
            );
            if (!$isInputInvoicePersonIdExisting) {
                $errors[] = "billing person";
            }

            $isInputShippingAddressIdExisting = in_array(
                $inputProvisioningAddressId,
                $allowedAddressIds
            );
            if (!$isInputShippingAddressIdExisting) {
                $errors[] = "provisioning address";
            }

            $isInputShippingPersonIdExisting = in_array(
                $inputProvisioningPersonId,
                $relationPersonIds
            );
            if (!$isInputShippingPersonIdExisting) {
                $errors[] = "provisioning person";
            }

            $proceed = $isInputAddressIdExisting && $isInputInvoicePersonIdExisting &&
                $isInputShippingAddressIdExisting && $isInputShippingPersonIdExisting;
            if (!$proceed) {
                $errorMessage = "Invalid " . join(", ", $errors);
                $errorMessage .=  " for customer number `{$relation->customer_number}`.";
            }
        }
        return compact("proceed", "errorMessage");
    }

    public function count()
    {
        return $this->repository->count();
    }

    /**
     * Show portal user data
     *
     * @param mixed $userId
     * @param string $message
     * @param bool $code
     * @return PortalCustomerResource
     */
    public function showPortalUser($relationId, $message = '', $code = true)
    {
        $user = request()->user();
        if (is_numeric($relationId)) {
            $person = RelationsPerson::where([
                ['person_id', $user->person->id],
                ['relation_id', $relationId]])->first();

            if (!blank($person)) {
                return new PortalCustomerResource(
                    $user,
                    $message,
                    true
                );
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
            'data'    => null,
        ], 403);
    }

    /**
     * List non-paginated subscriptions
     * @param mixed $id
     * @return BaseResourceCollection
     */
    public function subscriptionsUnpaginated($id)
    {
        $subscriptionService = new SubscriptionService();
        return $subscriptionService->listUnpaginated($id);
    }
}
