<?php

namespace App\Services;

use Logging;
use App\Mail\Layer23ErrorMail;
use App\Repositories\JsonDataRepository;
use App\Models\SubscriptionLineMeta;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;

class Layer23Service
{
    const CONNECTION = 'connection';
    const CARRIER = "Breedband Buitengebied Rucphen BBR";
    const ERROR_RECIPIENT = 'marisa.vanvelzen@xsprovider.nl';

    protected $jsonDataRepository;
    protected $body = [];
    protected $auth = '';
    protected $base_url = '';
    protected $headers = [];
    protected $subscription;
    protected $subscriptionLine;
    protected $subscriptionLineService;
    protected $statusService;
    protected $profiles;

    public function __construct($subscription = null, $subscriptionLine = null)
    {
        if ($subscription) {
            $this->subscription = $subscription;
        }
        if ($subscriptionLine) {
            $this->subscriptionLine = $subscriptionLine;
        }

        $this->subscriptionLineService = new SubscriptionLineService();
        $this->statusService = new StatusService();
        $this->jsonDataRepository = new JsonDataRepository();
        $this->base_url = config('layer23.url');
        $this->auth = config('layer23.auth');
        $this->profiles = config('layer23.profilesOrder');
        $this->headers = [
            'Auth' => $this->auth,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }

    public function setSubscriptionLine($subscriptionLine)
    {
        $this->subscriptionLine = $subscriptionLine;
    }

    private function getLocationDescription($zipcode, $housenumber)
    {
        return strtoupper($zipcode) . $housenumber;
    }

    public function createEosAddress($zip, $house, $houseext = "", $locationDestBool = false)
    {
        $address = [];
        $missingVariable = false;

        if ($zip != null) {
            $address["zipCode"] = strtoupper($zip);
        } else {
            $missingVariable = true;
        }
        if ($house != null) {
            $address["houseNumber"] = (int) $house;
        } else {
            $missingVariable = true;
        }

        if (!$missingVariable) {
            $address["houseNumberExt"] = strtoupper($houseext);
            if ($locationDestBool) {
                $address["locationDescription"] = $this->getLocationDescription($zip, $house);
            }
        } else {
            return $missingVariable; /* false :p */
        }
        return $address;
    }

    public function migrateOrder($zip, $house, $houseext = null, $wishDate)
    {
        $response = null;
        $migrationInfo = [
            "migrationInfo" => [
                "resellerID" => config('layer23.resellerID'),
                "wishDate" => $wishDate,
                "customerInfo" => [
                    "zipCode" => $zip,
                    "houseNumber" => $house,
                    "houseNumberExt" => $houseext ? $houseext : ""
                ]
            ],
            "products" => config('layer23.profilesMigration')
        ];

        $response = $this->layer23('eosmigration', $migrationInfo);

        return $response;
    }

    public function createOrder($firstname, $lastname, $email, $phone, $zip, $house, $houseext, $wishDate = null)
    {
        $response = null;
        $orderDetails = [
            "zipCode" => $zip,
            "houseNumber" => $house
        ];
        if ($houseext != null) {
            $orderDetails["houseNumberExt"] = $houseext;
        } else {
            $orderDetails["houseNumberExt"] = "";
        }
        if ($wishDate != null) {
            $orderDetails["wishDate"] = $wishDate;
        }

        $customerInfo = [
            "firstname" => $firstname,
            "lastname" => $lastname,
            "email" => $email,
            "phone" => $phone
        ];

        $orderDetails["customerInfo"] = $customerInfo;
        $orderDetails["profileList"] = $this->profiles;
        $orderInfo = ["orderInfo" => $orderDetails];
        $response = $this->layer23('eosorder', $orderInfo);


        return $response;
    }
    public function getLineInfo($zip, $house, $houseext, $orderId = 0)
    {
        $address = $this->createEosAddress($zip, $house, $houseext, true);
        $response = null;

        if ($address) {
            $addressInfo = ["addressInfo" => $address];
            $lineSelection = ["lineSelection" => $addressInfo];

            if ($orderId > 0) {
                $lineSelection["orderID"] = $orderId;
            }
            $response = $this->layer23('eoslineinfo', $lineSelection);
        }
        return $response;
    }

    public function lookingGlass($zip, $house, $houseext)
    {
        $address = $this->createEosAddress($zip, $house, $houseext, true);
        $response = null;

        if ($address) {
            $addressInfo = ["addressInfo" => $address];
            $response = $this->layer23('eoslookingglass', $addressInfo);
        }
        return $response;
    }

    public function getMigrationStatus($migrationId)
    {
        $order = ["migrationID" => $migrationId];
        $order["resellerID"] = config('layer23.resellerID');

        $response = null;

        if ($migrationId > 0) {
            $orderInfo = ["migrationInfo" => $order];
            $response = $this->layer23('eosmigrationstatus', $orderInfo);
        }
        return $response;
    }

    public function getOrderStatus($orderId)
    {
        $response = null;
        if ($orderId > 0) {
            $orderInfo = [
                "orderInfo" => [
                    "orderID" => $orderId
                ]
            ];
            $response = $this->layer23('eosorderstatus', $orderInfo);
        }
        return $response;
    }

    public function setCancelOrder($orderId)
    {
        $response = null;
        if ($orderId > 0) {
            $orderInfo = [
                "orderInfo" => [
                    "orderID" => $orderId
                ]
            ];
            $response = $this->layer23('eosordercancel', $orderInfo);
        }
        return $response;
    }

    public function getAvailability($zip, $house, $houseext, $ptype = 7)
    {
        $address = $this->createEosAddress($zip, $house, $houseext, true);
        $response = null;
        if ($address) {
            $addressInfo = [
                "addressInfo" => $address,
                "product_type" => $ptype
            ];
            $response = $this->layer23('eosavailabilitycheck', $addressInfo);
        }
        return $response;
    }

    private function getNewState($status, $result)
    {
        /* Define next internal state and update status of subscription_line according */
    }

    public function getMigrationOrderId($subscription, $subscriptionLine)
    {
        // check as well whether it is a migration order or not
        $jsonData = $this->jsonDataRepository
            ->getBy([
                'subscription_id' => $subscription->id,
                'subscription_line_id' => $subscriptionLine->id
            ])
            ->first();
        if (!$jsonData) {
            return false;
        } else {
            return $jsonData->json_data["migrationMessageAck"]["migrationID"];
        }
    }

    public function getOrderId($subscription, $subscriptionLine)
    {
        // check as well whether it is a migration order or not
        $jsonData = $this->jsonDataRepository
            ->getBy([
                'subscription_id' => $subscription->id,
                'subscription_line_id' => $subscriptionLine->id
            ])
            ->first();
        if (!$jsonData) {
            return false;
        } else {
            return $jsonData->json_data["orderMessageAck"]["orderID"];
        }
    }

    public function processProvisioning($subscription, $subscriptionLine)
    {
        $address = $subscription->getAttribute('address_provisioning');
        $person = $subscription->getAttribute('person_provisioning');

        // Check if address is on network
        // eosavailabilitycheck()
        $response = $rawResponse = $availabilityInfo = null;
        $response = $this->getAvailability(
            $address->zipcode,
            $address->house_number,
            $address->house_number_suffix
        );
        if ($response) {
            $rawResponse = json_decode($response->getBody());
            $availabilityInfo = $rawResponse->availabilityInfo;

            if ($availabilityInfo && $availabilityInfo->statusCode == 200) {
                $isBBRServiceAvailable = ($availabilityInfo->connectionCarrierID == 2 &&
                    $availabilityInfo->connectionCarrier == $this::CARRIER);

                if ($isBBRServiceAvailable) {
                    // eoslineinfo call
                    $response = $this->getLineInfo(
                        $address->zipcode,
                        $address->house_number,
                        $address->house_number_suffix
                    );
                    if ($response) {
                        $rawResponse = json_decode($response->getBody());
                        $lines = $rawResponse->lines;
                        if ($lines && $lines->statusCode == 200) {
                            $isLastOrderIdExisting = (!is_null($lines->EOSLineStatus) &&
                                !is_null($lines->EOSLineStatus->lastOrder) &&
                                $lines->EOSLineStatus->lastOrder->orderID != "");

                            // migration_tbc
                            if ($isLastOrderIdExisting) {
                                Logging::information(
                                    "Layer23 - eoslineinfo",
                                    [
                                        "current_provider" => $lines->EOSLineStatus->lastOrder,
                                        "response" => $lines
                                    ],
                                    19,
                                    1,
                                    $subscription->relation->tenant_id,
                                    'subscription',
                                    $subscription->id
                                );

                                // Update line status_id (migration_tbc)
                                $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'migration_tbc')]);
                            } else { // eosorder()
                                $response = $this->createOrder(
                                    $person->first_name,
                                    $person->last_name,
                                    $person->email,
                                    $person->mobile,
                                    $address->zipcode,
                                    $address->house_number,
                                    $address->house_numer_suffix,
                                    $subscription->wishDate
                                );

                                if ($response) {
                                    $rawResponse = json_decode($response->getBody());
                                    $orderMessageAck = $rawResponse->orderMessageAck;
                                    if ($orderMessageAck && $orderMessageAck->statusCode == 200) {
                                        $isOrderIdExisting = !is_null($orderMessageAck->orderID);
                                        if ($isOrderIdExisting) {
                                            // Save subscription_line_metas (eos_order_id)
                                            SubscriptionLineMeta::updateOrInsert(
                                                [
                                                    'key' => 'eos_order_id',
                                                    'subscription_line_id' => $subscriptionLine->id,
                                                ],
                                                [
                                                    'key' => 'eos_order_id',
                                                    'value' => $orderMessageAck->orderID,
                                                    'subscription_line_id' => $subscriptionLine->id,
                                                ]
                                            );

                                            // Update line status_id (order_pending)
                                            $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'order_pending')]);
                                        } else {
                                            $errorDetails = [
                                                "subscription_id" => $subscription->id,
                                                "subscription_line_id" => $subscriptionLine->id,
                                                "params" => [
                                                    "firstname" => $person->first_name,
                                                    "lastname" => $person->last_name,
                                                    "email" => $person->email,
                                                    "mobile" => $person->mobile,
                                                    "zipCode" => $address->zipcode,
                                                    "houseNumber" => $address->house_number,
                                                    "houseNumerExt" => $address->house_numer_suffix,
                                                    "wishDate" => $subscription->wishDate
                                                ],
                                                "statusCode" => $lines->statusCode,
                                                "statusMessage" => $lines->statusMessage
                                            ];

                                            Logging::information(
                                                "Layer23 - eosorder",
                                                $errorDetails,
                                                19,
                                                1,
                                                $subscription->relation->tenant_id,
                                                'subscription',
                                                $subscription->id
                                            );

                                            // Update line status_id (order_error)
                                            $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'order_error')]);

                                            // Send mail to Marisa noting about the error (error eosorder request)
                                            Mail::to($this::ERROR_RECIPIENT)
                                                ->queue((new Layer23ErrorMail(
                                                    [
                                                        "error_subject" => "eosorder() error",
                                                        "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                                                        "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                                                    ],
                                                    $subscription->relation->tenant_id
                                                )));
                                        }
                                    } else {
                                        $errorDetails = [
                                            "subscription_id" => $subscription->id,
                                            "subscription_line_id" => $subscriptionLine->id,
                                            "params" => [
                                                "firstname" => $person->first_name,
                                                "lastname" => $person->last_name,
                                                "email" => $person->email,
                                                "mobile" => $person->mobile,
                                                "zipCode" => $address->zipcode,
                                                "houseNumber" => $address->house_number,
                                                "houseNumerExt" => $address->house_numer_suffix,
                                                "wishDate" => $subscription->wishDate
                                            ],
                                            "statusCode" => $lines->statusCode,
                                            "statusMessage" => $lines->statusMessage
                                        ];

                                        Logging::information(
                                            "Layer23 - eosorder",
                                            $errorDetails,
                                            19,
                                            1,
                                            $subscription->relation->tenant_id,
                                            'subscription',
                                            $subscription->id
                                        );

                                        // Update line status_id (order_error)
                                        $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'order_error')]);

                                        // Send mail to Marisa noting about the error (error eosorder request)
                                        Mail::to($this::ERROR_RECIPIENT)
                                            ->queue((new Layer23ErrorMail(
                                                [
                                                    "error_subject" => "eosorder() error",
                                                    "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                                                    "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                                                ],
                                                $subscription->relation->tenant_id
                                            )));
                                    }
                                }
                            }
                        } else {
                            $errorDetails = [
                                "subscription_id" => $subscription->id,
                                "subscription_line_id" => $subscriptionLine->id,
                                "params" => [
                                    $address->zipcode,
                                    $address->house_number,
                                    $address->house_number_suffix
                                ],
                                "statusCode" => $lines->statusCode,
                                "statusMessage" => $lines->statusMessage,
                                "response" => $lines
                            ];
                            Logging::error(
                                "Layer23 - eoslineinfo error",
                                $errorDetails,
                                19,
                                0,
                                $subscription->relation->tenant_id,
                                'subscription',
                                $subscription->id
                            );

                            // Update line status_id (check_error)
                            $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'check_error')]);

                            // Send mail to Marisa noting about the error (eoslineinfo - error)
                            Mail::to($this::ERROR_RECIPIENT)
                                ->queue((new Layer23ErrorMail(
                                    [
                                        "error_subject" => "eoslineinfo() error",
                                        "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                                        "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                                    ],
                                    $subscription->relation->tenant_id
                                )));
                        }
                    }
                } else { // service not available
                    $errorDetails = [
                        "subscription_id" => $subscription->id,
                        "subscription_line_id" => $subscriptionLine->id,
                        "params" => [
                            $address->zipcode,
                            $address->house_number,
                            $address->house_number_suffix
                        ],
                        "statusCode" => $availabilityInfo->statusCode,
                        "statusMessage" => $availabilityInfo->statusMessage,
                        "response" => $availabilityInfo
                    ];

                    Logging::error(
                        "Layer23 - eosavailabilitycheck - BBR not available",
                        $errorDetails,
                        19,
                        0,
                        $subscription->relation->tenant_id,
                        'subscription',
                        $subscription->id
                    );

                    // Update line status_id (check_error)
                    $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'not_available')]);

                    // Send mail to Marisa noting about the error (BBR not available)
                    Mail::to($this::ERROR_RECIPIENT)
                        ->queue((new Layer23ErrorMail(
                            [
                                "error_subject" => "eosavailabilitycheck() / BBR not available",
                                "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                                "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                            ],
                            $subscription->relation->tenant_id
                        )));
                }
            } else { // error
                $errorDetails = [
                    "subscription_id" => $subscription->id,
                    "subscription_line_id" => $subscriptionLine->id,
                    "params" => [
                        $address->zipcode,
                        $address->house_number,
                        $address->house_number_suffix
                    ],
                    "statusCode" => $availabilityInfo->statusCode,
                    "statusMessage" => $availabilityInfo->statusMessage,
                    "response" => $availabilityInfo
                ];

                Logging::error(
                    "Layer23 - eosavailabilitycheck - error",
                    $errorDetails,
                    19,
                    0,
                    $subscription->relation->tenant_id,
                    'subscription',
                    $subscription->id
                );

                // Update line status_id (check_error)
                $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'check_error')]);

                // Send mail to Marisa noting about the error (eosavailabilitycheck error)
                Mail::to($this::ERROR_RECIPIENT)
                    ->queue((new Layer23ErrorMail(
                        [
                            "error_subject" => "eosavailabilitycheck() error",
                            "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                            "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                        ],
                        $subscription->relation->tenant_id
                    )));
            }
        }
    }

    public function processMigration($subscription, $subscriptionLine)
    {
        $address = $subscription->getAttribute('address_provisioning');
        $wishDate = null;
        if ($subscription->wish_date) {
            $wishDate = Carbon::parse($subscription->wish_date)->format('Y-m-d');
        }
        $response = $rawResponse = null;
        $response = $this->migrateOrder(
            $address->zipcode,
            $address->house_number,
            $address->house_number_suffix,
            $wishDate
        );

        if ($response) {
            $rawResponse = json_decode($response->getBody());
            $migrationMessageAck = $rawResponse->migrationMessageAck;
            $now = now()->format('Y-m-d H:i:s');

            if ($migrationMessageAck && $migrationMessageAck->statusCode == 200) {
                $isMigrationIdExisting = (!is_null($migrationMessageAck->migrationID));
                if ($isMigrationIdExisting) {
                    // Save subscription_line_metas (eos_migration_id)
                    SubscriptionLineMeta::updateOrInsert(
                        [
                            'key' => 'eos_migration_id',
                            'subscription_line_id' => $subscriptionLine->id
                        ],
                        [
                            'key' => 'eos_migration_id',
                            'value' => $migrationMessageAck->migrationID,
                            'subscription_line_id' => $subscriptionLine->id,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]
                    );

                    // Update line status_id (migration_pending)
                    $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'migration_pending')]);
                } else { //error (eosmigration)
                    $houseExt = $address->house_numer_suffix ? $address->house_numer_suffix : "";
                    $wishDate = $subscription->wish_date ? Carbon::parse($subscription->wish_date)->format('Y-m-d') : "";
                    $errorDetails = [
                        "subscription_id" => $subscription->id,
                        "subscription_line_id" => $subscriptionLine->id,
                        "params" => [
                            "migrationInfo" => [
                                "resellerID" => config('layer23.resellerID'),
                                "wishDate" => $wishDate,
                                "customerInfo" => [
                                    "zipCode" => $address->zipcode,
                                    "houseNumber" => $address->house_number,
                                    "houseNumberExt" => $houseExt
                                ]
                            ],
                            "products" => config('layer23.profilesMigration')
                        ],
                        "statusCode" => $migrationMessageAck->statusCode,
                        "statusMessage" => $migrationMessageAck->statusMessage,
                        "response" => $migrationMessageAck
                    ];
                    Logging::error(
                        "Layer23 - eosmigration",
                        $errorDetails,
                        19,
                        0,
                        $subscription->relation->tenant_id,
                        'subscription',
                        $subscription->id
                    );

                    // Update line status_id (migration_error)
                    $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'migration_error')]);

                    // Send mail to Marisa noting about the error (eosmigration)
                    Mail::to($this::ERROR_RECIPIENT)
                        ->queue((new Layer23ErrorMail(
                            [
                                "error_subject" => "eosmigration() error",
                                "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                                "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                            ],
                            $subscription->relation->tenant_id
                        )));
                }
            } else { //error
                $houseExt = $address->house_numer_suffix ? $address->house_numer_suffix : "";
                $wishDate = $subscription->wish_date ? Carbon::parse($subscription->wish_date)->format('Y-m-d') : "";
                $errorDetails = [
                    "subscription_id" => $subscription->id,
                    "subscription_line_id" => $subscriptionLine->id,
                    "params" => [
                        "migrationInfo" => [
                            "resellerID" => config('layer23.resellerID'),
                            "wishDate" => $wishDate,
                            "customerInfo" => [
                                "zipCode" => $address->zipcode,
                                "houseNumber" => $address->house_number,
                                "houseNumberExt" => $houseExt
                            ]
                        ],
                        "products" => config('layer23.profilesMigration')
                    ],
                    "statusCode" => $migrationMessageAck->statusCode,
                    "statusMessage" => $migrationMessageAck->statusMessage,
                    "response" => $migrationMessageAck
                ];
                Logging::error(
                    "Layer23 - eosmigration",
                    $errorDetails,
                    19,
                    0,
                    $subscription->relation->tenant_id,
                    'subscription',
                    $subscription->id
                );

                // Update line status_id (migration_error)
                $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'migration_error')]);

                // Send mail to Marisa noting about the error (eosmigration)
                Mail::to($this::ERROR_RECIPIENT)
                    ->queue((new Layer23ErrorMail(
                        [
                            "error_subject" => "eosmigration() error",
                            "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                            "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                        ],
                        $subscription->relation->tenant_id
                    )));
            }
        }
    }

    public function checkMigrationStatus($subscription, $subscriptionLine)
    {
        $address = $subscription->getAttribute('address_provisioning');
        $response = $rawResponse = null;
        $metaData = $subscriptionLine->subscriptionLineMeta()
            ->where('key', 'eos_migration_id')
            ->first();

        if ($metaData) {
            $response = $this->getMigrationStatus($metaData->value);

            if ($response) {
                $rawResponse = json_decode($response->getBody());
                $migrationMessageAck = $rawResponse->migrationMessageAck;

                if ($migrationMessageAck && $migrationMessageAck->statusCode == 200) {
                    $migrationStatus = $migrationMessageAck->migrationStatus;

                    $now = now()->format('Y-m-d H:i:s');
                    // Save subscription_line_metas (eos_migration_status)
                    SubscriptionLineMeta::updateOrInsert(
                        [
                            'key' => 'eos_migration_status',
                            'subscription_line_id' => $subscriptionLine->id
                        ],
                        [
                            'key' => 'eos_migration_status',
                            'value' => $migrationStatus,
                            'subscription_line_id' => $subscriptionLine->id,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]
                    );

                    // Save subscription_line_metas (eos_migration_date)
                    if (!is_null($migrationMessageAck->migrationDate)) {
                        SubscriptionLineMeta::updateOrInsert(
                            [
                                'key' => 'eos_migration_date',
                                'subscription_line_id' => $subscriptionLine->id
                            ],
                            [
                                'key' => 'eos_migration_date',
                                'value' => $migrationMessageAck->migrationDate,
                                'subscription_line_id' => $subscriptionLine->id,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                    }

                    // Save subscription_line_metas (eos_circuit_id)
                    if (property_exists($migrationMessageAck, 'CID')) {
                        SubscriptionLineMeta::updateOrInsert(
                            [
                                'key' => 'eos_circuit_id',
                                'subscription_line_id' => $subscriptionLine->id,
                            ],
                            [
                                'key' => 'eos_circuit_id',
                                'value' => $migrationMessageAck->CID,
                                'subscription_line_id' => $subscriptionLine->id,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                    }

                    $migrationStatusId = null;
                    switch ($migrationStatus) {
                        case 0: // new
                            $migrationStatusId = $this->statusService->getStatusId('connection', 'migration_pending');
                            break;

                        case 1: // in progress
                            $migrationStatusId = $this->statusService->getStatusId('connection', 'migration_pending');
                            if (property_exists($migrationMessageAck, 'orderID')) {
                                SubscriptionLineMeta::updateOrInsert(
                                    [
                                        'key' => 'eos_order_id',
                                        'subscription_line_id' => $subscriptionLine->id
                                    ],
                                    [
                                        'key' => 'eos_order_id',
                                        'value' => $migrationMessageAck->orderID,
                                        'subscription_line_id' => $subscriptionLine->id,
                                        'created_at' => $now,
                                        'updated_at' => $now
                                    ]
                                );
                            }
                            break;

                        case 2: // done
                            $migrationStatusId = $this->statusService->getStatusId('connection', 'active');
                            break;

                        case 7: // on-hold = cancelled?
                            $migrationStatusId = $this->statusService->getStatusId('connection', 'cancelled');
                            break;

                        case 8: // deleted
                            $migrationStatusId = $this->statusService->getStatusId('connection', 'error');
                            break;

                        case 3: // failed
                            $migrationStatusId = $this->statusService->getStatusId('connection', 'failed');
                            SubscriptionLineMeta::updateOrInsert(
                                [
                                    'key' => 'eos_failure_reason',
                                    'subscription_line_id' => $subscriptionLine->id
                                ],
                                [
                                    'key' => 'eos_failure_reason',
                                    'value' => $migrationMessageAck->status,
                                    'subscription_line_id' => $subscriptionLine->id,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ]
                            );
                            break;
                        case 9: // rejected
                            $migrationStatusId = $this->statusService->getStatusId('connection', 'rejected');
                            break;
                    }

                    // Update line status_id (migration_pending)
                    if ($migrationStatusId) {
                        $subscriptionLine->update(['status_id' => $migrationStatusId]);
                    }
                } else { //error
                    $errorDetails = [
                        "subscription_id" => $subscription->id,
                        "subscription_line_id" => $subscriptionLine->id,
                        "params" => [
                            "zipCode" => $address->zipcode,
                            "houseNumber" => $address->house_number,
                            "houseNumerExt" => $address->house_numer_suffix
                        ],
                        "statusCode" => $migrationMessageAck->statusCode,
                        "statusMessage" => $migrationMessageAck->statusMessage,
                        "response" => $migrationMessageAck
                    ];

                    Logging::error(
                        "Layer23 - eosmigrationstatus",
                        $errorDetails,
                        19,
                        0,
                        $subscription->relation->tenant_id,
                        'subscription',
                        $subscription->id
                    );

                    // Update line status_id (migration_error)
                    $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'migration_error')]);

                    // Send mail to Marisa noting about the error (eosmigrationstatus)
                    Mail::to($this::ERROR_RECIPIENT)
                        ->queue((new Layer23ErrorMail(
                            [
                                "error_subject" => "eosmigrationstatus() error",
                                "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                                "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                            ],
                            $subscription->relation->tenant_id
                        )));
                }
            }
        }
    }

    public function cancelOrderMigration($subscription, $subscriptionLine)
    {
        $response = $rawResponse = $paramId = null;
        $params = [];
        $orderMetaData = $subscriptionLine->subscriptionLineMeta()
            ->where('key', 'eos_order_id')
            ->first();
        $migrationMetaData = $subscriptionLine->subscriptionLineMeta()
            ->where('key', 'eos_migration_id')
            ->first();

        if ($orderMetaData && !$migrationMetaData) {
            $paramId = $orderMetaData->value;
            $params = [
                "orderId" => $orderMetaData->value,
            ];
        }

        if ($migrationMetaData && !$orderMetaData) {
            $paramId = $migrationMetaData->value;
            $params = [
                "migrationId" => $migrationMetaData->value,
            ];
        }

        if ($paramId) {
            $response = $this->setCancelOrder($paramId);
            $rawResponse = json_decode($response->getBody());

            $orderMessageAck = $rawResponse->orderMessageAck;
            if ($orderMessageAck && $orderMessageAck->statusCode == 200) {
                $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'cancelled')]);
            } else { //error
                $errorDetails = [
                    "subscription_id" => $subscription->id,
                    "subscription_line_id" => $subscriptionLine->id,
                    "params" => $params,
                    "statusCode" => $orderMessageAck->statusCode,
                    "statusMessage" => $orderMessageAck->statusMessage,
                    "response" => $orderMessageAck
                ];

                Logging::error(
                    "Layer23 - eosordercancel",
                    $errorDetails,
                    19,
                    0,
                    $subscription->relation->tenant_id,
                    'subscription',
                    $subscription->id
                );

                // Update line status_id (migration_error)
                $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'cancel_error')]);

                // Send mail to Marisa noting about the error (eosordercancel)
                Mail::to($this::ERROR_RECIPIENT)
                    ->queue((new Layer23ErrorMail(
                        [
                            "error_subject" => "eosordercancel() error",
                            "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                            "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                        ],
                        $subscription->relation->tenant_id
                    )));
            }
        }
    }

    public function checkOrderStatus($subscription, $subscriptionLine)
    {
        $response = $rawResponse = $orderId = null;
        $metaData = $subscriptionLine->subscriptionLineMeta()
            ->where('key', 'eos_order_id')
            ->first();

        if ($metaData) {
            $response = $this->getOrderStatus($metaData->value);

            if ($response) {
                $rawResponse = json_decode($response->getBody());
                $orderMessageAck = $rawResponse->orderMessageAck;
                if ($orderMessageAck && $orderMessageAck->statusCode == 200) {
                    $orderStatus = $orderMessageAck->orderStatus;
                    $now = now()->format('Y-m-d H:i:s');

                    // Save subscription_line_metas (eos_order_status)
                    SubscriptionLineMeta::updateOrInsert(
                        [
                            'key' => 'eos_order_status',
                            'subscription_line_id' => $subscriptionLine->id
                        ],
                        [
                            'key' => 'eos_order_status',
                            'value' => $orderStatus->status,
                            'subscription_line_id' => $subscriptionLine->id,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]
                    );

                    // Save subscription_line_metas (eos_planned_date)
                    if ($orderStatus->planned) {
                        SubscriptionLineMeta::updateOrInsert(
                            [
                                'key' => 'eos_planned_date',
                                'subscription_line_id' => $subscriptionLine->id
                            ],
                            [
                                'key' => 'eos_planned_date',
                                'value' => $orderStatus->planned,
                                'subscription_line_id' => $subscriptionLine->id,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                    }

                    // Save subscription_line_metas (eos_circuit_id)
                    if (!is_null($orderMessageAck->CID)) {
                        SubscriptionLineMeta::updateOrInsert(
                            [
                                'key' => 'eos_circuit_id',
                                'subscription_line_id' => $subscriptionLine->id
                            ],
                            [
                                'key' => 'eos_circuit_id',
                                'value' => $orderMessageAck->CID,
                                'subscription_line_id' => $subscriptionLine->id,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                    }

                    $orderStatusId = null;
                    $status = strtolower($orderStatus->status);
                    if ($status == 'new') {
                        $orderStatusId = $this->statusService->getStatusId('connection', 'order_pending');
                    } elseif ($status == 'inprogress') {
                        $orderStatusId = $this->statusService->getStatusId('connection', 'order_pending');
                    } elseif ($status == 'done') {
                        $orderStatusId = $this->statusService->getStatusId('connection', 'active');
                    } elseif ($status == 'failed') {
                        $orderStatusId = $this->statusService->getStatusId('connection', 'failed');
                        SubscriptionLineMeta::updateOrInsert(
                            [
                                'key' => 'eos_failure_reason',
                                'subscription_line_id' => $subscriptionLine->id
                            ],
                            [
                                'key' => 'eos_failure_reason',
                                'value' => $orderMessageAck->statusMessage,
                                'subscription_line_id' => $subscriptionLine->id,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                    } elseif ($status == 'deleted') {
                        $orderStatusId = $this->statusService->getStatusId('connection', 'error');
                    } elseif ($status == 'cancelled') {
                        $orderStatusId = $this->statusService->getStatusId('connection', 'cancelled');
                    }

                    // Update line status_id
                    if ($orderStatusId) {
                        $subscriptionLine->update(['status_id' => $orderStatusId]);
                    }
                } else { //error
                    $errorDetails = [
                        "subscription_id" => $subscription->id,
                        "subscription_line_id" => $subscriptionLine->id,
                        "eos_order_id" => $orderId,
                        "statusCode" => $orderMessageAck->statusCode,
                        "statusMessage" => $orderMessageAck->statusMessage,
                        "response" => $orderMessageAck
                    ];

                    Logging::error(
                        "Layer23 - eosorderstatus",
                        $errorDetails,
                        19,
                        0,
                        $subscription->relation->tenant_id,
                        'subscription',
                        $subscription->id
                    );

                    // Update line status_id (order_pending)
                    $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'order_pending')]);

                    // Send mail to Marisa noting about the error (eosorderstatus)
                    Mail::to($this::ERROR_RECIPIENT)
                        ->queue((new Layer23ErrorMail(
                            [
                                "error_subject" => "eosorderstatus() error",
                                "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                                "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                            ],
                            $subscription->relation->tenant_id
                        )));
                }
            }
        }
    }

    public function processDeprovisioning($subscription, $subscriptionLine)
    {
        $response = $rawResponse = null;
        $metaData = $subscriptionLine->subscriptionLineMeta()
            ->where('key', 'eos_order_id')
            ->first();

        if ($metaData) {
            $response = $this->setCancelOrder($metaData->value);
            $rawResponse = json_decode($response->getBody());

            $orderMessageAck = $rawResponse->orderMessageAck;
            if ($orderMessageAck && $orderMessageAck->statusCode == 200) {
                $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'terminated')]);
            } else { //error
                $errorDetails = [
                    "subscription_id" => $subscription->id,
                    "subscription_line_id" => $subscriptionLine->id,
                    "params" => [
                        "orderId" => $metaData->value
                    ],
                    "statusCode" => $orderMessageAck->statusCode,
                    "statusMessage" => $orderMessageAck->statusMessage,
                    "response" => $orderMessageAck
                ];

                Logging::error(
                    "Layer23 - eosordercancel (terminate)",
                    $errorDetails,
                    19,
                    0,
                    $subscription->relation->tenant_id,
                    'subscription',
                    $subscription->id
                );

                // Update line status_id (cancel_error)
                $subscriptionLine->update(['status_id' => $this->statusService->getStatusId('connection', 'cancel_error')]);

                // Send mail to Marisa noting about the error (cancel_error / termination)
                Mail::to($this::ERROR_RECIPIENT)
                    ->queue((new Layer23ErrorMail(
                        [
                            "error_subject" => "eosordercancel / termination error",
                            "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                            "subscription_url" =>  config('app.front_url') . "/#/subscriptions/$subscription->id/details",
                        ],
                        $subscription->relation->tenant_id
                    )));
            }
        }
    }

    public function layer23($cmd, $params = [])
    {
        $response = null;
        try {
            $client = new Client([
                'base_uri' => $this->base_url,
                'headers' => $this->headers,
            ]);

            $severity = 'debug';

            /*
            * TODO: decide in the end whether we need the case switch or the uri is
            * sufficient or not. case is easier for now
             * */
            switch ($cmd) {
                case 'eosavailabilitycheck':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
                case 'eosorderstatus':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
                case 'eosordercancel':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
                case 'eosorder':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
                case 'eosmigrationstatus':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
                case 'eosmigration':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
                case 'eoslineinfo':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
                case 'eoslookingglass':
                    $response = $client->post("$cmd/", ['json' => $params]);
                    break;
            }

            Logging::information(
                "Layer23 - " . $cmd,
                [
                    "subscription_id" => $this->subscription->id,
                    "subscription_line_id" => $this->subscriptionLine->id,
                    "params" => $params,
                    "response" => json_decode(json_encode(json_decode($response->getBody())), true)
                ],
                19,
                1,
                $this->subscription->relation->tenant_id,
                'subscription',
                $this->subscription->id
            );
            return $response;
        } catch (Exception $e) {
            $subscriptionUrl = $tenantId = '';
            $errorDetails = [
                'method' => $cmd,
                'error' => $e->getMessage(),
                'error_stacktrace' => $e->getTraceAsString(),
                "params" => $params,
            ];

            if ($this->subscription) {
                $subscriptionId = $this->subscription->id;
                $subscriptionUrl = config('app.front_url') . "/#/subscriptions/$subscriptionId/details";
                $errorDetails['subscription_id'] = $this->subscription->id;
                $tenantId = $this->subscription->relation->tenant_id;
            }

            // Update line status_id (order_error)
            if ($this->subscriptionLine) {
                $errorDetails['subscription_line_id'] = $this->subscriptionLine->id;

                $updatedStatusId = $this->statusService->getStatusId('connection', 'error');
                switch ($cmd) {
                    case 'eosavailabilitycheck':
                    case 'eoslineinfo':
                        $updatedStatusId = $this->statusService->getStatusId('connection', 'check_error');
                        break;

                    case 'eosorderstatus':
                    case 'eosorder':
                        $updatedStatusId = $this->statusService->getStatusId('connection', 'order_error');
                        break;

                    case 'eosordercancel':
                        $updatedStatusId = $this->statusService->getStatusId('connection', 'cancel_error');
                        break;

                    case 'eosmigrationstatus':
                    case 'eosmigration':
                        $updatedStatusId = $this->statusService->getStatusId('connection', 'migration_error');
                        break;

                    default:
                        $updatedStatusId = $this->statusService->getStatusId('connection', 'error');
                        break;
                }

                $this->subscriptionLine->update([
                    'status_id' => $updatedStatusId
                ]);
            }



            // Send mail to Marisa noting about the error (error eosorder request)
            Mail::to($this::ERROR_RECIPIENT)
                ->queue((new Layer23ErrorMail(
                    [
                        "error_subject" => "eos api error",
                        "error_details" => json_encode($errorDetails, JSON_PRETTY_PRINT),
                        "subscription_url" => $subscriptionUrl,
                    ],
                    $tenantId
                )));

            Logging::error(
                "layer23 - $cmd",
                [
                    'params' => $params,
                    'error' => $e->getMessage(),
                    'error_stacktrace' => $e->getTraceAsString()
                ],
                19,
                0,
                $this->subscription ? $this->subscription->relation->tenant_id : null,
                $this->subscription ? 'subscription' : null,
                $this->subscription ? $this->subscription->id : null,
            );
        }
    }
}
