<?php

namespace App\Services;

use Logging;
use App\Repositories\JsonDataRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\WebServiceLogRepository;
use Illuminate\Support\Str;
use App\Jobs\SendM7AdminReportMail;
use App\Models\Serial;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\Subscription;

class WebService
{
    protected $subscriptionRepository;
    protected $providerRepository;
    protected $webServiceLogRepository;
    protected $m7Service;

    public function __construct()
    {
        $this->jsonDataRepository = new JsonDataRepository();
        $this->providerRepository = new ProviderRepository();
        $this->webServiceLogRepository = new WebServiceLogRepository();
        $this->m7Service = new M7Service();
    }

    /*
    |--------------------------------------------------------------------------
    | Public Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Authenticates user/password, returning status of true with token, or throws SoapFault.
     *
     * @param string $user
     * @param string $password
     * @return array
     * @throws SoapFault
     */
    public function auth($provider, $token, $ip)
    {
        $this->webServiceLogRepository->create([
            'provider' => $provider,
            'token' => $token,
            'ip' => $ip,
            'req_data' => json_encode(request()->all())
        ]);

        if (!config('app.enable_whitelisting')) {
            $ip = null;
        }

        return $this->providerRepository->validateProvider($provider, $token, $ip);
    }

    // Provider $provider
    public function manager($provider)
    {
        $provider = strtolower($provider);
        $token = request()->bearerToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Missing Authorization: Bearer $token', 'state' => 401];
        }

        $ip = request()->ip();
        $authorize = $this->auth($provider, $token, $ip);

        if (!$authorize) {
            return ['success' => false, 'message' => 'Unauthorize', 'state' => 401];
        }

        $params = request()->all();

        switch ($provider) {
            case 'm7':
                if (isset($params['TransactionID'])) {
                    $this->updateTransactionStatus($provider, $params);
                } else {
                    return ['success' => false, 'message' => 'Missing TransactionID', 'state' => 500];
                }

                return ['success' => true, 'message' => '', 'state' => 200];
                break;
        }

