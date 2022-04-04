<?php

namespace App\Services;

use Logging;
use App\Repositories\JsonDataRepository;
use App\Models\Subscription;
use SoapClient;
use SoapHeader;
use SoapVar;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Jobs\SendM7Mail;
use App\Jobs\SendM7AdminReportMail;
use Intervention\Validation\Validator;
use Intervention\Validation\Rules\Iban;

class M7Service
{
    protected $jsonDataRepository;
    protected $subscription;
    protected $relation;
    protected $billing;
    protected $person;
    protected $product;
    protected $subscriptionLines;
    protected $isValid = true;
    protected $contractPeriod = 1;
    protected $dealerNumber;
    protected $optedForNewsletter = 'false';
    protected $customerNumber;
    protected $productsStb = [];
    protected $products = [];
    protected $decodernumber = '';
    protected $transactionType = '';
    protected $wishDate = '';
    protected $arrSmartCardPackages = [];
    protected $accountType;
    protected $messages = [];
    protected $forProvision = false;
    protected $forDeProvision = false;
    public $xmlRequest = '';
    protected $wsdl = '';
    protected $transaction = '';
    protected $stbs = [];
    protected $params = [];
    protected $isPending = false;
    protected $mainStbLine;
    protected $m7_product_lines;
    protected $m7_stb_lines;
    protected $pass = false;
    protected $method;
    protected $validator;
    protected $deprovisioningLog = [];

    public function __construct()
    {
        $this->jsonDataRepository = new JsonDataRepository();
        $this->dealerNumber = config('m7.dealer_code');
        $this->accountType = config('m7.account_type');
        $this->wsdl = config('m7.url');
        $this->validator = new Validator(new Iban());
    }

    public function setParams($params)
    {
        foreach ($params as $key => $val) {
            $this->params[$key] = $val;
        }
    }

