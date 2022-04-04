<?php

namespace App\Services;

use Logging;
use App\Jobs\SendBrightBlueMail;
use SoapClient;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class BrightBlueService
{
    protected $login;
    protected $password;
    protected $params;
    protected $accountNumber;
    protected $location;
    protected $subscription;
    protected $subscriptionBrightblueJsonData;
    protected $subscriptionLine;
    protected $subscriptionLineJsonData;
    protected $logger;
    protected $subscriptionService;
    protected $api;

    public function __construct()
    {
        $this->location = config('brightblue.location');
        $this->login = config('brightblue.login');
        $this->password = config('brightblue.password');
        $this->api = Config::get("constants.backend_apis.brightblue");
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    private function orderInterface()
    {
        return new SoapClient(
            storage_path("app/private/wsdls/brightblue/order.wsdl"),
            [
                'location' => $this->location,
                'login' => $this->login,
                'password' => $this->password,
            ]
        );
    }

    public function sendRequest($method)
    {
        $logData = [];
        $severity = "";
        $type = 1; //1 = info 0 = error

        $result = [
            'result' =>  'failed',
            'response' => null
        ];

        $tenantId = null;
        if (!empty($this->subscription)) {
            $tenantId = $this->subscription->relation->tenant_id;
        }

        try {
            $updateDetails = $deleteDetails = false;
            $response = [];
            switch ($method) {
                case 'NewAccount':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $result = $this->manager("CreateAccount");
                    break;

                case 'NewActivationCode':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $result = $this->manager("NewActivationCode");
                    break;

                case 'CloseAccount':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $result = $this->manager("CloseAccount");
                    break;

                case 'DisconnectAccount':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $result = $this->manager("DisconnectAccount");
                    break;

                case 'ReconnectAccount':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $result = $this->manager("ReconnectAccount");
                    break;

                case 'CreateAccount':
                    $params = [
                        'description' => $this->params['description'],
                        'primaryUserName' => $this->params['primaryUserName'],
                        'primaryUserPin' => $this->params['primaryUserPin'],
                        'status' => 'active',
                        'product' => [
                            ['productCode' => 'base.fiber'],
                        ],
                    ];
                    $response = $this->orderInterface()->CreateAccount($params);
                    break;

                case 'GetAccounts':
                    $params = [
                        'productCode' => $this->params['productCode'],
                        'minimumAccountNumber' => $this->params['minimumAccountNumber']
                    ];
                    $response = $this->orderInterface()->GetAccounts($params);
                    break;

                case 'GetAccountDetails':
                    $params = [
                        'accountNumber' => $this->params['accountNumber']
                    ];
                    $response = $this->orderInterface()->GetAccountDetails($params);
                    break;

                case 'SetAccountDescription':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'description' => $this->params['description'],
                    ];
                    $response = $this->orderInterface()->SetAccountDescription($params);
                    $updateDetails = true;
                    break;

                case 'ActivateAccount':
                    $params = [
                        'accountNumber' => $this->params['accountNumber']
                    ];
                    $response = $this->orderInterface()->ActivateAccount($params);
                    $updateDetails = true;
                    break;

                case 'SuspendAccount':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $params = [
                        'accountNumber' => $this->params['accountNumber']
                    ];
                    $response = $this->orderInterface()->SuspendAccount($params);
                    break;

                case 'ResumeAccount':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $params = [
                        'accountNumber' => $this->params['accountNumber']
                    ];
                    $response = $this->orderInterface()->ResumeAccount($params);
                    break;

                case 'CancelAccount':
                    $updateDetails = false;
                    $deleteDetails = false;

                    $params = [
                        'accountNumber' => $this->params['accountNumber']
                    ];
                    $response = $this->orderInterface()->CancelAccount($params);
                    break;

                case 'SetUserName':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'userNumber' => $this->params['userNumber'],
                        'name' => $this->params['name'],
                    ];
                    $response = $this->orderInterface()->SetUserName($params);
                    $updateDetails = true;
                    break;

                case 'SetPin':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'pin' => $this->params['pin'],
                    ];
                    $response = $this->orderInterface()->SetPin($params);
                    $updateDetails = true;
                    break;


                case 'RemoveClient':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'clientNumber' => $this->params['clientNumber'],
                    ];
                    $response = $this->orderInterface()->RemoveClient($params);
                    $updateDetails = true;
                    break;

                case 'SetClientName':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'clientNumber' => $this->params['clientNumber'],
                        'clientName' => $this->params['clientName'],
                    ];
                    $response = $this->orderInterface()->SetClientName($params);
                    $updateDetails = true;
                    break;

                case 'GenerateActivationCode':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'confirmUserInterfaceName' => $this->params['confirmUserInterfaceName'],
                    ];
                    $response = $this->orderInterface()->GenerateActivationCode($params);
                    break;

                case 'GetActivationRequest':
                    $params = [
                        'activationCodeId' => $this->params['activationCodeId'],
                        'timeout' => $this->params['timeout'],
                    ];
                    $response = $this->orderInterface()->GetActivationRequest($params);
                    break;

                case 'ConfirmActivationRequest':
                    $params = [
                        'activationRequestId' => $this->params['activationRequestId']
                    ];
                    $response = $this->orderInterface()->ConfirmActivationRequest($params);
                    break;

                case 'DismissActivationRequest':
                    $params = [
                        'activationRequestId' => $this->params['activationCodeId']
                    ];
                    $response = $this->orderInterface()->DismissActivationRequest($params);
                    break;

                case 'GetUnconfirmedActivationCodes':
                    $params = [
                        'accountNumber' => $this->params['accountNumber']
                    ];
                    $response = $this->orderInterface()->GetUnconfirmedActivationCodes($params);
                    break;

                case 'GenerateUnconfirmedActivationCode':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'description' => $this->params['description'],
                        'maxUses' => $this->params['maxUses'],
                        'expiryDate' => Carbon::parse($this->params['expiryDate'])->format("Y-m-d H:i:s")
                    ];
                    $response = $this->orderInterface()->GenerateUnconfirmedActivationCode($params);
                    break;

                case 'CancelUnconfirmedActivationCode':
                    $params = [
                        'activationCodeId' => $this->params['accountNumber']
                    ];
                    $response = $this->orderInterface()->CancelUnconfirmedActivationCode($params);
                    break;

                case 'AddProduct':
                    $params = [
                        'activationCodeId' => $this->params['accountNumber'],
                        'productCode' => $this->params['productCode'],
                    ];
                    $response = $this->orderInterface()->AddProduct($params);
                    break;

                case 'GetPackages':
                    $params = [];
                    $response = $this->orderInterface()->GetPackages($params);
                    break;

                case 'CancelProduct':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'productCode' => $this->params['productCode'],
                    ];
                    $response = $this->orderInterface()->CancelProduct($params);
                    break;

                case 'SetMonthlyCreditLimit':
                    $params = [
                        'accountNumber' => $this->params['accountNumber'],
                        'endUserAmount' => $this->params['endUserAmount'],
                    ];
                    $response = $this->orderInterface()->SetMonthlyCreditLimit($params);
                    break;

                case 'GetTransactions':
                    $params = [
                        'pointer' => $this->params['pointer'],
                        'limit' => $this->params['limit'],
                    ];
                    $response = $this->orderInterface()->GetTransactions($params);
                    break;
            }

            $brightBlueSubscriptionJsonData = $this->subscriptionBrightblueJsonData;
            $jsonData = $this->subscriptionLineJsonData;

            // Update existing JsonData
            if ($updateDetails && !empty($jsonData)) {
                $tenant = $this->subscription->relation->tenant;
                $jsonDataValue = $jsonData->json_data;

                if (!empty($jsonDataValue)) {
                    // ----------------------------
                    // Update SubscriptionLine JsonData
                    // ----------------------------
                    $jsonDataTenantName = $jsonDataValue[$this->api][$tenant->slugged_name];
                    $currentAccountNumber = $jsonDataTenantName["accountNumber"];
                    $currentActivationCode = null;
                    if (
                        array_key_exists(
                            "activationCode",
                            $jsonDataTenantName
                        )
                    ) {
                        $currentActivationCode = $jsonDataTenantName["activationCode"];
                    }

                    $this->setParams(['accountNumber' => $currentAccountNumber]);
                    $accountDetailsRequest = $this->sendRequest("GetAccountDetails");
                    $accountDetailsRequest["response"]["activationCode"] = $currentActivationCode;

                    $updateData = [];
                    $updateData[$this->api][$tenant->slugged_name] = $accountDetailsRequest["response"];
                    $newJsonData = array_replace_recursive($this->subscriptionLineJsonData->json_data, $updateData);
                    $this->subscriptionLineJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    // ----------------------------
                }
            }

            // Set status to Deprovisioned
            if ($deleteDetails && !empty($jsonData)) {
                // ----------------------------
                // Update Subscription JsonData
                // ----------------------------
                $subscriptionNewData = [];
                $subscriptionNewData[$this->api][$tenant->slugged_name]["status"] = "cancelled";
                $subscriptionNewData[$this->api][$tenant->slugged_name]["provisioning"] = [
                    "status" => "Deprovisioned",
                    "remarks" => ""
                ];
                $newData = array_replace_recursive($brightBlueSubscriptionJsonData->json_data, $subscriptionNewData);
                $brightBlueSubscriptionJsonData->update([
                    'json_data' => $newData
                ]);
                // ----------------------------

                // ----------------------------
                // Update SubscriptionLine JsonData
                // ----------------------------
                $updateData = [
                    $this->api => [
                        $tenant->slugged_name => [
                            "status" => "cancelled",
                            "provisioning" => [
                                "status" => "Deprovisioned",
                                "remarks" => ""
                            ]
                        ]
                    ]
                ];
                $newJsonData = array_replace_recursive($this->subscriptionLineJsonData->json_data, $updateData);
                $this->subscriptionLineJsonData->update([
                    'json_data' => $newJsonData
                ]);
                $this->subscriptionLine->update([
                    'subscription_stop' => now()->format("Y-m-d")
                ]);
                // ----------------------------
            }

            $data = json_decode(json_encode($response), true);
            $result = [
                'result' =>  'success',
                'response' => $data
            ];

            $logData = $result;
            $logData['params'] = $this->params;
            Logging::information('BrightBlue SoapRequest', $logData, 18, 0, $tenantId, 'subscription', $this->subscription->id);
        } catch (SoapFault $exception) {
            $requestXml = $this->orderInterface()->__getLastRequest();
            $result = [
                'error' => 1,
                'msg' => $exception->getMessage() . $exception->getTraceAsString(),
                'xml' =>  $requestXml
            ];

            $logData = [
                'error_message' =>  $exception->getMessage(),
                'error_stacktrace' => $exception->getTraceAsString(),
                'params' => $this->params,
                'subscription' => $this->subscription
            ];
            Logging::exception($exception, 18, 0, $tenantId, 'subscription', $this->subscription->id);
        } catch (\Exception $exception) {
            $requestXml = $this->orderInterface()->__getLastRequest();
            $result = [
                'error' => 2,
                'msg' => $exception->getMessage() . $exception->getTraceAsString(),
                'xml' =>  $requestXml
            ];

            $logData = [
                'error_message' =>  $exception->getMessage(),
                'error_stacktrace' => $exception->getTraceAsString(),
                'params' => $this->params,
                'subscription' => $this->subscription
            ];
            Logging::exception($exception, 18, 0, $tenantId, 'subscription', $this->subscription->id);
        }

        return $result;
    }

    public function setSubscription($subscription)
    {
        $this->messages = [];
        $this->isValid = true;

        $this->subscription = $subscription;
        $this->messages['subscription'] = $subscription;

        if (!$this->subscription) {
            $this->isValid = false;
            $this->messages['error'] = 'Empty subscription';
        }

        $this->relation = $subscription->relation;
        if (!$this->relation) {
            $this->isValid = false;
            $this->messages['error'] = 'Empty relation';
        }

        $this->billing = $subscription->address_provisioning;
        if (!$this->billing) {
            $this->isValid = false;
            $this->messages['error'] = 'Empty billing';
        }

        $this->person = $subscription->person_provisioning;
        if (!$this->person) {
            $this->isValid = false;
            $this->messages['error'] = 'Empty person';
        }

        $this->subscriptionLines = $subscription->provider_lines;
        if (!$this->subscriptionLines) {
            $this->isValid = false;
            $this->messages['error'] = 'Empty subscription lines';
        }

        Logging::error(
            Config::get("constants.backend_apis.brightblue"),
            $this->messages,
            18,
            1,
            $subscription->relation->tenant_id,
            'subscription',
            $subscription->id
        );
    }

    public function setSubscriptionBrightblueJsonData($subscriptionBrightblueJsonData)
    {
        $this->subscriptionBrightblueJsonData = $subscriptionBrightblueJsonData;
    }

    public function setSubscriptionLine($subscriptionLine)
    {
        $this->subscriptionLine = $subscriptionLine;
    }

    public function setSubscriptionLineJsonData($subscriptionLineJsonData)
    {
        $this->subscriptionLineJsonData = $subscriptionLineJsonData;
    }

    public function manager($method)
    {
        ini_set('memory_limit', '-1');

        $result = [
            'result' =>  null,
            'response' => null,
        ];

        $tenant = $this->subscription->relation->tenant;

        $brightBlueSubscriptionJsonData = $this->subscriptionBrightblueJsonData;
        $jsonData = null;
        if (!empty($this->subscriptionLineJsonData)) {
            $jsonData = $this->subscriptionLineJsonData;
        }

        switch ($method) {
            case 'CreateAccount':
                $subscriptionJsonData = $jsonDataTenantName = null;
                if (!empty($brightBlueSubscriptionJsonData)) {
                    $subscriptionJsonData = $brightBlueSubscriptionJsonData;
                    $isBrightblueInJsonData = array_key_exists('brightblue', $subscriptionJsonData->json_data);
                    if (is_array($subscriptionJsonData->json_data) && $isBrightblueInJsonData) {
                        $jsonDataTenantName = $subscriptionJsonData->json_data['brightblue'][$tenant->slugged_name];
                    }
                }

                $provisionThis = empty($subscriptionJsonData) ||
                    (!empty($subscriptionJsonData)
                        && $jsonDataTenantName['provisioning']['status']
                        == "Deprovisioned");

                if ($provisionThis) {
                    if (is_null($subscriptionJsonData)) {
                        $brightBlueData = [];
                        $jsonDataTenantName["provisioning"] = [
                            "status" => "Pending",
                        ];

                        // Subscription jsonData
                        $subscriptionJsonData = $this->subscription->jsonDatas()->create([
                            'tenant_id' => $this->subscription->relation->tenant_id,
                            'relation_id' => $this->subscription->relation_id,
                            'backend_api' => $this->api,
                            'json_data' => $brightBlueData
                        ]);
                        $this->setSubscriptionBrightblueJsonData($subscriptionJsonData);
                    } else {
                        $newJsonData = $this->subscriptionBrightblueJsonData->json_data;
                        $newJsonData[$this->api][$tenant->slugged_name]["accountNumber"] = null;
                        $this->subscriptionBrightblueJsonData->update([
                            'json_data' => $newJsonData
                        ]);
                    }

                    // SubscriptionLine jsonData
                    $subscriptionLineJsonData = $this->subscriptionLine->jsonDatas()->create([
                        'backend_api' => $this->api,
                        'tenant_id' => $this->subscription->relation->tenant_id,
                        'relation_id' => $this->subscription->relation_id,
                        'transaction_id' => null,
                        'subscription_id' => $this->subscription->id,
                        'product_id' => $this->subscriptionLine->product_id,
                        'json_data' => [
                            $this->api => [
                                $tenant->slugged_name => [
                                    "provisioning" => [
                                        "status" => "Pending",
                                        "remarks" => ""
                                    ]
                                ]
                            ]
                        ]
                    ]);
                    $this->setSubscriptionLineJsonData($subscriptionLineJsonData);

                    $this->subscription->refresh();
                    $this->subscriptionLine->refresh();

                    $brightBlueSubscriptionJsonData = $this->subscriptionBrightblueJsonData;

                    // 1) CreateAccount
                    $this->setParams([
                        'description' => $this->params['description'],
                        'primaryUserName' => $this->params['primaryUserName'],
                        'primaryUserPin' => $this->params['primaryUserPin'],
                    ]);
                    $createAccountRequest = $this->sendRequest('CreateAccount');
                    $accountNumber = $createAccountRequest["response"]["accountNumber"];

                    // 2) GenerateUnconfirmedActivationCode
                    $activationCodeRequest = $this->requestNewActivationCode(
                        $this->params['primaryUserPin'], // temporary PIN
                        $accountNumber, //accountNumber
                        $this->params['primaryUserName'], //description
                        10, //maxUses
                        now()->addMonths(3)->format("Y-m-d H:i:s") //expiryDate
                    );

                    // 3) GetAccountDetails, then save to jsonData
                    $this->setParams(['accountNumber' => $accountNumber]);
                    $accountDetailsRequest = $this->sendRequest("GetAccountDetails");

                    // ----------------------------
                    // Update Subscription JsonData
                    // ----------------------------

                    $subscriptionNewData = [
                        $this->api => [
                            $tenant->slugged_name => [
                                "accountNumber" => $accountNumber,
                                "provisioning" => [
                                    "status" => "Provisioned",
                                    "remarks" => ""
                                ],
                                "status" => $accountDetailsRequest["response"]["status"]
                            ]
                        ]
                    ];
                    $newJsonData = array_replace_recursive(
                        (array) $this->subscriptionBrightblueJsonData->json_data,
                        $subscriptionNewData
                    );

                    $this->subscriptionBrightblueJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    // ----------------------------
                    $jsonDataValue = !empty($subscriptionLineJsonData) ? $subscriptionLineJsonData->json_data : null;

                    if (!empty($jsonDataValue)) {
                        // ----------------------------
                        // Update SubscriptionLine JsonData
                        // ----------------------------
                        $updateData = [];
                        $updateData = [
                            $this->api => [
                                $tenant->slugged_name => [
                                    "activationCode" => $activationCodeRequest["response"]
                                ]
                            ]
                        ];

                        $jsonDataValue[$this->api][$tenant->slugged_name]['provisioning']['status'] = "Provisioned";
                        $updateData[$this->api][$tenant->slugged_name] = $accountDetailsRequest["response"];
                        $newJsonData = array_replace_recursive($jsonDataValue, $updateData);
                        $this->subscriptionLineJsonData->update([
                            'json_data' => $newJsonData
                        ]);
                    }
                }
                break;

            case "NewActivationCode":
                $activationCodeRequest = $this->requestNewActivationCode(
                    1234, //rand(1000,9999)
                    $this->params["accountNumber"],
                    $this->params["description"],
                    $this->params["maxUses"],
                    $this->params["expiryDate"]
                );

                $this->setParams(['accountNumber' => $this->params["accountNumber"]]);
                $accountDetailsRequest = $this->sendRequest("GetAccountDetails");


                // ----------------------------
                // Update Subscription JsonData
                // ----------------------------
                $updateData = [
                    $this->api => [
                        $tenant->slugged_name => [
                            "status" => "active",
                            "provisioning" => [
                                "status" => "Provisioned",
                                "remarks" => ""
                            ]
                        ]
                    ]
                ];
                $newJsonData = array_replace_recursive($brightBlueSubscriptionJsonData->json_data, $updateData);
                $brightBlueSubscriptionJsonData->update([
                    'json_data' => $newJsonData
                ]);
                // ----------------------------

                // ----------------------------
                // Update SubscriptionLine JsonData
                // ----------------------------
                $updateData = [
                    $this->api => [
                        $tenant->slugged_name => [
                            "status" => "active",
                            "provisioning" => [
                                "status" => "Provisioned",
                                "remarks" => ""
                            ],
                            "activationCode" => $activationCodeRequest["response"]
                        ]
                    ]
                ];
                $newJsonData = array_replace_recursive($this->subscriptionLineJsonData->json_data, $updateData);
                $this->subscriptionLineJsonData->update([
                    'json_data' => $newJsonData
                ]);
                // ----------------------------

                break;

            case 'CloseAccount':
                // CancelAccount request
                $this->setParams(['accountNumber' => $this->params["accountNumber"]]);
                $cancelAccountRequest = $this->sendRequest('CancelAccount');

                if ($cancelAccountRequest["result"] === "success") {
                    // ----------------------------
                    // Update Subscription JsonData
                    // ----------------------------
                    $updateData = [
                        $this->api => [
                            $tenant->slugged_name => [
                                "status" => "cancelled",
                                "provisioning" => [
                                    "status" => "Deprovisioned",
                                    "remarks" => ""
                                ]
                            ]
                        ]
                    ];
                    $newJsonData = array_replace_recursive($brightBlueSubscriptionJsonData->json_data, $updateData);
                    $brightBlueSubscriptionJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    // ----------------------------

                    // ----------------------------
                    // Update SubscriptionLine JsonData
                    // ----------------------------
                    $updateData = [
                        $this->api => [
                            $tenant->slugged_name => [
                                "status" => "cancelled",
                                "provisioning" => [
                                    "status" => "Deprovisioned",
                                    "remarks" => ""
                                ]
                            ]
                        ]
                    ];
                    $newJsonData = array_replace_recursive($this->subscriptionLineJsonData->json_data, $updateData);
                    $this->subscriptionLineJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    $this->subscriptionLine->update([
                        'subscription_stop' => now()->format("Y-m-d")
                    ]);
                    // ----------------------------
                }
                break;

            case 'DisconnectAccount':
                // SuspendAccount request
                $this->setParams(['accountNumber' => $this->params["accountNumber"]]);
                $suspendAccountRequest = $this->sendRequest('SuspendAccount');

                if ($suspendAccountRequest["result"] === "success") {
                    // ----------------------------
                    // Update Subscription JsonData
                    // ----------------------------
                    $updateData = [
                        $this->api => [
                            $tenant->slugged_name => [
                                "status" => "suspended",
                                "provisioning" => [
                                    "status" => "Suspended",
                                    "remarks" => ""
                                ]
                            ]
                        ]
                    ];
                    $newJsonData = array_replace_recursive($brightBlueSubscriptionJsonData->json_data, $updateData);
                    $brightBlueSubscriptionJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    // ----------------------------

                    // ----------------------------
                    // Update SubscriptionLine JsonData
                    // ----------------------------
                    $updateData = [
                        $this->api => [
                            $tenant->slugged_name => [
                                "status" => "suspended",
                                "provisioning" => [
                                    "status" => "Suspended",
                                    "remarks" => ""
                                ]
                            ]
                        ]
                    ];
                    $newJsonData = array_replace_recursive($this->subscriptionLineJsonData->json_data, $updateData);
                    $this->subscriptionLineJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    // ----------------------------
                }
                break;

            case 'ReconnectAccount':
                // CancelAccount request
                $this->setParams(['accountNumber' => $this->params["accountNumber"]]);
                $resumeAccountRequest = $this->sendRequest('ResumeAccount');

                if ($resumeAccountRequest["result"] === "success") {
                    // ----------------------------
                    // Update Subscription JsonData
                    // ----------------------------
                    $updateData = [
                        $this->api => [
                            $tenant->slugged_name => [
                                "status" => "active",
                                "provisioning" => [
                                    "status" => "Provisioned",
                                    "remarks" => ""
                                ]
                            ]
                        ]
                    ];
                    $newJsonData = array_replace_recursive($brightBlueSubscriptionJsonData->json_data, $updateData);
                    $brightBlueSubscriptionJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    // ----------------------------

                    // ----------------------------
                    // Update SubscriptionLine JsonData
                    // ----------------------------
                    $updateData = [
                        $this->api => [
                            $tenant->slugged_name => [
                                "status" => "active",
                                "provisioning" => [
                                    "status" => "Provisioned",
                                    "remarks" => ""
                                ]
                            ]
                        ]
                    ];
                    $newJsonData = array_replace_recursive($this->subscriptionLineJsonData->json_data, $updateData);
                    $this->subscriptionLineJsonData->update([
                        'json_data' => $newJsonData
                    ]);
                    // ----------------------------
                }
                break;

            default:
                $this->sendRequest($method);
                break;
        };

        //$this->subscription->jsonDatas()->first()->refresh();
        $result['result'] = 'success';
        $result['response'] = $jsonData;

        return $result;
    }

    protected function requestNewActivationCode($temporaryPin, $accountNumber, $description, $maxUses, $expiryDate)
    {
        $this->setParams([
            'accountNumber' => $accountNumber,
            'description' => $description,
            'maxUses' => $maxUses,
            'expiryDate' => $expiryDate
        ]);
        $activationCodeRequest = $this->sendRequest("GenerateUnconfirmedActivationCode");

        // 2.1) Send email with generated code (for account activation)
        $sluggedTenantName = Str::slug($this->subscription->relation->tenant->slugged_name);
        if ($this->subscription->relation->tenant_id === 7) {
            $sluggedTenantName = "fiber";
        }

        $data = [
            "userFullname" => $this->subscription->person_provisioning->full_name,
            "accountNumber" => $accountNumber,
            "activationCode" => $activationCodeRequest["response"]["code"],
            "slug" => $sluggedTenantName,
            "temporaryPin" => $temporaryPin
        ];

        /** product_id specific email template */
        $emailTemplate = $this->subscription->relation->tenant
            ->emailTemplates()
            ->where('product_id', $this->subscriptionLine->product_id)
            ->where('type', 'brightblue.create_account')
            ->first();

        /** if no product_id specific emplate template found, use 'type' as only where condition */
        if (!$emailTemplate) {
            $emailTemplate = $this->subscription->relation->tenant
                ->emailTemplates()
                ->where('type', 'brightblue.create_account')
                ->first();
        }

        SendBrightBlueMail::dispatchNow(
            $this->subscription->relation->tenant,
            $data,
            $this->subscription->person_provisioning->email,
            null,
            false,
            $emailTemplate->id
        );
        return $activationCodeRequest;
    }
}
