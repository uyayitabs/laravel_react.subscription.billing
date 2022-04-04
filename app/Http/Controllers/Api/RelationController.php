<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Models\BankAccount;
use App\Http\Requests\PersonApiRequest;
use App\Http\Requests\RelationPersonApiRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PersonResource;
use App\Http\Resources\RelationPersonResource;
use App\Http\Resources\RelationResource;
use App\Http\Resources\SalesInvoiceResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Payment;
use App\Models\Person;
use App\Models\RelationsPerson;
use App\Models\SalesInvoice;
use App\Services\PersonService;
use App\Services\SalesInvoiceService;
use App\Services\SubscriptionService;
use App\Models\Subscription;
use Logging;
use App\Models\Relation;
use App\Http\Requests\BankAccountApiRequest;
use App\Http\Requests\PaymentInvoiceApiRequest;
use App\Http\Requests\RelationApiRequest;
use App\Http\Requests\RelationCsApiRequest;
use App\Http\Resources\PortalCustomerResource;
use App\Services\AddressService;
use App\Services\BankAccountService;
use App\Services\PaymentService;
use App\Services\RelationService;
use App\Models\User;
use Illuminate\Support\Arr;

class RelationController extends BaseController
{
    protected $bankAccountService;
    protected $service;
    protected $paymentService;
    protected $personService;

    public function __construct()
    {
        parent::__construct();
        $this->bankAccountService = new BankAccountService();
        $this->service = new RelationService();
        $this->paymentService = new PaymentService();
        $this->personService = new PersonService();
    }

    /**
     * Return a listing of customers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendPaginateOrResult(
            $this->service->list(),
            'Relations list received successfully.',
            function (Relation $relation) {
                return (new RelationResource(
                    $relation,
                    true
                ));
            }
        );
    }

    /**
     * Store a newly created customer
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RelationApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->service->create($data);
    }

    /**
     * Store a newly created customer
     *
     * @return \Illuminate\Http\Response
     */
    public function storeCs(RelationCsApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->service->createCs($data);
    }

    /**
     * Return the specified relation
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Relation $relation)
    {
        $this->authorize('view', $relation);
        return $this->service->show(
            $relation->id,
            'Successfully received relation(s)'
        );
    }

    /**
     * Update the specified relation
     *
     * @param \App\Models\Relation $relation
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Relation $relation, RelationApiRequest $request)
    {
        $datas = jsonRecode($request->all());
        return $this->service->update($datas, $relation);
    }

    /**
     * Remove the specified relation
     *
     * @param \App\Models\Relation $relation
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Relation $relation)
    {
        $relation->delete();
        Logging::information('Delete Relation', $relation, 1, 1);
        return $this->sendResult($relation, 'Customer deleted successfully.');
    }

    /**
     * Return a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResponse(
            $this->service->optionList(request()),
            'Customer lists retrieved successfully.'
        );
    }

    /**
     * Return a listing of the customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary()
    {
        return $this->sendPaginateOrResult(
            $this->service->summary(),
            'Relation summary retrieved successfully.'
        );
    }


    /**
     * Return a list of subscriptions belong to a specified relation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriptions(Relation $relation)
    {
        $this->authorize('view', $relation);
        $subscriptionService = new SubscriptionService();

        return $this->sendPaginateOrResult(
            $subscriptionService->list($relation->id),
            'Relation subscriptions retrieved successfully',
            function (Subscription $subscription) {
                return (new SubscriptionResource(
                    $subscription
                ));
            }
        );
    }

    /**
     * Return a list of addresses belong to a relation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addresses(Relation $relation)
    {
        $this->authorize('view', $relation);
        $addressService = new AddressService();

        return $this->sendPaginateOrResult(
            $addressService->list($relation->id),
            'Relation addresses retrieved successfully',
            function (Address $address) {
                return (new AddressResource(
                    $address
                ));
            }
        );
    }

    /**
     * Return a list of persons belong to a relation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexPersons(Relation $relation)
    {
        $this->authorize('view', $relation);
        $personService = new PersonService();
        $relationId = $relation->id;

        return $this->sendPaginateOrResult(
            $personService->listRelationsPersons($relation->id),
            'Relation persons retrieved successfully',
            function (Person $person) use ($relationId) {
                return (new RelationPersonResource(
                    $person,
                    RelationsPerson::where([['relation_id', $relationId], ['person_id', $person->id]])->first()
                ));
            }
        );
    }

    /*
     * Return a list of salesInvoices belong to a relation.
     *
     * @param mixed $relation
     */
    public function salesInvoices(Relation $relation)
    {
        $this->authorize('view', $relation);
        $invoiceService = new SalesInvoiceService();

        return $this->sendPaginateOrResult(
            $invoiceService->list($relation->id),
            'Relation invoices retrieved successfully',
            function (SalesInvoice $invoice) use ($relation) {
                return (new SalesInvoiceResource(
                    $invoice
                ));
            }
        );
    }

    public function bankAccounts(Relation $relation)
    {
        $this->authorize('view', $relation);
        return $this->bankAccountService->list($relation);
    }