    public function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }

    public function setSubscription(Subscription $subscription)
    {
        $this->messages = [];
        $this->isValid = true;

        $this->subscription = $subscription;
        $this->isPending = $subscription->jsonData &&
            isset($subscription->json_data_m7->json_data['m7']) &&
            isset($subscription->json_data_m7->json_data['m7']['status']) &&
            'Pending' == $subscription->json_data_m7->json_data['m7']['status'];

        if ($this->isPending) {
            return;
        }

        if ($this->subscription->m7_provisioned) {
            $m7_has_provisioning = $this->subscription->m7_has_provisioning;
            $has_provisioning = $m7_has_provisioning['hasNewStb'] || $m7_has_provisioning['hasNewProd'];
            $m7_has_deprovisioning = $this->subscription->m7_has_deprovisioning;
            $has_deprovisioning = $m7_has_deprovisioning['hasDeProStb'] || $m7_has_deprovisioning['hasDeProProd'];
            $this->pass = $has_provisioning || $has_deprovisioning;
            if (!$this->pass) {
                return;
            }
        }

        if (!$subscription) {
            $this->isValid = false;
            $this->messages[] = 'Empty subscription';
            return;
        }

        $jsonData = $subscription->json_data_m7;
        $isM7 =  $jsonData && array_key_exists('m7', $jsonData->json_data);
        $m7_provisionable = $subscription->m7_provisionable;

        $m7JsonData = [];

        if (!$subscription->m7_provisioned && $jsonData && $isM7) {
            $m7JsonData = $jsonData->json_data['m7'];
            if (array_key_exists('transaction', $m7JsonData)) {
                if (array_key_exists('status', $m7JsonData) && $m7JsonData['status'] != 'Provisioned') {
                    $this->transaction = $m7JsonData['transaction'];
                }
            }
        }

        if ($this->transaction == 'migration') {
            if (
                $isM7 &&
                !empty($m7JsonData) &&
                array_key_exists('stbs', $m7JsonData) &&
                $m7JsonData['stbs'] != ""
            ) {
                $this->stbs = explode(',', $m7JsonData['stbs']);
                $iStbs = count($this->stbs);
                $iProductsStb = $m7_provisionable['stbCount'];

                if ($iStbs > 0 && $iProductsStb != $iStbs) {
                    $this->isValid = false;
                    $this->messages[] = 'Stbs not match $iStbs-' . $iStbs . ' : $iProductsStb-' . $iProductsStb;
                }
            } else {
                $this->isValid = false;
                $this->messages[] = 'Missing migration mac address';
            }
        } else {
            $this->stbs = $subscription->mac_addresses;
        }

        $this->relation = $subscription->relation;
        if (!$this->relation) {
            $this->isValid = false;
            $this->messages[] = 'Empty relation';
        } elseif (!$this->validator->validate($this->relation->iban)) {
            $this->isValid = false;
            $this->messages[] = 'Invalid IBAN';
        }

        // Address object
        $this->billing = $subscription->address_provisioning;
        if (!$this->billing) {
            $this->isValid = false;
            $this->messages[] = 'Empty billing';
        } elseif (!blank($this->billing->house_number) && !is_numeric($this->billing->house_number)) {
            $this->isValid = false;
            $this->messages[] = 'Can\'t start provisioning, housenumber field must be numeric';
        }

        $this->person = $subscription->person_provisioning;
        if (!$this->person) {
            $this->isValid = false;
            $this->messages[] = 'Empty person';
        }

        $this->subscriptionLines = $subscription->provider_lines;
        if (!$this->subscriptionLines) {
            $this->isValid = false;
            $this->messages[] = 'Empty subscription lines';
        }

        if (!$m7_provisionable['hasBasis']) {
            $this->isValid = false;
            $this->messages[] = 'No basis';
        }

        if (!$m7_provisionable['hasStb']) {
            $this->isValid = false;
            $this->messages[] = 'No setup box';
        }

        if ($m7_provisionable['basicCount'] > 1) {
            $this->isValid = false;
            $this->messages[] = 'Multiple basis';
        }

        if ($m7_provisionable['invalidProdIds']) {
            $this->isValid = false;
            $this->messages[] = 'Multiple addon ProductId';
        }

        if (empty($this->stbs)) {
            $this->isValid = false;
            $this->messages[] = 'Missing mac address';
        }

        if ($this->person) {
            if (!$this->person->birthdate) {
                if (config('app.env') == 'production') {
                    $this->isValid = false;
                    $this->messages[] = 'Missing birthdate';
                } else {
                    $this->person->birthdate = '1970-01-01';
                }
            }

            if ($this->person->age < 18) {
                $this->isValid = false;
                $this->messages[] = 'Under age';
            }
        }

        $this->m7_product_lines = collect($subscription->m7_product_lines);
        $this->m7_stb_lines = collect($subscription->m7_stb_lines);
    }

    public function parameters()
    {
        $parameters = [
            'subscription' => $this->subscription,
            'relation' => $this->relation,
            'xmlRequest' => $this->xmlRequest,
            'wsdl' => $this->wsdl,
            'transaction' => $this->transaction,
            'stbs' => $this->stbs,
            'params' => $this->params,
            'isPending' => $this->isPending,
            'mainStbLine' => $this->mainStbLine,
            'm7_product_lines' => $this->m7_product_lines,
            'm7_stb_lines' => $this->m7_stb_lines,
            'pass' => $this->pass
        ];

        return $parameters;
    }

    public function validation()
    {

        return ['isValid' => $this->isValid, 'messages' => $this->messages];
    }

    public function setXmlRequest($xmlRequest)
    {
        $this->xmlRequest = $xmlRequest;
    }

    private function getTenantId()
    {
        $tenant_id = isset($this->params['tenant_id']) ? $this->params['tenant_id'] : null;
        $subscription = $this->subscription;

        if (!$tenant_id) {
            try {
                $tenant_id = $subscription && $subscription->relation ? $subscription->relation->tenant_id : null;
            } catch (\Exception $e) {
            }
        }
        return $tenant_id;
    }

    public function sendRequest($method)
    {
        $this->method = $method;
        if ('production' != config('app.env')) {
            return $this->processResponse($method, []);
        }

        $result = [
            'Result' =>  '',
            'Exception' => '',
            'ResultDescription' => '',
            'response' => [],
            'methodResult' =>  '',
            'TransactionIdReturned' => null
        ];

        $credentials = [
            'Method' => $method,
            'parameters' => $this->parameters()
        ];

        $client = $this->clientConnect($method);

        $ret = [];
        try {
            $this->xmlRequest = $this->m7GenerateRequest($method);
            if (!$this->xmlRequest) {
                $result['Result'] = 'Error';
                $result['Exception'] = 'Not valid for CloseAccount';
                return $result;
            }

            $actionHeader = new SoapHeader(
                'http://www.w3.org/2005/08/addressing',
                'Action',
                'http://tempuri.org/IM7Service/' . $method
            );
            $client->__setSoapHeaders($actionHeader);

            $soapBody = new SoapVar($this->xmlRequest, XSD_ANYXML);

            switch ($method) {
                case 'CaptureSubscriber':
                    $ret = $client->CaptureSubscriber($soapBody);
                    break;

                case 'ChangePackage':
                    $ret = $client->ChangePackage($soapBody);
                    break;

                case 'ChangeAddress':
                    $ret = $client->ChangeAddress($soapBody);
                    break;

                case 'SwopSmartcard':
                    $ret = $client->SwopSmartcard($soapBody);
                    break;

                case 'ReAuthSmartcard':
                    $ret = $client->ReAuthSmartcard($soapBody);
                    break;

                case 'CreateMyAccount':
                    $ret = $client->CreateMyAccount($soapBody);
                    break;

                case 'ChangeMyAccount':
                    $ret = $client->ChangeMyAccount($soapBody);
                    break;

                case 'Disconnect':
                    $ret = $client->Disconnect($soapBody);
                    break;

                case 'Reconnect':
                    $ret = $client->Reconnect($soapBody);
                    break;

                case 'CloseAccount':
                    $this->deprovisioningLog['start_time'] = now()->format("Y-m-d H:i:s");
                    $ret = $client->CloseAccount($soapBody);
                    break;

                case 'ResetPin':
                    $ret = $client->ResetPin($soapBody);
                    break;

                case 'UpdateTransactionStatus':
                    $ret = $client->UpdateTransactionStatus($soapBody);
                    break;

                case 'SetLineProperties':
                    $ret = $client->SetLineProperties($soapBody);
                    break;

                case 'GetCustomerInfo':
                    $ret = $client->GetCustomerInfo($soapBody);
                    break;

                case 'RemoveMyAccount':
                    $ret = $client->RemoveMyAccount($soapBody);
                    break;
            }

            $array = json_decode(json_encode($ret), true);
            $methodResult = $array[$method . 'Result'];

            if ("success" == strtolower($methodResult['Result'])) {
                switch ($method) {
                    case 'CloseAccount':
                        $this->processDeprovisioning($methodResult);
                        break;

                    case 'ChangePackage':
                        $smartcardNumber = $this->getParam('SmartcardNumber');
                        if (!$smartcardNumber) {
                            $smartcardNumber = $this->subscription->main_mac_address;
                        }
                        foreach ($this->products as $productObj) {
                            $subscriptionLineJsonData = $productObj['subscriptionLine']->json_data_m7;
                            if (!$subscriptionLineJsonData) {
                                $productObj['subscriptionLine']->jsonDatas()->create([
                                    'backend_api' => 'm7',
                                    'tenant_id' => $this->subscription->relation->tenant->id,
                                    'subscription_id' => $this->subscription->id,
                                    'json_data' => ['m7' => [
                                        'method' => $method,
                                        'productId' => $productObj['product']->jsonData->json_data['m7']['productId'],
                                        'SmartcardNumber' => $smartcardNumber
                                    ]],
                                    'transaction_id' => $methodResult['TransactionIdReturned']
                                ]);
                            } else {
                                $subscriptionLineJsonData->transaction_id = $methodResult['TransactionIdReturned'];
                                $json_data = $subscriptionLineJsonData->json_data;
                                $json_data['m7']['logs'] = ['method' => $method, 'date' => now()];
                                $subscriptionLineJsonData->json_data = $json_data;
                                $subscriptionLineJsonData->save();
                            }
                        }
                        break;

                    case 'CaptureSubscriber':
                        try {
                            $jsonDataSubscription = $this->subscription->json_data_m7;
                            $json_data = [];
                            if ($jsonDataSubscription) {
                                $json_data = $jsonDataSubscription->json_data;
                                $json_data['m7']['method'] = $method;
                                $json_data['m7']['status'] = 'Pending';
                                $json_data['m7']['remarks'] = $methodResult['Result'];
                                $transaction_ids = isset($json_data['m7']) && isset($json_data['m7']['transaction_id']) ? $json_data['m7']['transaction_id'] : [];
                                $transaction_ids[] = $methodResult['TransactionIdReturned'];
                                $json_data['m7']['transaction_id'] = $transaction_ids;
                                $jsonDataSubscription->transaction_id = $methodResult['TransactionIdReturned'];
                                $jsonDataSubscription->json_data = $json_data;
                                $jsonDataSubscription->save();
                            } else {
                                $json_data['method'] = $method;
                                $json_data['status'] = 'Pending';
                                $json_data['remarks'] = $methodResult['Result'];
                                $json_data['transaction_id'] = [$methodResult['TransactionIdReturned']];

                                $this->subscription->jsonDatas()->create([
                                    'backend_api' => 'm7',
                                    'tenant_id' => $this->subscription->relation->tenant_id,
                                    'transaction_id' => $methodResult['TransactionIdReturned'],
                                    'json_data' => ['m7' => $json_data]
                                ]);
                            }

                            foreach ($this->arrSmartCardPackages as $key => $jsonDataParam) {
                                $jsonDataParams = $jsonDataParam;
                                if (!empty($methodResult)) {
                                    if ($methodResult['Result'] == 'SUCCESS') {
                                        $jsonDataParams['transaction_id'] = $methodResult['TransactionIdReturned'];
                                    }
                                }
                                $jd = $this->jsonDataRepository
                                    ->getBy([
                                        'subscription_id' => $jsonDataParams['subscription_id'],
                                        'subscription_line_id' => $jsonDataParams['subscription_line_id']
                                    ])
                                    ->first();
                                if (!$jd) {
                                    $this->jsonDataRepository->create($jsonDataParams);
                                } else {
                                    $jd->transaction_id = $methodResult['TransactionIdReturned'];
                                    $jd->save();
                                }
                            }

                            if ($this->transaction == 'migration') {
                                $warehouse = $this->subscription
                                    ->relation
                                    ->tenant
                                    ->warehouses()
                                    ->where('warehouse_location', $this->subscription->address_provisioning->id)
                                    ->first();

                                if (!$warehouse) {
                                    $warehouse = $this->subscription
                                        ->relation
                                        ->tenant
                                        ->warehouses()
                                        ->create([
                                            'warehouse_location' => $this->subscription->address_provisioning->id,
                                            'description' => $this->subscription->address_provisioning->full_address,
                                            'active_from' => $this->subscription->subscription_start,
                                            'status' => 'ACTIVE'
                                        ]);
                                }

                                foreach ($this->productsStb as $inx => $stb) {
                                    $exists = $stb['product']->serial()->where('warehouse_id', $warehouse->id)->where('serial', $this->stbs[$inx])->exists();

                                    if (!$exists) {
                                        $stb['product']->serial()->create([
                                            'warehouse_id' => $warehouse->id,
                                            'serial' => $this->stbs[$inx],
                                            'json_data' => [
                                                'serial' => [
                                                    'mac' => $this->stbs[$inx],
                                                    'serial' => $this->stbs[$inx]
                                                ]
                                            ]
                                        ]);
                                    }

                                    $stb['subscriptionLine']->serial = $this->stbs[$inx];
                                    $stb['subscriptionLine']->save();
                                }
                            }
                        } catch (\Exception $exception) {
                            $logMsg = $exception->getMessage();
                            $severity = 'alert';
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }

            $result = [
                'Result' =>  $array[$method . 'Result']['Result'],
                'CustomerDTOInfo' => $array[$method . 'Result']['CustomerDTOInfo'],
                'Exception' => $array[$method . 'Result']['Exception'],
                'ResultDescription' => $array[$method . 'Result']['ResultDescription'],
                'response' => $array,
                'methodResult' =>  $methodResult,
                'TransactionIdReturned' => isset($methodResult['TransactionIdReturned']) ? $methodResult['TransactionIdReturned'] : null
            ];
            $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $client->__getLastRequest());
            $credentials['Xml'] = $requestXml;
            $credentials['Result'] = $array;
            $logMsg = $method . ' - ' . $array[$method . 'Result']['Result'];

            if ($methodResult['Result'] == 'SUCCESS') {
                Logging::information(
                    $logMsg,
                    $credentials,
                    16,
                    0,
                    $this->getTenantId(),
                    !is_null($this->subscription) ? 'subscription' : null,
                    $this->subscription->id ?? null
                );
            } else {
                $this->sendErrorReport($methodResult['Result'] . ", " . $result["ResultDescription"]);
                Logging::error(
                    $logMsg,
                    $credentials,
                    16,
                    0,
                    $this->getTenantId(),
                    !is_null($this->subscription) ? 'subscription' : null,
                    $this->subscription->id ?? null
                );
            }
        } catch (\SoapFault $exception) {
            $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $client->__getLastRequest());
            $result = [
                'error' => 1,
                'msg' => $exception->getMessage(),
                'xml' =>  $requestXml
            ];

            $credentials['Xml'] = $requestXml;
            $credentials['error_stacktrace'] = $exception->getTraceAsString();
            $logMsg = $method . ' - ' . $exception->getMessage();
            $this->sendErrorReport($logMsg);
            Logging::error(
                $logMsg,
                $credentials,
                16,
                0,
                $this->getTenantId(),
                !is_null($this->subscription) ? 'subscription' : null,
                $this->subscription->id ?? null
            );
        } catch (\Exception $exception) {
            $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $client->__getLastRequest());
            $result = [
                'error' => 2,
                'msg' => $exception->getMessage(),
                'xml' =>  $requestXml
            ];

            $credentials['Xml'] = $requestXml;
            $credentials['error_stacktrace'] = $exception->getTraceAsString();
            $logMsg = $method . ' - ' . $exception->getMessage();
            $this->sendErrorReport($logMsg);
            Logging::error(
                $logMsg,
                $credentials,
                16,
                0,
                $this->getTenantId(),
                !is_null($this->subscription) ? 'subscription' : null,
                $this->subscription->id ?? null
            );
        }

        return $result;
    }

    public function setBilling($billing)
    {
        $this->billing = $billing;
    }

    public function setPerson($person)
    {
        $this->person = $person;
        if ($this->person->birthdate == '') {
            $this->person->birthdate = '1970-01-01';
        }
    }

    private function sendErrorReport($message)
    {
        if ('local' != config('app.env')) {
            $subscription_id = $this->subscription->id;
            $link = Str::replaceArray('?', [Str::finish(config('app.front_url'), '/'), $subscription_id], '?#/subscriptions/?/details');
            $message = Str::replaceArray('?', [(gettype($message) == 'array' ? implode(', ', $message) : $message), $link, $subscription_id], '? for subscription <a href="?">?</a>');
            $admins_emails = config('m7.admins_email');
            $user =  request()->user();
            try {
                SendM7AdminReportMail::dispatchNow(
                    [
                        'message' => $message,
                        'job' => $this->method,
                        'username' => $user ? $user->username : null
                    ],
                    $admins_emails
                );
            } catch (\Exception $e) {
                Logging::exception(
                    $e,
                    2,
                    1,
                    $this->subscription ? $this->subscription->relation->tenant_id : null,
                    $this->subscription ? 'subscription' : null,
                    $this->subscription ? $this->subscription->id : null
                );
            }
        }
    }

    private function clientConnect($method)
    {
        $wsdl = config('m7.url');
        $option = [
            "uri" => null,
            'encoding' => 'utf-8',
            'soap_version' => SOAP_1_2,
            'trace' => true,
            'exceptions' => true
        ];

        try {
            $client = new SoapClient(
                $wsdl,
                $option
            );
        } catch (\Throwable $e) {
            $result = [];
            $result['Result'] = 'Error';
            $result['Exception'] = $e->getMessage();
            $result['Stacktrace'] = $e->getTraceAsString();
            Logging::error(
                'M7 conenction error',
                $result,
                16,
                1,
                $this->getTenantId(),
                $this->subscription ? 'subscription' : null,
                $this->subscription ? $this->subscription->id : null
            );
            $this->sendErrorReport($result['Exception']);
            return $result;
        }

        $actionHeader = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            'http://tempuri.org/IM7Service/' . $method
        );
        $client->__setSoapHeaders($actionHeader);
        return $client;
    }

    public function manager($method)
    {
        $this->method = $method;

        if ('CaptureSubscriber' != $method && 'ChangePackage' != $method) {
            return $this->sendRequest($method);
        }

        $credentials = [
            'Method' => $method,
            'Subscription' => $this->subscription->id
        ];

        $result = [
            'Result' =>  '',
            'Exception' => '',
            'ResultDescription' => '',
            'response' => [],
            'methodResult' =>  '',
            'TransactionIdReturned' => null
        ];

        if ($this->subscription->m7_provisioned) {
            if (!$this->pass) {
                return;
            }
        }

        if ($this->isPending) {
            return;
        }

        if ('CaptureSubscriber' == $method && !$this->subscription->m7_provisioned && !$this->validator->validate($this->relation->iban)) {
            $this->isValid = false;
            $this->messages[] = 'Invalid IBAN';
        }

        if (!$this->isValid) {
            if (!$this->subscription->m7_provisioned) {
                $jsonData = $this->subscription->json_data_m7;
                $json_data = [];
                if ($jsonData) {
                    $json_data = $jsonData->json_data;
                    $json_data['m7']['status'] = 'Failed';
                    $json_data['m7']['remarks'] = implode(', ', $this->messages);
                    $jsonData->json_data = $json_data;
                    $jsonData->save();
                } else {
                    $json_data['status'] = 'Failed';
                    $json_data['remarks'] = implode(', ', $this->messages);
                    $this->subscription->jsonDatas()->create([
                        'backend_api' => 'm7',
                        'json_data' => ['m7' => $json_data],
                        'tenant_id' => $this->subscription->relation->tenant_id
                    ]);
                }
            }
            $this->sendErrorReport($this->messages);
            return ['Result' => 'Error', 'error' => 1, 'msg' => 'Invalid', 'xml' =>  '', 'ResultDescription' => implode('. ', $this->messages)];
        }

        $rs = $this->m7GenerateRequest($method);

        if ('production' != config('app.env')) {
            $credentials['Xml'] = Str::replaceLast(config('m7.password'), 'xxxx', $rs);
            Logging::information(
                'M7 ' . $method,
                $credentials,
                16,
                1,
                $this->getTenantId(),
                'subscription',
                $this->subscription->id
            );
            return $this->processResponse($method, $credentials);
        }

        ini_set('memory_limit', '-1');

        $methodResult = [];

        $client = $this->clientConnect($method);
        $credentials['Xml'] = Str::replaceLast(config('m7.password'), 'xxxx', $rs);

        Logging::information(
            $method . ' pre',
            $credentials,
            16,
            0,
            $this->getTenantId(),
            'subscription',
            $this->subscription->id
        );

        try {
            $soapBody = new SoapVar($rs, XSD_ANYXML);

            if ('CaptureSubscriber' == $method) {
                $ret = $client->CaptureSubscriber($soapBody);
            } else {
                $ret = $client->ChangePackage($soapBody);
            }

            $array = json_decode(json_encode($ret), true);
            $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $client->__getLastRequest());

            $methodResult = $array[$method . 'Result'];

            $result = [
                'Result' =>  $array[$method . 'Result']['Result'],
                'CustomerDTOInfo' => $array[$method . 'Result']['CustomerDTOInfo'],
                'Exception' => $array[$method . 'Result']['Exception'],
                'ResultDescription' => $array[$method . 'Result']['ResultDescription'],
                'xml' => $requestXml,
                'array' => $array
            ];

            $credentials['Xml'] = $requestXml;
            $credentials['Result'] = $array;
            $logMsg = $array[$method . 'Result']['Result'];
            if ($methodResult['Result'] == 'SUCCESS') {
                Logging::information(
                    $logMsg,
                    $credentials,
                    16,
                    1,
                    $this->getTenantId(),
                    !is_null($this->subscription) ? 'subscription' : null,
                    $this->subscription->id ?? null
                );
            } else {
                Logging::error(
                    $logMsg,
                    $credentials,
                    16,
                    0,
                    $this->getTenantId(),
                    !is_null($this->subscription) ? 'subscription' : null,
                    $this->subscription->id ?? null
                );
                $this->sendErrorReport($logMsg);
            }
            $this->processProvisioning($methodResult, $credentials);
        } catch (\SoapFault $exception) {
            $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $rs);
            $result = [
                'error' => 1,
                'msg' => $exception->getMessage(),
                'xml' =>  $requestXml
            ];

            $credentials['Xml'] = $requestXml;
            $credentials['error_stacktrace'] = $exception->getTraceAsString();
            $logMsg = $exception->getMessage();
            Logging::error(
                $logMsg,
                $credentials,
                16,
                0,
                $this->getTenantId(),
                !is_null($this->subscription) ? 'subscription' : null,
                $this->subscription->id ?? null
            );
            $this->sendErrorReport($logMsg);
        } catch (\Exception $exception) {
            $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $rs);

            $credentials['Xml'] = $requestXml;
            $credentials['error_stacktrace'] = $exception->getTraceAsString();
            $logMsg = $exception->getMessage();
            $result = ['error' => 1, 'msg' => $logMsg, 'xml' =>  $requestXml];
            Logging::error(
                $logMsg,
                $credentials,
                16,
                0,
                $this->getTenantId(),
                !is_null($this->subscription) ? 'subscription' : null,
                $this->subscription->id ?? null
            );
            $this->sendErrorReport($logMsg);
        }

        return $result;
    }

    private function bankingInformation()
    {
        $bic = $this->relation ? $this->relation->bic : $this->getParam('bic');
        $iban = $this->relation ? $this->relation->iban : $this->getParam('iban');

        $bankingInformation = $this->generateXml('AccountName', $this->accountType);
        $bankingInformation .= $this->generateXml('BIC', $bic);
        $bankingInformation .= $this->generateXml('IBAN', $iban);
        return $this->generateXml('BankingInformation', $bankingInformation);
    }

    private function billingaddress()
    {
        $city_name = $this->billing ? $this->billing->city->name : $this->getParam('City');
        $country_name = $this->billing ? $this->billing->country_name : $this->getParam('Country');
        $house_number = $this->billing ? $this->billing->house_number : $this->getParam('HouseNumber');
        $house_number_suffix = $this->billing ? $this->billing->house_number_suffix : $this->getParam('HouseNumberExtension');
        $city_municipality = $this->billing ? $this->billing->city->municipality : $this->getParam('Municipality');
        $zipcode = $this->billing ? $this->billing->zipcode : $this->getParam('PostalCode');
        $state_name = $this->billing ? $this->billing->state_name : $this->getParam('State');
        $street = $this->billing ? $this->billing->street1 : $this->getParam('Street');
        $street2 = $this->billing ? $this->billing->street2 : $this->getParam('Street2');

        $billingaddress = $this->generateXml('City', $city_name);
        $billingaddress .= $this->generateXml('Country', strtoupper($country_name));
        $billingaddress .= $this->generateXml('HouseNumber', $house_number);
        $billingaddress .= $this->generateXml('HouseNumberExtension', $house_number_suffix);
        $billingaddress .= $this->generateXml('Municipality', $city_municipality);
        $billingaddress .= $this->generateXml('PostalCode', $zipcode);
        $billingaddress .= $this->generateXml('State', $state_name);

        if ($street2) {
            $street .= ', ' . $street2;
        }
        $billingaddress .= $this->generateXml('Street', $street);

        return $this->generateXml('BillingAddress', $billingaddress);
    }

    private function billingCustomerDetails()
    {
        $birthdate = $this->person ? $this->person->birthdate : $this->getParam('DateOfBirth');
        $dateOfBirth = $birthdate ? Carbon::parse($birthdate)->format('Y-m-d') : '';
        $email = $this->person ? $this->person->email : $this->getParam('Email');
        $first_name = $this->person ? $this->person->first_name : $this->getParam('FirstName');
        $gender = $this->person ? $this->person->gender : $this->getParam('Gender');
        if ($gender) {
            $gender = 'male' == strtolower($gender) ? 'M' : 'V';
        }

        $initials = $this->person ? $this->person->initials : $this->getParam('Initials');
        $last_name = $this->person ? $this->person->last_name : $this->getParam('SurName');
        $mobile = $this->person ? $this->person->mobile : $this->getParam('Mobile');
        $phone = $this->person ? $this->person->phone : $this->getParam('Phone');
        $middle_name = $this->person ? $this->person->middle_name : $this->getParam('MiddleName');
        $title = $this->person ? $this->person->title : $this->getParam('Title');

        $first_name = Str::substr(preg_replace('/\s+/', ' ', preg_replace("/[&<>]/", "", $first_name)), 0, 32);
        $middle_name = Str::substr(preg_replace('/\s+/', ' ', preg_replace("/[&<>]/", "", $middle_name)), 0, 32);
        $last_name = Str::substr(preg_replace('/\s+/', ' ', preg_replace("/[&<>]/", "", $last_name)), 0, 32);

        $billingCustomerDetails = $this->generateXml('DateOfBirth', $dateOfBirth);
        $billingCustomerDetails .= $this->generateXml('Email', $email);
        $billingCustomerDetails .= $this->generateXml('FirstName', $first_name);
        $billingCustomerDetails .= $this->generateXml('Gender', $gender);
        $billingCustomerDetails .= $this->generateXml('Initials', $initials);
        $billingCustomerDetails .= $this->generateXml('MiddleName', $middle_name);
        $billingCustomerDetails .= $this->generateXml('Mobile', $mobile);
        $billingCustomerDetails .= $this->generateXml('Phone', $phone);
        $billingCustomerDetails .= $this->generateXml('SurName', $last_name);
        $billingCustomerDetails .= $this->generateXml('Title', $title);
        return $this->generateXml('BillingCustomerDetails', $billingCustomerDetails);
    }

    private function generateSmartcardPackagesDTO($mainSmartcard, $smartcardNumber, $productInfos)
    {
        $smartcardPackagesDTOChild = $this->generateXml('DecoderNumber', $this->decodernumber);
        $smartcardPackagesDTOChild .= $this->generateXml('MainSmartcard', $mainSmartcard);
        $smartcardPackagesDTOChild .= $productInfos;
        $smartcardPackagesDTOChild .= $this->generateXml('SmartcardNumber', $this->formatSmartcard($smartcardNumber));

        return $this->generateXml('SmartcardPackagesDTO', $smartcardPackagesDTOChild);
    }

    private function generateProductInfo($productId, $addon)
    {
        $productInfoChild = $this->generateXml('CampaignCode', '');
        $productInfoChild .= $this->generateXml('IsAddOn', $addon);
        $productInfoChild .= $this->generateXml('Keywords', '');
        $productInfoChild .= $this->generateXml('ProductId', $productId);

        return $this->generateXml('ProductInfo', $productInfoChild);
    }

    private function generateMainProductInfo($line)
    {
        $productLines = $this->m7_product_lines;
        $productInfoList = '';
        foreach ($productLines as $product_line) {
            if ($product_line->is_stoped || $product_line->m7_deprovisioned || !$product_line->is_started) {
                continue;
            }
            $productInfoList .= $this->generateProductInfo($product_line->m7_product_id, 'addon' == $product_line->json_data_product_type ? 'true' : 'false');
        }

        return $this->generateXml('ProductInfo', $productInfoList);
    }

    private function smartcardPackages()
    {
        $smartcardPackagesDTO = '';
        $main_stb_line = $this->subscription->main_stb_line;
        $mac = $this->transaction != 'migration' ? $main_stb_line->serial_item->json_data['serial']['mac'] : $main_stb_line->mac_address;
        $smartcardNumber = $this->formatSmartcard($mac);
        $this->mainStbLine = ['line' => $main_stb_line, 'mac' => $smartcardNumber];
        $productInfos = $this->generateMainProductInfo($main_stb_line);
        $smartcardPackagesDTO .= $this->generateSmartcardPackagesDTO('', $smartcardNumber, $productInfos);

        return $this->generateXml('SmartcardPackages', $smartcardPackagesDTO);
    }

    private function authorization()
    {
        $authorization = $this->generateXml('Internal', 'false', 'm7s1');
        $authorization .= $this->generateXml('Password', config('m7.password'), 'm7s1');
        $authorization .= $this->generateXml('UserName', config('m7.username'), 'm7s1');

        return $this->generateXml('Authorization', $authorization);
    }

    private function defaultRequest()
    {
        $subscriptionStart = now()->format('Y-m-d');
        if ($this->subscription->subscription_start) {
            $subscriptionStart = Carbon::parse($this->subscription->subscription_start)->format('Y-m-d');
        }

        $defaultRequest = $this->rCompany();
        $contractNumber = $this->getParam('ContractNumber');
        if ('' == $contractNumber) {
            $contractNumber = $this->subscription->contract_number;
        }
        $defaultRequest .= $this->generateXml('ContractNumber', $contractNumber);
        $defaultRequest .= $this->generateXml('ContractPeriod', $this->contractPeriod);
        $defaultRequest .= $this->generateXml('ContractStartDate', $subscriptionStart);
        $defaultRequest .= $this->generateXml('CustomerNumber', $this->subscription->customer_number);
        $defaultRequest .= $this->generateXml('DealerNumber', $this->dealerNumber);
        $defaultRequest .= $this->generateXml('OptedForNewsletter', $this->optedForNewsletter);

        return $defaultRequest;
    }

    private function defaultRequestEnd()
    {
        $wishDate = $this->wishDate ? Carbon::parse($this->wishDate)->format('Y-m-d') : now()->format('Y-m-d');
        $defaultRequestEnd = $this->generateXml('TransactionType', $this->transactionType);
        $defaultRequestEnd .= $this->generateXml('WishDate', $wishDate);

        return $defaultRequestEnd;
    }

    private function rCompany()
    {
        return $this->generateXml('Company', config('m7.company'));
    }

    private function getParam($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : '';
    }

    private function rCustomerNumber()
    {
        $customerNumber = isset($this->params['CustomerNumber']) ? $this->params['CustomerNumber'] : $this->subscription->customer_number;
        return $this->generateXml('CustomerNumber', $customerNumber);
    }

    private function generateXml($key, $value, $ns = 'm7s')
    {
        return sprintf("<%s:%s>%s</%s:%s>", $ns, $key, $value, $ns, $key);
    }

    private function rDealerNumber()
    {
        return $this->generateXml('DealerNumber', $this->dealerNumber);
    }

    private function emptySmartcardPackage()
    {
        $body = '<m7s:SmartcardPackages>' .
            '<m7s:SmartcardPackagesDTO>' .
            '<m7s:DecoderNumber></m7s:DecoderNumber>' .
            '<m7s:MainSmartcard></m7s:MainSmartcard>' .
            '<m7s:ProductInfo>' .
            '<m7s:ProductInfo>' .
            '<m7s:CampaignCode></m7s:CampaignCode>' .
            '<m7s:IsAddOn>false</m7s:IsAddOn>' .
            '<m7s:Keywords></m7s:Keywords>' .
            '<m7s:ProductId></m7s:ProductId>' .
            '</m7s:ProductInfo>' .
            '</m7s:ProductInfo>' .
            '<m7s:SmartcardNumber></m7s:SmartcardNumber>' .
            '</m7s:SmartcardPackagesDTO>' .
            '</m7s:SmartcardPackages>';

        return $body;
    }

    public function stbXmlGenerator($method, $subscriptionLine)
    {
        $this->subscription = $subscriptionLine->subscription;
        $authorization = $this->authorization();
        $rs = "<tem:" . $method;
        $rs .= "    xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\"";
        $rs .= "    xmlns:tem=\"http://tempuri.org/\"";
        $rs .= "    xmlns:m7s=\"http://schemas.datacontract.org/2004/07/M7Service.Models\"";
        $rs .= "    xmlns:m7s1=\"http://schemas.datacontract.org/2004/07/M7Service.Security\">";
        $body = $authorization;
        $body .= $this->bankingInformation();
        $body .= $this->billingAddress();
        $body .= $this->billingCustomerDetails();
        $body .= $this->defaultRequest();

        $mac = $subscriptionLine->serial_item->json_data['serial']['mac'];

        $smartcardNumber = $this->formatSmartcard($mac);
        $jsonDataM7 = $subscriptionLine->product->jsonData->json_data['m7'];
        $productInfoList = $this->generateProductInfo($jsonDataM7['productId'], 'false');
        $productInfos = $this->generateXml('ProductInfo', $productInfoList);
        $smartcardPackagesDTO = $this->generateSmartcardPackagesDTO(
            $this->formatSmartcard($this->subscription->main_mac_address),
            $smartcardNumber,
            $productInfos
        );

        $body .= $this->generateXml('SmartcardPackages', $smartcardPackagesDTO);
        $body .= $this->defaultRequestEnd();

        $rs .= $this->generateXml('customer', $body, 'tem');
        $rs .= "</tem:" . $method . ">";

        return $rs;
    }

    public function processSingleProvision($subscriptionLine)
    {
        $method = 'CaptureSubscriber';
        $this->setSubscription($subscriptionLine->subscription);
        $credentials = [
            'Method' => $method,
            'Subscription' => $this->subscription->id
        ];
        $authorization = $this->authorization();
        $rs = "<tem:" . $method;
        $rs .= "    xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\"";
        $rs .= "    xmlns:tem=\"http://tempuri.org/\"";
        $rs .= "    xmlns:m7s=\"http://schemas.datacontract.org/2004/07/M7Service.Models\"";
        $rs .= "    xmlns:m7s1=\"http://schemas.datacontract.org/2004/07/M7Service.Security\">";
        $body = $authorization;
        $body .= $this->bankingInformation();
        $body .= $this->billingAddress();
        $body .= $this->billingCustomerDetails();
        $body .= $this->defaultRequest();

        $mac = $subscriptionLine->serial_item->json_data['serial']['mac'];

        $smartcardNumber = $this->formatSmartcard($mac);
        $jsonDataM7 = $subscriptionLine->product->jsonData->json_data['m7'];
        $productInfoList = $this->generateProductInfo($jsonDataM7['productId'], 'false');
        $productInfos = $this->generateXml('ProductInfo', $productInfoList);
        $smartcardPackagesDTO = $this->generateSmartcardPackagesDTO(
            $this->formatSmartcard($this->subscription->main_mac_address),
            $smartcardNumber,
            $productInfos
        );

        $body .= $this->generateXml('SmartcardPackages', $smartcardPackagesDTO);
        $body .= $this->defaultRequestEnd();

        $rs .= $this->generateXml('customer', $body, 'tem');
        $rs .= "</tem:" . $method . ">";

        $main_stb_line = $this->subscription->main_stb_line;
        $this->mainStbLine['line'] = $main_stb_line;
        $this->mainStbLine['mac'] = $main_stb_line->mac_address;
        $this->stbs[] = $mac;

        $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $rs);
        $credentials['Xml'] = $requestXml;

        if ('production' != config('app.env')) {
            $methodResult = [
                'Result' => 'SUCCESS',
                'Exception' => null,
                'CustomerDTOInfo' => null,
                'ResultDescription' => null,
                'TransactionIdReturned' => uniqid()
            ];
            if ($methodResult['Result'] == 'SUCCESS') {
                Logging::information(
                    Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                    $credentials,
                    16,
                    1,
                    $this->getTenantId(),
                    'subscription',
                    $this->subscription->id
                );
            } else {
                Logging::error(
                    Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                    $credentials,
                    16,
                    0,
                    $this->getTenantId(),
                    'subscription',
                    $this->subscription->id
                );
            }
            $this->updateLine($subscriptionLine, $methodResult['TransactionIdReturned']);

            return $methodResult;
        }

        $client = $this->clientConnect($method);
        $soapBody = new SoapVar($rs, XSD_ANYXML);

        try {
            $soapBody = new SoapVar($rs, XSD_ANYXML);
            $ret = $client->CaptureSubscriber($soapBody);

            $array = json_decode(json_encode($ret), true);

            $methodResult = $array[$method . 'Result'];

            $result = [
                'Result' =>  $array[$method . 'Result']['Result'],
                'CustomerDTOInfo' => $array[$method . 'Result']['CustomerDTOInfo'],
                'Exception' => $array[$method . 'Result']['Exception'],
                'ResultDescription' => $array[$method . 'Result']['ResultDescription'],
                'xml' => $requestXml,
                'array' => $array
            ];

            $credentials['Result'] = $array;
            if ($methodResult['Result'] == 'SUCCESS') {
                Logging::information(
                    Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                    $credentials,
                    16,
                    1,
                    $this->getTenantId(),
                    'subscription',
                    $this->subscription->id
                );
            } else {
                Logging::error(
                    Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                    $credentials,
                    16,
                    0,
                    $this->getTenantId(),
                    !is_null($this->subscription) ? 'subscription' : null,
                    $this->subscription->id ?? null
                );
            }
            $this->updateLine($subscriptionLine, $methodResult['TransactionIdReturned']);
        } catch (\SoapFault $exception) {
            $result = [
                'error' => 1,
                'xml' =>  $requestXml
            ];
            $credentials['Xml'] = $requestXml;
            Logging::exceptionWithData(
                $exception,
                Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                $credentials,
                16,
                0,
                $this->getTenantId(),
                'subscription',
                $this->subscription->id
            );
        } catch (\Exception $exception) {
            $credentials['Xml'] = $requestXml;
            $result = ['error' => 1, 'msg' => $exception->getMessage(), 'xml' =>  $requestXml];
            Logging::exceptionWithData(
                $exception,
                Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                $credentials,
                16,
                0,
                $this->getTenantId(),
                'subscription',
                $this->subscription->id
            );
        }

        return $result;
    }

    public function manualDeprovisionSmartcard($smartcardNumber, $customerNumber, $preview = true)
    {
        $method = 'CloseAccount';
        $authorization = $this->authorization();
        $rs = "<tem:" . $method;
        $rs .= "    xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\"";
        $rs .= "    xmlns:tem=\"http://tempuri.org/\"";
        $rs .= "    xmlns:m7s=\"http://schemas.datacontract.org/2004/07/M7Service.Models\"";
        $rs .= "    xmlns:m7s1=\"http://schemas.datacontract.org/2004/07/M7Service.Security\">";
        $body = $authorization;
        $body .= $this->rCompany();
        $body .= $this->generateXml('CustomerNumber', $customerNumber);
        $body .= $this->rDealerNumber();
        $body .= $this->generateXml('DecoderNumber', '');
        $body .= $this->generateXml('OldDecoderNumber', '');
        $body .= $this->generateXml('OldSmartcardNumber', '');
        $smartcardNumber = $this->formatSmartcard($smartcardNumber);

        $body .= $this->generateXml('SmartcardNumber', $smartcardNumber);
        $body .= $this->defaultRequestEnd();

        $rs .= $this->generateXml('transaction', $body, 'tem');
        $rs .= "</tem:$method>";

        if ($preview) {
            return $rs;
        }

        $client = $this->clientConnect($method);

        try {
            $actionHeader = new SoapHeader(
                'http://www.w3.org/2005/08/addressing',
                'Action',
                'http://tempuri.org/IM7Service/' . $method
            );
            $client->__setSoapHeaders($actionHeader);

            $soapBody = new SoapVar($rs, XSD_ANYXML);
            $ret = $client->CloseAccount($soapBody);

            $array = json_decode(json_encode($ret), true);
            $methodResult = $array[$method . 'Result'];

            $result = [
                'Result' =>  $array[$method . 'Result']['Result'],
                'CustomerDTOInfo' => $array[$method . 'Result']['CustomerDTOInfo'],
                'Exception' => $array[$method . 'Result']['Exception'],
                'ResultDescription' => $array[$method . 'Result']['ResultDescription'],
                'response' => $array,
                'methodResult' =>  $methodResult,
                'TransactionIdReturned' => isset($methodResult['TransactionIdReturned']) ? $methodResult['TransactionIdReturned'] : null
            ];
            $requestXml = Str::replaceLast(config('m7.password'), 'xxxx', $client->__getLastRequest());
            $credentials['Xml'] = $requestXml;
            $credentials['Result'] = $array;

            if ($methodResult['Result'] == 'SUCCESS') {
                Logging::information(
                    Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                    $credentials,
                    16,
                    1,
                    $this->getTenantId(),
                    'subscription',
                    $this->subscription->id
                );
            } else {
                Logging::error(
                    Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                    $credentials,
                    16,
                    0,
                    $this->getTenantId(),
                    !is_null($this->subscription) ? 'subscription' : null,
                    $this->subscription->id ?? null
                );
            }
        } catch (\SoapFault $exception) {
            $result = [
                'error' => 1,
                'msg' => $exception->getMessage()
            ];

            $credentials['error_stacktrace'] = $exception->getTraceAsString();
            Logging::error(
                Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                $credentials,
                16,
                0,
                $this->getTenantId(),
                !is_null($this->subscription) ? 'subscription' : null,
                $this->subscription->id ?? null
            );
        } catch (\Exception $exception) {
            if ($this->subscription) {
                $result = [
                    'error' => 2,
                    'msg' => $exception->getMessage()
                ];

                $credentials['error_stacktrace'] = $exception->getTraceAsString();
                Logging::error(
                    Str::substr($method . ' ' . $methodResult['Result'], 0, 190),
                    $credentials,
                    16,
                    0,
                    $this->getTenantId(),
                    !is_null($this->subscription) ? 'subscription' : null,
                    $this->subscription->id ?? null
                );
            }
        }

        return $result;
    }

    public function m7GenerateRequest($method)
    {
        $authorization = $this->authorization();
        $rs = "<tem:$method";
        $rs .= "    xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\"";
        $rs .= "    xmlns:tem=\"http://tempuri.org/\"";
        $rs .= "    xmlns:m7s=\"http://schemas.datacontract.org/2004/07/M7Service.Models\"";
        $rs .= "    xmlns:m7s1=\"http://schemas.datacontract.org/2004/07/M7Service.Security\">";

        switch ($method) {
            case 'CaptureSubscriber':
            case 'ChangePackage':
                $body = $authorization;
                $body .= $this->bankingInformation();
                $body .= $this->billingAddress();
                $body .= $this->billingCustomerDetails();
                $body .= $this->defaultRequest();
                $body .= $this->smartcardPackages();
                $body .= $this->defaultRequestEnd();

                $rs .= $this->generateXml('customer', $body, 'tem');
                break;

            case 'ChangeAddress':
                $body = $authorization;
                $body .= $this->billingAddress();
                $body .= $this->billingCustomerDetails();
                $body .= $this->rCompany();
                $contractNumber = $this->getParam('ContractNumber');
                if ('' == $contractNumber) {
                    $contractNumber = $this->subscription->contract_number;
                }
                $body .= $this->generateXml('ContractNumber', $contractNumber);
                $body .= $this->generateXml('ContractPeriod', $this->contractPeriod);
                $body .= $this->generateXml('ContractStartDate', now()->format('Y-m-d'));
                $body .= $this->rCustomerNumber();
                $body .= $this->rDealerNumber();
                $body .= $this->emptySmartcardPackage();
                $body .= $this->defaultRequestEnd();

                $rs .= $this->generateXml('customer', $body, 'tem');
                break;

            case 'SwopSmartcard':
                $smartcardNumber = $this->formatSmartcard($this->getParam('SmartcardNumber'));
                $body = $authorization;
                $body .= $this->rCompany();
                $body .= $this->rCustomerNumber();
                $body .= $this->rDealerNumber();
                $body .= $this->generateXml('DecoderNumber', $this->getParam('DecoderNumber'));
                $body .= $this->generateXml('OldDecoderNumber', $this->getParam('OldDecoderNumber'));
                $body .= $this->generateXml('OldSmartcardNumber', $this->getParam('OldSmartcardNumber'));
                $body .= $this->generateXml('SmartcardNumber', $smartcardNumber);
                $body .= $this->defaultRequestEnd();

                $rs .= $this->generateXml('transaction', $body, 'tem');
                break;

            case 'ReAuthSmartcard':
                $smartcardNumber = $this->formatSmartcard($this->getParam('SmartcardNumber'));
                $body = $authorization;
                $body .= $this->rCompany();
                $body .= $this->rCustomerNumber();
                $body .= $this->rDealerNumber();
                $body .= $this->generateXml('DecoderNumber', $this->getParam('DecoderNumber'));
                $body .= $this->generateXml('OldDecoderNumber', $this->getParam('OldDecoderNumber'));
                $body .= $this->generateXml('OldSmartcardNumber', $this->getParam('OldSmartcardNumber'));
                $body .= $this->generateXml('SmartcardNumber', $smartcardNumber);
                $body .= $this->defaultRequestEnd();

                $rs .= $this->generateXml('transaction', $body, 'tem');
                break;

            case 'CreateMyAccount':
            case 'RemoveMyAccount':
            case 'ChangeMyAccount':
                $body = $authorization;
                $body .= $this->rCompany();
                $body .= $this->generateXml('ConfirmPassword', $this->getParam('ConfirmPassword'));
                $body .= $this->rCustomerNumber();
                $body .= $this->rDealerNumber();
                $body .= $this->generateXml('Email', $this->getParam('Email'));
                $body .= $this->generateXml('NewPassword', $this->getParam('NewPassword'));
                $body .= $this->generateXml('OldPassword', $this->getParam('OldPassword'));
                $rs .= $this->generateXml('userInfo', $body, 'tem');
                break;

            case 'CloseAccount':
                $body = $authorization;
                $body .= $this->rCompany();
                $body .= $this->rCustomerNumber();
                $body .= $this->rDealerNumber();
                $body .= $this->generateXml('DecoderNumber', $this->getParam('DecoderNumber'));
                $body .= $this->generateXml('OldDecoderNumber', $this->getParam('OldDecoderNumber'));
                $body .= $this->generateXml('OldSmartcardNumber', $this->getParam('OldSmartcardNumber'));
                // [START] set $smartcardNumber
                $m7_has_provisioning = $this->subscription->m7_has_provisioning;
                $m7_has_deprovisioning = $this->subscription->m7_has_deprovisioning;
                /*
                $smartcardNumber = '';
                if ($m7_has_deprovisioning['hasDeProStb']) {
                    $m7_stb_lines = $this->subscription->m7_stb_lines;
                    foreach ($m7_stb_lines as $stb_line) {
                        if ($stb_line->is_stoped) {
                            if ($stb_line->mac_address != $this->subscription->main_mac_address) {
                                $smartcardNumber = $this->formatSmartcard($stb_line->mac_address);
                                break;
                            }
                        }
                    }
                }
                */
                $smartcardNumber = $this->formatSmartcard($this->getParam('SmartcardNumber'));
                if ($m7_has_deprovisioning['hasDeProProd']) {
                    $m7_product_lines = $this->subscription->m7_product_lines;
                    foreach ($m7_product_lines as $m7_product_line) {
                        if ($m7_product_line->is_stoped && 'basis' == $m7_product_line->json_data_product_type && !$m7_product_line->m7_deprovisioned) {
                            $smartcardNumber = '';
                            if ($m7_has_provisioning['hasNewBasis']) {
                                return false;
                            }
                            break;
                        }
                    }
                }

                $body .= $this->generateXml('SmartcardNumber', $smartcardNumber);
                // [END] set $smartcardNumber
                $body .= $this->defaultRequestEnd();

                $rs .= $this->generateXml('transaction', $body, 'tem');
                break;

            case 'Disconnect':
            case 'Reconnect':
            case 'ResetPin':
                $body = $authorization;
                $body .= $this->rCompany();
                $body .= $this->rCustomerNumber();
                $body .= $this->rDealerNumber();
                $body .= $this->generateXml('DecoderNumber', $this->getParam('DecoderNumber'));
                $body .= $this->generateXml('OldDecoderNumber', $this->getParam('OldDecoderNumber'));
                $body .= $this->generateXml('OldSmartcardNumber', $this->getParam('OldSmartcardNumber'));
                $body .= $this->generateXml('SmartcardNumber', $this->formatSmartcard($this->getParam('SmartcardNumber')));
                $body .= $this->defaultRequestEnd();

                $rs .= $this->generateXml('transaction', $body, 'tem');
                break;

            case 'SetLineProperties':
                $body = $authorization;
                $body .= $this->generateXml('ChannelListType', $this->getParam('ChannelListType'));
                $body .= $this->rCustomerNumber();
                $body .= $this->generateXml('KpnPackageID', $this->getParam('KpnPackageID'));
                $body .= $this->generateXml('LineMinDownload', $this->getParam('LineMinDownload'));
                $body .= $this->generateXml('LineProfile', $this->getParam('LineProfile'));
                $body .= $this->generateXml('LineType', $this->getParam('LineType'));

                $rs .= $this->generateXml('setLineProperties', $body, 'tem');
                break;

            case 'GetCustomerInfo':
                $body = $authorization;
                $body .= $this->rCompany();
                $body .= $this->rCustomerNumber();
                $rs .= $this->generateXml('customer', $body, 'tem');
                break;
        }

        $rs .= "</tem:" . $method . ">";

        return $rs;
    }

    public function updateLine($line, $transactionIdReturned)
    {
        if ('Provisioning' == $line->m7_provisioning_status && $line->is_started && !$line->is_stoped) {
            $lineJsonData = $line->json_data_m7;
            if ($lineJsonData) {
                $jsonData = $lineJsonData->json_data;

                $jsonDataM7 = $jsonData['m7'];
                $jsonDataM7['status'] = 'Provisioning';
                $jsonDataM7['provision_date'] = now();

                $transaction_ids = isset($jsonDataM7['transaction_id']) ? $jsonDataM7['transaction_id'] : [];

                if (!is_array($transaction_ids)) {
                    $transaction_ids = [
                        $transaction_ids
                    ];
                }

                if ($transactionIdReturned) {
                    $transaction_ids[] = $transactionIdReturned;
                }
                $jsonDataM7['transaction_id'] = $transaction_ids;
                $jsonData['m7'] = $jsonDataM7;

                $lineJsonData->json_data = $jsonData;
                $lineJsonData->save();
            } else {
                $jsonData = [
                    'backend_api' => 'm7',
                    'tenant_id' => $this->getTenantId(),
                    'subscription_id' => $this->subscription->id,
                    'json_data' => [
                        'm7' => [
                            'productId' => $line->m7_product_id,
                            'DecoderNumber' => '',
                            'MainSmartcard' => $this->subscription->main_stb_line->id == $line->id ? '' : $this->subscription->main_stb_line->mac_address,
                            'provision_date' => Carbon::now(),
                            'status' => 'Provisioning'
                        ]
                    ],
                    'transaction_id' => $transactionIdReturned
                ];

                if ('stb' == $line->json_data_product_type) {
                    $jsonData['json_data']['m7']['SmartcardNumber'] = $this->subscription->main_stb_line->mac_address;
                    if ($this->subscription->main_stb_line->id == $line->id) {
                        $jsonData['json_data']['m7']['type'] = 'main';
                    }
                }

                if ($transactionIdReturned) {
                    $jsonData['json_data']['m7']['transaction_id'] = [$transactionIdReturned];
                }
                $line->jsonDatas()->create($jsonData);
            }
        }

        if ('Provisioned' == $line->m7_provisioning_status && $line->is_stoped) {
            $jsonData = $line->json_data_m7;
            $json_data = $jsonData->json_data;

            $jsonDataM7 = $json_data['m7'];
            $jsonDataM7['status'] = 'Deprovisioning';
            $jsonDataM7['deprovisioning_date'] = now();

            $json_data['m7'] = $jsonDataM7;
            $jsonData->json_data = $json_data;
            $jsonData->transaction_id = $transactionIdReturned;
            $jsonData->save();
        }
    }

    public function processProvisioning($methodResult, $credentials)
    {
        $subscription = $this->subscription;
        if ($this->transaction == 'migration') {
            $warehouse = $this->subscription
                ->relation
                ->tenant
                ->warehouses()
                ->where('warehouse_location', $subscription->address_provisioning->id)
                ->first();

            if (!$warehouse) {
                $warehouse = $this->subscription
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

            $stbLine = $subscription->main_stb_line;

            $exists = $stbLine->product->serial()->where('warehouse_id', $warehouse->id)->where('serial', $this->stbs[0])->exists();

            if (!$exists) {
                $stbLine->product->serial()->create([
                    'warehouse_id' => $warehouse->id,
                    'serial' => $this->stbs[0],
                    'json_data' => [
                        'serial' => [
                            'mac' => $this->stbs[0],
                            'serial' => $this->stbs[0]
                        ]
                    ]
                ]);
            }

            $stbLine->serial = $this->stbs[0];
            $stbLine->save();
        }

        try {
            $jsonDataSubscription = $subscription->json_data_m7;
            if (!$subscription->m7_provisioned) {
                $json_data = [];
                if ($jsonDataSubscription) {
                    $json_data = $jsonDataSubscription->json_data;
                    $json_data['m7']['method'] = $this->method;
                    $json_data['m7']['status'] = 'Pending';
                    $json_data['m7']['remarks'] = $methodResult['Result'];

                    $transaction_ids = isset($json_data['m7']) && isset($json_data['m7']['transaction_id']) ?  $json_data['m7']['transaction_id'] : [];
                    $transaction_ids[] = $methodResult['TransactionIdReturned'];
                    $json_data['m7']['transaction_id'] = $transaction_ids;

                    $jsonDataSubscription->transaction_id = $methodResult['TransactionIdReturned'];
                    $jsonDataSubscription->json_data = $json_data;
                    $jsonDataSubscription->save();
                } else {
                    $json_data['method'] = $this->method;
                    $json_data['status'] = 'Pending';
                    $json_data['remarks'] = $methodResult['Result'];
                    $json_data['transaction_id'] = [$methodResult['TransactionIdReturned']];

                    $subscription->jsonDatas()->create([
                        'backend_api' => 'm7',
                        'tenant_id' => $subscription->relation->tenant_id,
                        'transaction_id' => $methodResult['TransactionIdReturned'],
                        'json_data' => ['m7' => $json_data]
                    ]);
                }
            }

            if ('ChangePackage' != $this->method) {
                $this->updateLine($this->subscription->main_stb_line, $methodResult['TransactionIdReturned']);
            }

            foreach ($this->m7_product_lines as $i => $packageLine) {
                $this->updateLine($packageLine, $methodResult['TransactionIdReturned']);
            }
        } catch (\Exception $exception) {
            Logging::exception(
                $exception,
                16,
                0,
                $this->getTenantId(),
                'subscription',
                $this->subscription->id
            );
        }
    }

    private function formatSmartcard($smartcardNumber)
    {
        return strtoupper(implode('', explode(':', $smartcardNumber)));
    }

    private function processResponse($method, $credentials)
    {
        $methodResult = [
            'Result' => 'SUCCESS',
            'Exception' => null,
            'CustomerDTOInfo' => null,
            'ResultDescription' => null,
            'TransactionIdReturned' => uniqid()
        ];

        if ('CloseAccount' == $method) {
            $this->processDeprovisioning($methodResult);
        }

        if (in_array($method, ['ChangePackage', 'CaptureSubscriber'])) {
            $this->processProvisioning($methodResult, $credentials);
        }

        return $methodResult;
    }

    public function processDeprovisioning($methodResult)
    {
        $m7_has_deprovisioning = $this->subscription->m7_has_deprovisioning;
        $m7_has_provisioning = $this->subscription->m7_has_provisioning;
        $mainStbLine = null;
        $basisLine = null;
        $deprovsnIds = [];
        if ($m7_has_deprovisioning['hasDeProStb']) {
            $m7_stb_lines = $this->m7_stb_lines;

            foreach ($m7_stb_lines as $stb_line) {
                $isMainStb = $stb_line->mac_address == $this->subscription->main_mac_address;
                if ($stb_line->is_stoped && !$stb_line->m7_deprovisioned) {
                    $deprovsnIds[] = $stb_line->id;
                    if ($isMainStb) {
                        $mainStbLine = $stb_line;
                        $subscriptionJsonData = $this->subscription->json_data_m7;
                        $jsonData = $subscriptionJsonData->json_data;

                        $jsonDataM7 = $jsonData['m7'];
                        $jsonDataM7['status'] = 'Deprovisioning';
                        $transaction_ids = isset($jsonDataM7['transaction_id']) ? $jsonDataM7['transaction_id'] : [];
                        if (gettype($transaction_ids) == 'string') {
                            $transaction_ids = [$transaction_ids];
                        }
                        $transaction_ids[] = $methodResult['TransactionIdReturned'];
                        $jsonDataM7['transaction_id'] = $transaction_ids;
                        $jsonData['m7'] = $jsonDataM7;

                        $subscriptionJsonData->json_data = $jsonData;
                        $subscriptionJsonData->transaction_id = $methodResult['TransactionIdReturned'];
                        $subscriptionJsonData->save();
                    }

                    $lineJsonData = $stb_line->json_data_m7;
                    if ($lineJsonData) {
                        $jsonData = $lineJsonData->json_data;

                        $jsonDataM7 = $jsonData['m7'];
                        $jsonDataM7['status'] = 'Deprovisioning';
                        $transaction_ids = isset($jsonDataM7['transaction_id']) ? $jsonDataM7['transaction_id'] : [];
                        $transaction_ids[] = $methodResult['TransactionIdReturned'];
                        $jsonDataM7['transaction_id'] = $transaction_ids;
                        $jsonData['m7'] = $jsonDataM7;

                        $lineJsonData->json_data = $jsonData;
                        $lineJsonData->transaction_id = $methodResult['TransactionIdReturned'];
                        $lineJsonData->save();
                    }
                }
            }
        }

        if ($m7_has_deprovisioning['hasDeProProd']) {
            $m7_product_lines = $this->m7_product_lines;
            foreach ($m7_product_lines as $m7_product_line) {
                if ($m7_product_line->is_stoped && !$m7_product_line->m7_deprovisioned) {
                    $deprovsnIds[] = $m7_product_line->id;
                    $lineJsonData = $m7_product_line->json_data_m7;
                    $jsonData = $lineJsonData->json_data;

                    $jsonDataM7 = $jsonData['m7'];
                    $jsonDataM7['status'] = 'Deprovisioning';
                    $transaction_ids = isset($jsonDataM7['transaction_id']) ? $jsonDataM7['transaction_id'] : [];
                    if (gettype($transaction_ids) == 'string') {
                        $transaction_ids = [$transaction_ids];
                    }
                    $transaction_ids[] = $methodResult['TransactionIdReturned'];
                    $jsonDataM7['transaction_id'] = $transaction_ids;
                    $jsonData['m7'] = $jsonDataM7;

                    $lineJsonData->json_data = $jsonData;
                    $lineJsonData->transaction_id = $methodResult['TransactionIdReturned'];
                    $lineJsonData->save();

                    $subscriptionJsonData = $this->subscription->json_data_m7;
                    $jsonData = $subscriptionJsonData->json_data;
                    $jsonDataM7 = $jsonData['m7'];

                    $isBasisLine = 'basis' == $m7_product_line->json_data_product_type;
                    if ($isBasisLine) {
                        $basisLine = $m7_product_line;
                    }

                    if ($isBasisLine && 'Deprovisioning' != $jsonDataM7['status']) {
                        $jsonDataM7['status'] = 'Deprovisioning';
                        $transaction_ids = isset($jsonDataM7['transaction_id']) ? $jsonDataM7['transaction_id'] : [];
                        $transaction_ids[] = $methodResult['TransactionIdReturned'];
                        $jsonDataM7['transaction_id'] = $transaction_ids;
                        $jsonData['m7'] = $jsonDataM7;

                        $subscriptionJsonData->json_data = $jsonData;
                        $subscriptionJsonData->transaction_id = $methodResult['TransactionIdReturned'];
                        $subscriptionJsonData->save();
                    }
                }
            }
        }

        if (($m7_has_deprovisioning['hasDeProBas'] && !$m7_has_provisioning['hasNewBasis']) || $mainStbLine) {
            $subscription_stop = $basisLine ? $basisLine->subscription_stop : $mainStbLine->subscription_stop;
            foreach ($this->subscription->provider_lines as $provider_line) {
                if ('Provisioned' == $provider_line->m7_provisioning_status) {
                    if (is_null($provider_line->subscription_stop)) {
                        $provider_line->subscription_stop = $subscription_stop;
                        $provider_line->save();
                    }
                    $deprovsnIds[] = $provider_line->id;
                    $lineJsonData = $provider_line->json_data_m7;
                    $jsonData = $lineJsonData->json_data;

                    $jsonDataM7 = $jsonData['m7'];
                    $jsonDataM7['status'] = 'Deprovisioning';
                    $transaction_ids = isset($jsonDataM7['transaction_id']) ? $jsonDataM7['transaction_id'] : [];
                    if (gettype($transaction_ids) == 'string') {
                        $transaction_ids = [$transaction_ids];
                    }
                    $transaction_ids[] = $methodResult['TransactionIdReturned'];
                    $jsonDataM7['transaction_id'] = $transaction_ids;
                    $jsonData['m7'] = $jsonDataM7;

                    $lineJsonData->json_data = $jsonData;
                    $lineJsonData->transaction_id = $methodResult['TransactionIdReturned'];
                    $lineJsonData->save();
                }
            }
        }
        $this->deprovisioningLog['subscription_line_ids'] = array_unique(array_filter($deprovsnIds));
        $this->deprovisioningLog['params'] = $this->parameters();
        $this->deprovisioningLog['m7_api_response'] = $methodResult;
        $this->deprovisioningLog['end_time'] = now()->format("Y-m-d H:i:s");

        Logging::information(
            'M7 Deprovisioning',
            $this->deprovisioningLog,
            16,
            1,
            $this->getTenantId(),
            'subscription',
            $this->subscription->id
        );
    }

    public function sendMail($subscription, $jsonData, $pass, $email, $fullname)
    {
        $json_data = $jsonData->json_data;
        $json_data['m7']['account'] = [
            'Email' => $email,
            'Password' => $pass
        ];
        $jsonData->json_data = $json_data;
        $jsonData->save();

        $relation = $subscription->relation;

        switch ($relation->tenant->id) {
            case 8:
                $slug = 'stipte';
                break;

            default:
                $slug = 'fiber';
                break;
        }

        $dTenant = $relation->tenant->name;

        $data = [
            "user_fullname" => $fullname,
            "email" => $email,
            "password" => $pass,
            "slug" => $slug,
            "tenant" => $dTenant
        ];

        $customer_email = $relation->customer_email;

        try {
            SendM7Mail::dispatchNow(
                $relation->tenant,
                $data,
                $customer_email,
                'CreateMyAccount'
            );
        } catch (\Exception $e) {
            Logging::exceptionWithData(
                $e,
                'Unable to send CreateAccount mail',
                [
                    'data' => $jsonData,
                ],
                2,
                1,
                $this->getTenantId(),
                'subscription',
                $this->subscription->id
            );
        }
    }

    public function createMyAccount($provider, $jsonData, $pass = null, $email = null)
    {
        $subscription = $jsonData->subscription;
        $this->subscription = $subscription;
        $person_provisioning = $subscription->person_provisioning;
        if (!$email) {
            $email = $person_provisioning->email;
        }
        $fullname = $person_provisioning->full_name;
        if (!$pass) {
            $pass = uniqid();
        }

        $json_data = $jsonData->json_data;
        $json_dataM7 = $json_data['m7'];

        $dParams = [
            'ConfirmPassword' => $pass,
            'CustomerNumber' => $json_dataM7['CustomerNumber'],
            'NewPassword' => $pass,
            'Email' => $email
        ];
        $this->setParams($dParams);

        $response = $this->sendRequest('CreateMyAccount');

        if ($response['Result'] == 'FATAL' && Str::contains($response['ResultDescription'], 'Account already exists')) {
            $response = $this->sendRequest('ChangeMyAccount');
        }

        if ($response['Result'] == 'SUCCESS') {
            if ('local' != config('app.env')) {
                $this->sendMail($subscription, $jsonData, $pass, $email, $fullname);
            }
        }

        return $response;
    }

    public function getCustomerInfo($customerNumber)
    {
        $params = [
            'CustomerNumber' => $customerNumber
        ];

        $this->setParams($params);

        $method = 'GetCustomerInfo';
        $client = $this->clientConnect($method);

        $this->xmlRequest = $this->m7GenerateRequest($method);
        if (!$this->xmlRequest) {
            $result['Result'] = 'Error';
            $result['Exception'] = 'Not valid for CloseAccount';
            return $result;
        }

        $actionHeader = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            'http://tempuri.org/IM7Service/' . $method
        );
        $client->__setSoapHeaders($actionHeader);

        $soapBody = new SoapVar($this->xmlRequest, XSD_ANYXML);

        $ret = $client->GetCustomerInfo($soapBody);

        return $ret;
    }
}
