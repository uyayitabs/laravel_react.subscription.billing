<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Logging;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use App\Models\SubscriptionLinePrice;
use App\Models\JsonData;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\SubscriptionLinePriceResource;

class SubscriptionLineService
{
    protected $m7Service;
    protected $brightBlueService;
    protected $serialService;

    public function __construct()
    {
        $this->m7Service = new M7Service();
        $this->brightBlueService = new BrightBlueService();
        $this->serialService = new SerialService();
    }

    public function list($queryOnly = true)
    {
        return QueryBuilder::for(SubscriptionLine::class, request())
            ->allowedFields(SubscriptionLine::$fields)
            ->allowedIncludes(SubscriptionLine::$scopes)
            ->allowedFilters(SubscriptionLine::$fields)
            ->defaultSort('-id')
            ->allowedSorts(SubscriptionLine::$fields);
    }

    public function show($id)
    {
        return QueryBuilder::for(SubscriptionLine::where('id', $id))
            ->allowedFields(SubscriptionLine::$fields)
            ->allowedIncludes(SubscriptionLine::$scopes);
    }

    public function create(Subscription $subscription, array $inputParams)
    {
        $proceedSave = false;
        $message = "";
        $data = [];

        $statusService = new StatusService();
        $terminatedStatusId = $statusService->getStatusId('subscription', 'terminated');
        if ($subscription->status == $terminatedStatusId) {
            return [
                "data" => [],
                "message" => "Cannot create a new line on a terminated subscription"
            ];
        }

        $product = Product::find($inputParams['product_id']);
        if (!empty($product->backend_api)) {
            $backendApi = $product->backend_api;

            $productType = $product->jsonData->json_data[array_key_first($product->jsonData->json_data)]['type'];

            if ($productType === "basis") {
                $data = [
                    "basis_count" => 0,
                    "provisioned" => false,
                    "deprovisioned" => false,
                    "productType" => $productType
                ];

                if ($backendApi === "brightblue") {
                    $data = $subscription->brightblue_basis_provisioning_status;
                } elseif ($backendApi === "m7") {
                    $data = $subscription->m7_basis_provisioning_status;
                }

                if ($data["basis_count"] === 0) {
                    if (
                        ($data["provisioned"] === false && $data["deprovisioned"] === true) ||
                        $data["provisioned"] === false && $data["deprovisioned"] === false
                    ) {
                        $proceedSave = true;
                        $message = "";
                    }
                } else {
                    if ($data["basis_count"] < 2) {
                        if ($data["provisioned"] === false && $data["deprovisioned"] === true) {
                            $proceedSave = true;
                            $message = "";
                        } elseif (
                            ($data["provisioned"] === true && $data["deprovisioned"] === false) ||
                            ($data["provisioned"] === true && $data["deprovisioned"] === true) ||
                            ($data["provisioned"] === false && $data["deprovisioned"] === true) ||
                            ($data["provisioned"] === false && $data["deprovisioned"] === false)
                        ) {
                            $proceedSave = false;
                            $message = "Only one (1) ";
                            $message .= strtoupper($backendApi);
                            $message .= " BASE allowed in a Subscription.";
                        }
                    } else {
                        if (
                            ($data["provisioned"] === true && $data["deprovisioned"] === false) ||
                            ($data["provisioned"] === true && $data["deprovisioned"] === true) ||
                            ($data["provisioned"] === false && $data["deprovisioned"] === false)
                        ) {
                            $proceedSave = false;
                            $message = "Only one (1) ";
                            $message .= strtoupper($backendApi);
                            $message .= " BASE allowed in a Subscription.";
                        } elseif (
                            ($data["provisioned"] === false && $data["deprovisioned"] === false) ||
                            ($data["provisioned"] === false && $data["deprovisioned"] === true)
                        ) {
                            $proceedSave = true;
                            $message = "";
                        }
                    }
                }
            } else {
                $proceedSave = true;
                $message = "";
            }
        } else {
            $proceedSave = true;
            $message = "";
        }

        if ($proceedSave) {
            DB::beginTransaction();
            $slpService = new SubscriptionLinePriceService();
            $subscriptionLine = $this->createSubscriptionLine($subscription, $inputParams);
            if (array_key_exists('subscription_line_prices', $inputParams)) {
                foreach ($inputParams['subscription_line_prices'] as $priceInputParams) {
                    $slpCreateResult = $slpService->create($subscriptionLine, $priceInputParams);
                    if (!$slpCreateResult['success']) {
                        DB::rollBack();
                        return $slpCreateResult;
                    }
                }
            }
            DB::commit();

            return [
                "success" => true,
                "data" => $this->getOne(['id' => $subscriptionLine->id]),
                "message" => null
            ];
        }
        Logging::error(
            'Error saving Subscription Line for Subscription',
            [
                "subscription_id" => $subscription->id,
                "relation_id" => $subscription->relation_id
            ],
            1,
            1
        );
        return [
            "success" => false,
            "data" => [],
            "message" => $message
        ];
    }