    public function storeBankAccount(Relation $relation, BankAccountApiRequest $request)
    {
        $attribute = request([
            'relation_id',
            'description',
            'status',
            'bank_name',
            'iban',
            'bic',
            'dd_default',
            'mndt_id',
            'dt_of_sgntr',
            'amdmnt_ind'
        ]);
        $attribute['iban'] = str_replace(" ", "", $attribute['iban']);

        if (
            BankAccount::where([
                ['relation_id', $relation->id],
                ['iban', $attribute['iban']]
            ])->count() > 0
        ) {
            return $this->sendError('Iban is already taken', [], 422);
        }

        $result = $this->bankAccountService->create($relation, $attribute);
        if ($result['success']) {
            return $this->sendResult($result['data'], $result['message']);
        } else {
            return $this->sendError($result['errorMessage'], [], 422);
        }
    }

    public function updateBankAccount(Relation $relation, $bankAccount, BankAccountApiRequest $request)
    {
        $info = $this->bankAccountService->info($bankAccount);

        $attribute = request([
            'relation_id',
            'description',
            'status',
            'bank_name',
            'iban',
            'bic',
            'dd_default',
            'mndt_id',
            'dt_of_sgntr',
            'amdmnt_ind'
        ]);
        $attribute['iban'] = str_replace(" ", "", $attribute['iban']);

        if (
            BankAccount::where([
                ['relation_id', $relation->id],
                ['iban', $attribute['iban']],
                ['id', '!=', $bankAccount]
            ])->count() > 0
        ) {
            return $this->sendError('This IBAN has already been taken', [], 422);
        }

            $result = $this->bankAccountService->update($info, $attribute);
        if ($result['success']) {
            return $this->sendResult($result['data'], $result['message']);
        } else {
            return $this->sendError($result['errorMessage'], [], 422);
        }
    }

    public function nextMndtId(Relation $relation)
    {
        return $this->sendResult(
            $this->bankAccountService->nextMndtId($relation),
            'Customer deleted successfully.'
        );
    }

    /**
     * Return a paginated list of payments
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function relationPayments($relation = null)
    {
        return $this->sendPaginate(
            $this->paymentService->listRelationPayments($relation),
            'Payment listing retrieved successfully',
            function (Payment $payment) {
                return (new PaymentResource(
                    $payment,
                    'Payment retrieved successfully.',
                    true,
                    true
                ));
            }
        );
    }

    /**
     * Return paginated list of invoices with no payment set
     *
     * @param mixed|null $relation
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function relationPaymentInvoices($relation = null)
    {
        return $this->sendPaginate(
            $this->paymentService->invoicesNoPayment($relation),
            'Invoice listing retrieved successfully'
        );
    }

    /**
     * Set invoice_id for a payment
     *
     * @param mixed|null $relation
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function setPaymentInvoice(PaymentInvoiceApiRequest $request)
    {
        $paymentId = $request->payment_id;
        $invoiceId = $request->invoice_id;

        $query = $this->paymentService->setPaymentInvoice($paymentId, $invoiceId);

        if (!blank($query)) {
            return $this->service->show($paymentId, 'Payment updated successfully.');
        } else {
            return $this->sendError(
                'Error encountered setting invoice for a payment.',
                [
                    'payment_id' => $paymentId,
                    'invoice_id' => $invoiceId
                ],
                500
            );
        }
    }

    /**
     * Return a list of subscriptions belong to a specified relation.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscriptionsUnpaginated(Relation $relation)
    {
        return $this->service->subscriptionsUnpaginated($relation->id);
    }

    /**
     * Get relations_persons.person
     *
     * @param Relation $relation
     * @param Person $person
     * @return RelationPersonResource|null
     */
    public function showPerson(Relation $relation, Person $person): ?RelationPersonResource
    {
        if (RelationsPerson::where([['relation_id', $relation->id], ['person_id', $person->id]])->exists()) {
            return $this->personService->showRelationPerson($person->id, $relation->id, 'Person retrieved successfully.');
        }
        return null;
    }

    /**
     * Save relations_persons.person
     *
     * @param RelationPersonApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePerson(Relation $relation, RelationPersonApiRequest $request)
    {
        $params = $request->all();
        $params['relation_id'] = $relation->id;
        $result = $this->personService->create($params);
        if ($result && $result['success']) {
            return $this->sendResult(
                $result,
                'Person created successfully'
            );
        } elseif ($result) {
            return $this->sendError($result['message'], [], 422);
        }
        return $this->sendError('Unexpected error creating Person', [], 500);
    }

    /**
     * Update relations_persons.person
     *
     * @param Relation $relation
     * @param Person $person
     * @param RelationPersonApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePerson(Relation $relation, Person $person, RelationPersonApiRequest $request)
    {
        $result = $this->personService->updateRelationPerson($request->all(), $relation, $person);
        if ($result && $result['success']) {
            return $this->sendResult(
                $result,
                'Person created successfully.'
            );
        } elseif ($result) {
            return $this->sendError($result['message'], [], 422);
        }
        return $this->sendError('Unexpected error updating Person', [], 500);
    }

    /**
     * Delete a relations_persons.person
     *
     * @param Relation $relation
     * @param Person $person
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyPerson(Relation $relation, Person $person)
    {
        $result = $this->personService->deleteRelationPerson($relation, $person);
        if (isset($result) && $result['success']) {
            return $this->sendResult(
                $result['data'],
                $result['message']
            );
        } elseif (isset($result)) {
            return $this->sendError($result['message'], [], 422);
        }
        return $this->sendError('Unexpected error updating Person', [], 500);
    }
}