        return ['success' => true, 'message' => '', 'state' => 200];
    }

    public function updateTransactionStatus($provider, $params)
    {
        $remarks = 'Success';
        if ('20' != $params['Result']) {
            switch ($params['Result']) {
                case '21':
                    $remarks = 'Customerinfo: Customer Products not found';
                    break;

                case '31':
                    $remarks = 'Captsub: Smartcard not found';
                    break;

                case '44':
                    $remarks = 'CaptSub: address not found';
                    break;

                case '72':
                    $remarks = 'CaptSub: invalid package name';
                    break;

                case '88':
                    $remarks = 'No input parameter found';
                    break;

                case '90':
                    $remarks = 'Address change: invalid new address';
                    break;

                case '91':
                    $remarks = 'ReconnectProduct: [smartcardnumber] does not exist';
                    break;

                case '99':
                    $remarks = 'Re-Auth failed';
                    break;

                case '189':
                    $remarks = 'NO SMARTCARDS FOUND FOR CUSTOMER';
                    break;

                case '200':
                    $remarks = 'Contract Number is Mandatory';
                    break;

                case '202':
                    $remarks = 'Captsub: Main Smartcard not registered';
                    break;

                case '205':
                    $remarks = 'Registration impossible, Maximum number of extra cards has been reached';
                    break;

                case '255':
                    $remarks = 'Various (internal) errors';
                    break;

                default:
                    $remarks = 'Unknown error ' . $params['Result'];
                    break;
            }
        }

        $tid = $params['TransactionID'];
        $model = $this->jsonDataRepository->getModel();
        $jsonDatas = $model::where('transaction_id', $tid)->get();

        if (!$jsonDatas) {
            $jsonDatas = $model::where('json_data->m7->transaction_id', 'like', "'%" . $tid . "%'")->get();
        }

        if (!$jsonDatas || count($jsonDatas) == 0) {
            return;
        }

        $subscription = $jsonDatas[0]->subscription;
        $newSubscription = 'Pending' == $subscription->m7_provisioning_status;

        $tenant_id = $subscription->relation->tenant_id;
        if ($params['Result'] == 20) {
            Logging::information(
                'UpdateTransactionStatus',
                [
                    'jsonDatas' => $jsonDatas,
                    'params' => $params
                ],
                16,
                0,
                $tenant_id,
                'subscription',
                $subscription->id
            );
        } else {
            Logging::error(
                'UpdateTransactionStatus',
                [
                    'jsonDatas' => $jsonDatas,
                    'params' => $params
                ],
                16,
                0,
                $tenant_id,
                'subscription',
                $subscription->id
            );
        }

        if (!empty($jsonDatas->toArray())) {
            foreach ($jsonDatas as $jsonData) {
                $json_data = $jsonData->json_data;
                $providerJsonData = $json_data[$provider];
                $status = 'Provisioned';
                $providerJsonDataStatus = isset($providerJsonData['status']) ? $providerJsonData['status'] : null;

                switch ($providerJsonDataStatus) {
                    case 'Deprovisioning':
                        $status = 'Deprovisioned';
                        $providerJsonData['deprovisioning_date'] = now();
                        break;

                    case 'Disconnecting':
                        $status = 'Disconnected';
                        $providerJsonData['disconnecting_date'] = now();
                        break;

                    case 'Reconnecting':
                        $providerJsonData['reconnecting_date'] = now();
                        break;

                    default:
                        $processes = isset($providerJsonData['Processes']) ? $providerJsonData['Processes'] : [];
                        $i = count($processes);
                        if ($i > 0) {
                            $processes[$i - 1]['status'] = $status;
                        }
                        $providerJsonData['Processes'] = $processes;
                        break;
                }

                $providerJsonData['remarks'] = $remarks;
                $providerJsonData['result'] = $params['Result'];

                $providerJsonData['status'] = '20' == $params['Result'] ? $status : 'Failed';

                if (
                    'Provisioned' == $providerJsonData['status'] &&
                    !$jsonData->subscription_line_id && '20' == $providerJsonData['result']
                ) {
                    $providerJsonData['CustomerNumber'] = $params['CustomerNumber'];
                    $providerJsonData['provisioning_date'] = now();
                }

                $json_data[$provider] = $providerJsonData;
                $jsonData->json_data = $json_data;
                $jsonData->save();

                /* NOTE:: Commented so no serial record is deleted after deprovisioning
                if ('Deprovisioned' == $providerJsonData['status']) {
                    if ($jsonData->subscription_line_id) {
                        $subscriptionLine = $jsonData->subscriptionLine;
                        if ('stb' == $subscriptionLine->json_data_product_type) {
                            $serial = $subscriptionLine->serial_item;
                            if ($serial) {
                                $warehouse = Warehouse::where('id', $serial->warehouse_id)->first();
                                Serial::where('serial', $serial->serial)->delete();

                                if ($warehouse) {
                                    $exists = Serial::where('warehouse_id', $warehouse->id)->exists();
                                    if (!$exists) {
                                        Stock::where('warehouse_id', $warehouse->id)->delete();
                                        $warehouse->delete();
                                    }
                                }
                            }
                            $subscriptionLine->serial = null;
                            $subscriptionLine->save();
                        }
                    }
                }
                */

                if ($newSubscription && '20' == $params['Result']) {
                    $stbLines = $subscription->m7_stb_lines;
                    foreach ($stbLines as $stbLine) {
                        $jsonDataM7 = $stbLine->json_data_m7;
                        if ($jsonDataM7 || !$stbLine->serial || $stbLine->is_stoped) {
                            continue;
                        }
                        $this->m7Service->processSingleProvision($stbLine);
                    }
                }

                if (
                    'Deprovisioned' == $providerJsonData['status'] &&
                    !$jsonData->subscription_line_id && '20' == $providerJsonData['result']
                ) {
                    $this->processDeprovisioning($jsonData->subscription, $providerJsonData['deprovisioning_date']);
                }
            }

            foreach ($jsonDatas as $jsonData) {
                $json_data = $jsonData->json_data;
                $json_dataM7 = $json_data['m7'];
                if (
                    'Provisioned' == $json_dataM7['status'] &&
                    !$jsonData->subscription_line_id && '20' == $json_dataM7['result']
                ) {
                    $this->m7Service->createMyAccount($provider, $jsonData);
                    break;
                }
            }
        }

        if ('20' != $params['Result']) {
            $this->sendErrorReport($remarks, $subscription->id, $params['Result']);
        }
    }

    private function sendErrorReport($message, $subscription_id, $result)
    {
        $subscription = Subscription::find($subscription_id);
        $link = Str::replaceArray('?', [Str::finish(config('app.front_url'), '/'), $subscription_id], '?#/subscriptions/?/details');
        $message = Str::replaceArray('?', [$message, $result, $link, $subscription_id], '? result code of ? for subscription <a href="?">?</a>');

        $admins_emails = config('m7.admins_email');
        try {
            SendM7AdminReportMail::dispatchNow(
                [
                    'message' => $message,
                    'job' => 'UpdatedTransactionStatus'
                ],
                $admins_emails
            );
        } catch (\Exception $e) {
            Logging::exceptionWithData(
                $e,
                'sendErrorReport',
                [
                    'subscription_id' => $subscription_id
                ],
                16,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
        }
    }

    private function processDeprovisioning($subscription, $deprovisioningDate)
    {
        $m7_stb_lines = $subscription->m7_stb_lines;
        $m7_product_lines = $subscription->m7_product_lines;

        foreach ($m7_stb_lines as $m7_stb_line) {
            if ('Provisioned' == $m7_stb_line->m7_provisioning_status) {
                if (is_null($m7_stb_line->subscription_stop)) {
                    $m7_stb_line->subscription_stop = $deprovisioningDate;
                    $m7_stb_line->save();
                }
                $jsonData = $m7_stb_line->json_data_m7;
                $lineJsonData = $jsonData->json_data;

                $lineJsonDataM7 = $lineJsonData['m7'];
                $lineJsonDataM7['deprovisioning_date'] = $deprovisioningDate;
                $lineJsonDataM7['status'] = 'Deprovisioned';

                $lineJsonData['m7'] = $lineJsonDataM7;
                $jsonData->json_data = $lineJsonData;
                $jsonData->save();
            }
        }

        foreach ($m7_product_lines as $m7_pack_line) {
            if ('Provisioned' == $m7_pack_line->m7_provisioning_status) {
                if (is_null($m7_pack_line->subscription_stop)) {
                    $m7_pack_line->subscription_stop = $deprovisioningDate;
                    $m7_pack_line->save();
                }
                $jsonData = $m7_pack_line->json_data_m7;
                $lineJsonData = $jsonData->json_data;

                $lineJsonDataM7 = $lineJsonData['m7'];
                $lineJsonDataM7['deprovisioning_date'] = $deprovisioningDate;
                $lineJsonDataM7['status'] = 'Deprovisioned';

                $lineJsonData['m7'] = $lineJsonDataM7;
                $jsonData->json_data = $lineJsonData;
                $jsonData->save();
            }
        }
    }
}