    public function delete(SubscriptionLine $subscriptionLine)
    {
        $statusService = new StatusService();
        $terminatedStatusId = $statusService->getStatusId('subscription', 'terminated');
        if ($subscriptionLine->subscription->status == $terminatedStatusId) {
            return [
                "data" => [],
                "errorMessage" => "Cannot create a new line on a terminated subscription"
            ];
        }
        Logging::information('Delete Subscription Line', $subscriptionLine, 1, 1);
        $subscriptionLine->delete();
        return $this->list();
    }

    public function update(SubscriptionLine $subscriptionLine, array $inputParams)
    {
        $log['old_values'] = $subscriptionLine->getRawDBData();
        $proceedSave = true;
        $errorMessage = 'Error updating Subscription Line';

        $statusService = new StatusService();
        $terminatedStatusId = $statusService->getStatusId('subscription', 'terminated');
        if ($subscriptionLine->subscription->status == $terminatedStatusId) {
            return [
            'success' => false,
            'message' => 'Cannot edit a terminated subscription'
            ];
        }

        if (array_key_exists('subscription_start', $inputParams)) {
            // Updating start date is not allowed after an invoice has been created for this line
            // if subscription_start is being changed
            $isStartDateEarlier = ($inputParams['subscription_start'] < $subscriptionLine->subscription_start->format("Y-m-d"));
            $isLineInvoiced = ($subscriptionLine->salesinvoiceLines->count() > 0);
            if ($isStartDateEarlier && $isLineInvoiced) {
                return [
                'success' => false,
                'message' => 'Updating start date is not allowed after an invoice has been created.'
                ];
            }

            // Update line prices if price_valid_from of line price is:
            // After new subscription line start
            // Before or at old subscription line start
            // There is only one line price meeting the above criteria
            // Do not forget that updating the subscription line is still possible if:
            // At least one price exists before new subscription line start
            $prices = $subscriptionLine->subscriptionLinePrices;
            $hasValidPrice = ($prices
                    ->where('price_valid_from', '<=', $subscriptionLine->subscription_start)->count() > 0 ||
                $prices->count() === 0);
            if ($isStartDateEarlier && !$hasValidPrice) {
                return [
                'success' => false,
                'message' => 'Cannot set StartDate of SubscriptionLine to a date without a valid price'
                ];
            }

            $pricesBetween = $prices
                ->where('price_valid_from', '<=', $subscriptionLine->subscription_start)
                ->where('price_valid_from', '>=', $inputParams['subscription_start']);
            if ($isStartDateEarlier && $pricesBetween->count() > 1) {
                return [
                'success' => false,
                'message' => 'Cannot set StartDate of SubscriptionLine to a date with two valid prices'
                ];
            }

            if ($pricesBetween->count() === 1) {
                $price = $pricesBetween->first();
                $linePriceService = new SubscriptionLinePriceService();
                $result = $linePriceService->update($price, [
                    'price_valid_from' => $inputParams['subscription_start'],
                    'fixed_price' => $price->fixed_price,
                    'margin' => $price->margin]);
                if (!$result['success']) {
                    return $result;
                }
            }
        }

        if ($proceedSave) {
            DB::beginTransaction();
            $attributes = filterArrayByKeys($inputParams, SubscriptionLine::$fields);
            $subscriptionLine->update($attributes);

            $log['new_values'] = $subscriptionLine->getRawDBData();
            $log['changes'] = $subscriptionLine->getChanges();
            Logging::information('Update Subscription Line', $log, 1, 1);

            if (array_key_exists('subscription_line_prices', $inputParams)) {
                $result = $this->createSubscriptionLinePrices($subscriptionLine, $inputParams['subscription_line_prices']);
                if (!$result['success']) {
                    DB::rollBack();
                    return $result;
                }
            }

            DB::commit();
            return [
                "success" => true,
                "data" => $this->getOne(['id' => $subscriptionLine->id]),
                "message" => null
            ];
        }
        Logging::error('Error updating Subscription Line', [], 1, 1);
        return [
            "success" => false,
            "data" => null,
            "message" => $errorMessage
        ];
    }

    public function getOne($where = [], $queryOnly = true)
    {
        $query = QueryBuilder::for(SubscriptionLine::where($where))
            ->allowedIncludes(SubscriptionLine::$scopes);

        return $queryOnly ? $query : $query->first();
    }

    public function createSubscriptionLine($subscription, array $inputParams)
    {
        $attributes = filterArrayByKeys(
            $inputParams,
            [
                'subscription_line_type',
                'product_id',
                'serial',
                'mandatory_line',
                'subscription_start',
                'subscription_stop',
                'description',
                'description_long'
            ]
        );

        $subscriptionLine = $subscription->subscriptionLines()->create($attributes);
        Logging::information('Create Subscription Line', $subscriptionLine, 1, 1);
        return $subscriptionLine;
    }

    public function createSubscriptionLinePrices(SubscriptionLine $subscriptionLine, $subscriptionLinePrices)
    {
        $subscriptionLinePrices = collect($subscriptionLinePrices)->sortBy('price_valid_from')->toarray();
        $slpService = new SubscriptionLinePriceService();
        foreach ($subscriptionLinePrices as $slp) {
            if (isset($slp['id'])) {
                $subscriptionLinePrice = SubscriptionLinePrice::find($slp['id']);
                $result = $slpService->update($subscriptionLinePrice, $slp);
            } else {
                $result = $slpService->create($subscriptionLine, $slp);
            }

            if (!$result['success']) {
                return $result;
            }
        }
        return ['success' => true];
    }

    public function processRequest($provider, $method, SubscriptionLine $subscriptionLine)
    {
        $subscription = $subscriptionLine->subscription;
        $tenant = $subscription->relation->tenant;

        $valid = false;
        $now = now();

        switch ($provider) {
            case 'm7':
                $params = [
                    'CustomerNumber' => $subscription->customer_number,
                    'tenant_id' => $tenant->id
                ];

                switch ($method) {
                    case 'CaptureSubscriber':
                        if ('stb' == $subscriptionLine->json_data_product_type) {
                            $subscriptionLineJsonData = $subscriptionLine->json_data_m7;
                            $json_data_m7 = $subscriptionLineJsonData && $subscriptionLineJsonData->json_data &&
                            isset($subscriptionLineJsonData->json_data['m7']) ? $subscriptionLineJsonData->json_data['m7'] : null;
                            if ($json_data_m7 && $json_data_m7['status'] == 'Provisioning') {
                                $subscriptionLineJsonData->delete();
                            }
                            $m7Response = $this->m7Service->processSingleProvision($subscriptionLine);
                            $success = 'success' == strtolower($m7Response['Result']);
                            return [
                                'msg' => $success ? $m7Response['Result'] : $m7Response['ResultDescription'],
                                'data' => [], 'code' => $success ? 200 : 500
                            ];
                        }
                        $jsonData = $subscription->json_data_m7;
                        if ($jsonData) {
                            $json_data = $jsonData->json_data;
                            $jsonDataM7 = $json_data['m7'];
                            $status = strtolower($jsonDataM7['status']);
                            if ('failed' == $status || 'pending' == $status) {
                                $jsonDataM7['status'] = 'New';
                                $json_data['m7'] = $jsonDataM7;
                                $jsonData->json_data = $json_data;
                                $jsonData->save();

                                JsonData::where('subscription_id', $subscription->id)->whereNotNull('subscription_line_id')->delete();
                            }
                        }
                        $valid = true;
                        break;

                    case 'ChangePackage':
                        $pid = request('product');
                        if ($pid) {
                            $tenantProduct = $tenant->tenantProducts()->where('product_id', $pid['value'])->first();
                            if (!$tenantProduct) {
                                return [
                                    'msg' => 'No valid tenant product',
                                    'data' => [],
                                    'code' => 500
                                ];
                            }
                            $subscription_start = request('subscription_start');
                            if (!$subscription_start) {
                                $subscription_start = $now;
                            }
                            $lineData = [
                                'subscription_line_type' => 3,
                                'product_id' => $tenantProduct->product_id,
                                'description' => $tenantProduct->product->description,
                                'description_long' => $tenantProduct->product->description_long,
                                'subscription_start' => $subscription_start
                            ];

                            $newSubscriptionLine = $subscription->subscriptionLines()->create($lineData);
                            $newSubscriptionLine->subscriptionLinePrices()->create(
                                [
                                    'fixed_price' => $tenantProduct->price,
                                    'price_valid_from' => $subscription_start
                                ]
                            );

                            $subscriptionLine->subscription_stop = $subscription_start->addDays(-1);
                            $subscriptionLine->save();

                            if ($newSubscriptionLine->is_started) {
                                $valid = true;
                            } else {
                                return [
                                    'msg' => '',
                                    'data' => [],
                                    'code' => 200
                                ];
                            }
                        } else {
                            if ('addon' == $subscriptionLine->json_data_product_type) {
                                if ('Provisioned' == $subscriptionLine->m7_provisioning_status) {
                                    $subscriptionLine->subscription_stop = $now;
                                    $subscriptionLine->save();

                                    $subscriptionLineJsonData = $subscriptionLine->json_data_m7;
                                    $json_data = $subscriptionLineJsonData->json_data;
                                    $json_dataM7 = $json_data['m7'];
                                    $json_dataM7['state'] = 'Deprovisioned';
                                    $json_data['m7'] = $json_dataM7;
                                    $subscriptionLineJsonData->json_data = $json_data;
                                    $subscriptionLineJsonData->save();

                                    $valid = true;
                                }

                                if ('Provisioning' == $subscriptionLine->m7_provisioning_status) {
                                    $subscriptionLine->subscription_start = $now;
                                    $subscriptionLine->save();
                                    $valid = true;
                                }
                            } else {
                                $valid = true;
                            }
                        }

                        break;

                    case 'ChangeAddress':
                        $params['ContractNumber'] = $subscription->id;
                        $this->m7Service->setPerson($subscription->person_provisioning);
                        $this->m7Service->setBilling($subscription->address_provisioning);
                        $valid = true;
                        break;

                    case 'Disconnect':
                    case 'Reconnect':
                        $isMainSmartcard = $subscriptionLine->mac_address == $subscription->main_mac_address;
                        if (!$isMainSmartcard) {
                            $params['SmartcardNumber'] = $subscriptionLine->mac_address;
                        }
                        $valid = true;
                        break;

                    case 'ResetPin':
                    case 'ReAuthSmartcard':
                        $params['SmartcardNumber'] = $subscriptionLine->mac_address;
                        $valid = true;
                        break;

                    case 'CloseAccount':
                        $subscriptionLine->subscription_stop = $now;
                        $subscriptionLine->save();
                        $subscriptionLineJsonData = $subscriptionLine->json_data_m7;
                        $json_data = $subscriptionLineJsonData->json_data;
                        $json_dataM7 = $json_data['m7'];
                        $json_dataM7['state'] = 'Deprovisioned';
                        $json_data['m7'] = $json_dataM7;
                        $subscriptionLineJsonData->json_data = $json_data;
                        $subscriptionLineJsonData->save();

                        // set SmartcardNumber param if not main_smartcard
                        if ($subscriptionLine->mac_address !== $subscription->main_mac_address) {
                            $params['SmartcardNumber'] = $subscriptionLine->mac_address;
                        }
                        $valid = true;
                        break;

                    case 'ChangeMyAccount':
                    case 'CreateMyAccount':
                        $response = $this->m7Service->createMyAccount(
                            $provider,
                            $subscription->json_data_m7,
                            request('password'),
                            request('email')
                        );
                        $success = 'success' == strtolower($response['Result']);
                        return [
                            'msg' => $success ? $response['Result'] : $response['ResultDescription'],
                            'data' => [],
                            'code' => $success ? 200 : 500
                        ];

                    case 'RemoveMyAccount':
                        $valid = true;
                        $jsonData = $subscription->json_data_m7;
                        $json_data = $jsonData->json_data;
                        $jsonDataM7 = $json_data['m7'];
                        $params['Email'] = $jsonDataM7 && isset($jsonDataM7['account']) ? $jsonDataM7['account']['Email'] : '';
                        break;

                    case 'SwopSmartcard':
                        $serial = request('serial');
                        $mac_address = strtolower(implode('', explode(':', request('mac_address'))));
                        $params['SmartcardNumber'] = $mac_address;
                        $params['OldSmartcardNumber'] = $subscriptionLine->mac_address;
                        $valid = true;
                        break;

                    case 'SetLineProperties':
                        $params['LineType'] = 'UNKNOWN';
                        $params['LineProfile'] = 20;
                        $params['LineMinDownload'] = 15000;
                        $params['KpnPackageID'] = 10111;
                        $params['ChannelListType'] = 'CDS-R_OTT';
                        $valid = true;
                        break;
                }

                if ($valid) {
                    $this->m7Service->setSubscription($subscription);
                    $this->m7Service->setParams($params);
                    $m7Response = $this->m7Service->manager($method);

                    if ($m7Response['Result'] == 'SUCCESS') {
                        $slJd = $subscriptionLine->json_data_m7;
                        $slJsonData = $slJd->json_data;
                        $processes = isset($slJsonData['m7']['Processes']) ? $slJsonData['m7']['Processes'] : [];
                        if (!empty($processes) && empty($processes[0])) {
                            $processes = [];
                        }
                        switch ($method) {
                            case 'ReAuthSmartcard':
                                $processes[] = [
                                    'method' => 'ReAuthSmartcard',
                                    'Date' => now(),
                                    'transaction_id' => $m7Response['TransactionIdReturned'],
                                    'status' => 'Processing'
                                ];
                                $slJsonData['m7']['Processes'] = $processes;
                                $slJsonData['m7']['status'] = 'ReAuthingSmartcard';
                                $slJd->json_data = $slJsonData;
                                $slJd->transaction_id = $m7Response['TransactionIdReturned'];
                                $slJd->save();
                                break;

                            case 'SwopSmartcard':
                                $processes[] = [
                                    'method' => 'SwopSmartcard',
                                    'SmartcardNumber' => $slJsonData['m7']['SmartcardNumber'],
                                    'Date' => now(),
                                    'transaction_id' => $m7Response['TransactionIdReturned'],
                                    'status' => 'Processing'
                                ];
                                $slJsonData['m7']['Processes'] = $processes;

                                $cleanedSmartCardNumber = implode('', explode(':', $params['SmartcardNumber']));
                                $slJsonData['m7']['SmartcardNumber'] = strtoupper($cleanedSmartCardNumber);
                                $slJsonData['m7']['status'] = 'SwopingSmartcard';
                                $slJd->json_data = $slJsonData;
                                $slJd->transaction_id = $m7Response['TransactionIdReturned'];
                                $slJd->save();

                                $address_provisioning = $subscription->address_provisioning;

                                $warehouse = $subscription
                                    ->relation
                                    ->tenant
                                    ->warehouses()
                                    ->where('warehouse_location', $address_provisioning->id)
                                    ->first();

                                if (!$warehouse) {
                                    $warehouse = $subscription
                                        ->relation
                                        ->tenant
                                        ->warehouses()
                                        ->create([
                                            'warehouse_location' => $address_provisioning->id,
                                            'description' => $address_provisioning->full_address,
                                            'active_from' => $subscription->subscription_start,
                                            'status' => 'ACTIVE'
                                        ]);
                                }
                                $product = $subscriptionLine->product;
                                $exists = $product->serial()
                                    ->where('warehouse_id', $warehouse->id)
                                    ->where('serial', request('serial'))
                                    ->exists();
                                if (!$exists) {
                                    $product->serial()->create([
                                        'warehouse_id' => $warehouse->id,
                                        'serial' => $serial,
                                        'json_data' => [
                                            'serial' => [
                                                'mac' => $slJsonData['m7']['SmartcardNumber'],
                                                'serial' => $serial
                                            ]
                                        ]
                                    ]);
                                }

                                $subscriptionLine->serial = $serial;
                                $subscriptionLine->save();
                                break;

                            case 'CloseAccount':
                                // $jsonData = $subscriptionLine->jsonData;
                                // $m7JsonData = $jsonData->json_data['m7'];
                                // $m7JsonData['status'] = 'Deprovisioning';
                                // $m7JsonData['deprovision_date'] = now();
                                // $m7JsonData['transaction_id'] = isset($m7JsonData['transaction_id']) ? $m7JsonData['transaction_id'] : [];
                                // $m7JsonData['transaction_id'][] = $m7Response['TransactionIdReturned'];
                                // $jsonData->json_data['m7'] = $m7JsonData;
                                // $jsonData->transaction_id = $m7Response['TransactionIdReturned'];
                                // $jsonData->save();
                                break;

                            case 'Disconnect':
                                $jsonData = $subscription->json_data_m7;
                                $json_data = $jsonData->json_data;

                                $m7JsonData = $json_data['m7'];
                                $m7JsonData['status'] = 'Disconnecting';

                                $transactionId = [];
                                if (isset($m7JsonData['transaction_id'])) {
                                    $transactionId = $m7JsonData['transaction_id'];
                                }
                                $m7JsonData['transaction_id'] = $transactionId;
                                $m7JsonData['transaction_id'][] = $m7Response['TransactionIdReturned'];

                                $json_data['m7'] = $m7JsonData;
                                $jsonData->json_data = $json_data;
                                $jsonData->transaction_id = $m7Response['TransactionIdReturned'];
                                $jsonData->save();
                                break;

                            case 'Reconnect':
                                $jsonData = $subscription->json_data_m7;
                                $json_data = $jsonData->json_data;

                                $m7JsonData = $json_data['m7'];
                                $m7JsonData['status'] = 'Reconnecting';
                                $transactionId = [];
                                if (isset($m7JsonData['transaction_id'])) {
                                    $transactionId = $m7JsonData['transaction_id'];
                                }
                                $m7JsonData['transaction_id'] = $transactionId;
                                $m7JsonData['transaction_id'][] = $m7Response['TransactionIdReturned'];

                                $json_data['m7'] = $m7JsonData;
                                $jsonData->json_data = $json_data;
                                $jsonData->transaction_id = $m7Response['TransactionIdReturned'];
                                $jsonData->save();
                                break;

                            case 'RemoveMyAccount':
                                $jd = $subscription->json_data_m7;
                                $json_data = $jd->json_data;
                                $m7JsonData = $json_data['m7'];
                                Arr::pull($m7JsonData, 'account');
                                $json_data['m7'] = $m7JsonData;
                                $jd->json_data = $json_data;
                                $jd->save();
                                break;
                        }
                    }

                    $success = 'success' == strtolower($m7Response['Result']);
                    return [
                        'msg' => $success ? $m7Response['Result'] : $m7Response['ResultDescription'],
                        'data' => [],
                        'code' => $success ? 200 : 500
                    ];
                }

                break;

            case 'brightblue':
                $params = [];

                // Subscription DATA
                $this->brightBlueService->setSubscription($subscription);
                $subscriptionBrightblueJsonData = $subscription->jsonDatas()
                    ->where('backend_api', 'brightblue')
                    ->first();
                $this->brightBlueService->setSubscriptionBrightblueJsonData($subscriptionBrightblueJsonData);

                // SubscriptionLine DATA
                $this->brightBlueService->setSubscriptionLine($subscriptionLine);
                $subscriptionLineJsonData = $subscriptionLine->jsonDatas()
                    ->where([
                        ['backend_api', '=', 'brightblue'],
                        ['subscription_line_id', '=', $subscriptionLine->id],
                        ["json_data->brightblue->{$tenant->slugged_name}->provisioning->status", "<>", "Deprovisioned"]
                    ])
                    ->first();

                $brightblueJsonData = ["accountNumber" => null];
                if (!empty($subscriptionLineJsonData) && !empty($subscriptionLineJsonData->json_data)) {
                    if (array_key_exists("brightblue", $subscriptionLineJsonData->json_data)) {
                        $tenantName = $subscription->relation->tenant->slugged_name;
                        $brightblueJsonData = $subscriptionLineJsonData->json_data['brightblue'][$tenantName];
                    }
                }
                $this->brightBlueService->setSubscriptionLineJsonData($subscriptionLineJsonData);

                $valid = false;
                $errorMsg = "";

                $isProvisioningExisting = array_key_exists('provisioning', $brightblueJsonData);
                if ($isProvisioningExisting && !empty($brightblueJsonData['provisioning'])) {
                    if ('Pending' == $brightblueJsonData['provisioning']['status']) {
                        $valid = false;
                        $errorMsg = 'Provisioning is still on pending state.';
                    } elseif ('Provisioned' == $brightblueJsonData['provisioning']['status']) {
                        if ($method == "NewAccount") {
                            $valid = false;
                            $errorMsg = "This is already provisioned.";
                        } else {
                            $valid = true;
                            $errorMsg = "";
                        }
                    } elseif ('Suspended' == $brightblueJsonData['provisioning']['status']) {
                        $valid = true;
                        $errorMsg = "";
                    } else {
                        $valid = false;
                        $errorMsg = "Unexpected error encountered!";
                    }
                } else {
                    $valid = true;
                    $errorMsg = "";
                }

                switch ($method) {
                    case 'NewAccount':
                        $params = [
                            'description' => request("description"),
                            'primaryUserName' => request("primaryUserName"),
                            'primaryUserPin' => request("primaryUserPin")
                        ];
                        break;

                    case 'NewActivationCode':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'description' => request('description'),
                            'maxUses' => request('maxUses'),
                            'expiryDate' => request('expiryDate'),
                        ];
                        break;

                    case 'CloseAccount':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'DisconnectAccount':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'ReconnectAccount':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'GetAccounts':
                        $params = [
                            'productCode' => 'base.fiber',
                            'minimumAccountNumber' => 1
                        ];
                        break;

                    case 'GetAccountDetails':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'SetAccountDescription':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'description' => request('description'),
                        ];
                        break;

                    case 'ActivateAccount':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'SuspendAccount':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'ResumeAccount':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'CancelAccount':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'SetUserName':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'userNumber' => request('userNumber'),
                            'name' => request('name'),
                        ];
                        break;

                    case 'SetPin':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'pin' => request('pin'),
                        ];
                        break;

                    case 'RemoveClient':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'clientNumber' => request('clientNumber'),
                        ];
                        break;

                    case 'SetClientName':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'clientNumber' => request('clientNumber'),
                            'clientName' => request('clientName'),
                        ];
                        break;

                    case 'GenerateActivationCode':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'confirmUserInterfaceName' => request('confirmUserInterfaceName'),
                        ];
                        break;

                    case 'GetActivationRequest':
                        $params = [
                            'activationCodeId' => request('activationCodeId'),
                            'timeout' => 60 * 60, //SET TO 1 hour
                        ];
                        break;

                    case 'ConfirmActivationRequest':
                        $params = [
                            'activationRequestId' => request('activationRequestId')
                        ];
                        break;

                    case 'DismissActivationRequest':
                        $params = [
                            'activationRequestId' => request('activationRequestId')
                        ];
                        break;

                    case 'GetUnconfirmedActivationCodes':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'GenerateUnconfirmedActivationCode':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'description' => request('description'),
                            'maxUses' => request('maxUses'),
                            'expiryDate' => request('expiryDate'),
                        ];
                        break;

                    case 'CancelUnconfirmedActivationCode':
                        $params = [
                            'activationCodeId' => $brightblueJsonData["accountNumber"]
                        ];
                        break;

                    case 'AddProduct':
                        $params = [
                            'activationCodeId' => $brightblueJsonData["accountNumber"],
                            'productCode' => request('productCode'),
                        ];
                        break;

                    case 'GetPackages':
                        $params = [];
                        break;

                    case 'CancelProduct':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'productCode' => request('productCode'),
                        ];
                        $valid = true;
                        break;

                    case 'SetMonthlyCreditLimit':
                        $params = [
                            'accountNumber' => $brightblueJsonData["accountNumber"],
                            'endUserAmount' => request('endUserAmount'),
                        ];
                        $valid = true;
                        break;

                    case 'GetTransactions':
                        $params = [
                            'pointer' => request('pointer'),
                            'limit' => request('limit'),
                        ];
                        break;
                }

                if ($valid) {
                    $this->brightBlueService->setParams($params);
                    $response = $this->brightBlueService->sendRequest($method);

                    if (isset($response['result']) && $response['result'] === 'success') {
                        return [
                            'msg' => $method . ' Success',
                            'data' => $response['response'],
                            'code' => 200
                        ];
                    } else {
                        return [
                            'msg' => $method . ' Error',
                            'data' => $response,
                            'code' => 500
                        ];
                    }

                    //return ['msg' => "Success", 'data' => $response, 'code' => 200];
                } else {
                    return [
                        'msg' => $errorMsg,
                        'data' => [
                            'method' => $method,
                            'subscription_id' => $subscriptionLine->subscription_id
                        ],
                        'code' => 500
                    ];
                }
                break;

            case 'lineProvisioning':
                $statusService = new StatusService();
                switch ($method) {
                    case 'StartProvisioning':
                    case 'RetryProvisioning':
                    case 'ReprovisionLine':
                        // Set line status to check_pending status
                        $subscriptionLine->update([
                            'status_id' => $statusService->getStatusId('connection', 'check_pending')
                        ]);
                        return [
                            'msg' => '',
                            'data' => [],
                            'code' => 200
                        ];
                        break;

                    case 'StartMigration':
                        // Set line status to pending_migration status
                        $subscriptionLine->update([
                            'status_id' => $statusService->getStatusId('connection', 'migration_confirmed')
                        ]);
                        return [
                            'msg' => '',
                            'data' => [],
                            'code' => 200
                        ];

                        break;
                    case 'RetryMigration':
                        // Set line status to pending_migration status
                        $subscriptionLine->update([
                            'status_id' => $statusService->getStatusId('connection', 'migration_confirmed')
                        ]);
                        return [
                            'msg' => '',
                            'data' => [],
                            'code' => 200
                        ];
                        break;

                    case 'CancelOrderMigration':
                        // Set line status to pending_cancel status
                        $subscriptionLine->update([
                            'status_id' => $statusService->getStatusId('connection', 'cancel_pending')
                        ]);

                        return [
                            'msg' => '',
                            'data' => [],
                            'code' => 200
                        ];
                        break;

                    case 'AbortProvisioning':
                        // Set line status to inactive status
                        $subscriptionLine->update([
                            'status_id' => $statusService->getStatusId('connection', 'inactive')
                        ]);
                        return [
                            'msg' => '',
                            'data' => [],
                            'code' => 200
                        ];
                        break;


                    case 'TerminateLine':
                        // Set line status to pending_termination status
                        $subscriptionLine->update([
                            'status_id' => $statusService->getStatusId('connection', 'pending_termination')
                        ]);
                        return [
                            'msg' => '',
                            'data' => [],
                            'code' => 200
                        ];
                        break;
                }
                break;
        }

        return [
            'msg' => '',
            'data' => [
                "method" => $method
            ],
            'code' => 500
        ];
    }

    public function newSerial(SubscriptionLine $subscriptionLine)
    {
        $serial = request('serial');
        $isExist = $this->serialService->isExist($serial);
        $isTaken = $this->serialService->isTaken($serial, $subscriptionLine->id);
        if ($isExist && $isTaken) {
            return ['data' => [], 'message' => 'Serial number is taken.', 'status' => 500];
        }

        if ($subscriptionLine->serial && $subscriptionLine->itemSerial) {
            $this->serialService->remove($subscriptionLine->itemSerial);
        }

        $mac = request('mac_address');
        $subscription = $subscriptionLine->subscription;
        $warehouse = $subscription
            ->relation
            ->tenant
            ->warehouses()
            ->where('warehouse_location', $subscription->address_provisioning->id)
            ->first();

        if (!$warehouse) {
            $warehouse = $subscription
                ->relation
                ->tenant
                ->warehouses()
                ->create([
                    'warehouse_location' => $subscription->address_provisioning->id,
                    'description' => $subscription->address_provisioning->full_address,
                    'active_from' => $subscription->subscription_start,
                    'status' => 'ACTIVE'
                ]);
        }

        if ($mac) {
            $mac = strtolower(implode('', explode(':', $mac)));
        }
        $subscriptionLine->product->serial()->create([
            'warehouse_id' => $warehouse->id,
            'serial' => $serial,
            'json_data' => [
                'serial' => [
                    'mac' => $mac,
                    'serial' => $serial
                ]
            ]
        ]);

        $subscriptionLine->serial = $serial;
        $subscriptionLine->save();

        return ['data' => $subscriptionLine, 'message' => '', 'status' => 200];
    }

    public function prices(SubscriptionLine $subscriptionLine)
    {
        return SubscriptionLinePriceResource::collection($subscriptionLine->subscriptionLinePrices);
    }
}
