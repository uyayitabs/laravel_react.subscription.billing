<?php

namespace App\Services;

use GuzzleHttp\Client;

class L2FiberService
{
    public function l2FiberGetToken()
    {
        $client = new Client([
            'base_uri' => config('l2fiber.url'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $response = $client->request(
            'POST',
            'Auth/Token/Teleplaza',
            ['body' => '"' . config('l2fiber.api_key') . '"']
        );

        return $response->getBody();
    }

    public function l2fiber($cmd, $params = [])
    {
        $token = $this->l2FiberGetToken();
        $client = new Client([
            'base_uri' => config('l2fiber.url'),
            'headers' => [
                'Authorization' => "Bearer " . str_replace('"', '', $token),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        switch ($cmd) {
            case 'addresses':
                $uri = 'Addresses?';
                $getParams = [];
                foreach ($params as $key => $value) {
                    if (!in_array($key, ['method'])) {
                        $getParams[] = "{$key}={$value}";
                    }
                }
                $uri .= join("&", $getParams);
                $response = $client->request('GET', $uri);
                break;

            case 'availability':
                $uri = 'Availability/';
                if (isset($params['postalCode']) && !empty($params['postalCode'])) {
                    $uri .= $params['postalCode'] . '/';
                }
                if (isset($params['streetNr']) && !empty($params['streetNr'])) {
                    $uri .= $params['streetNr'] . '/';
                }
                if (isset($params['streetNrAddition']) && !empty($params['streetNrAddition'])) {
                    $uri .= $params['streetNrAddition'] . '/';
                }
                if (isset($params['room']) && !empty($params['room'])) {
                    $uri .= $params['room'] . '/';
                }
                $response = $client->request('GET', $uri);
                break;

            case 'connection':
                if ($params['method'] == 'GET') {
                    $uri = "Addresses/{$params['addressPublicId']}/Connection";
                    $response = $client->request($params['method'], $uri);
                }
                if ($params['method'] == 'POST') {
                    $uri = 'Addresses/' . $params['addressPublicId'] . '/Connection';

                    $formBody = [
                        'customerId' => '',
                        'bandwidth' => '',
                        'hasIpTv' => '',
                        'hasCaTv' => '',
                        'option82Label' => '',
                        'requestedActivationDate' => '',
                        'terminationDate' => '',
                    ];

                    foreach ($formBody as $key => $value) {
                        if (isset($params[$key])) {
                            $formBody[$key] = $params[$key];
                        }
                    }

                    $response = $client->request(
                        $params['method'],
                        $uri,
                        ['body' => json_encode($formBody)]
                    );
                }
                break;

            case 'subscriptions':
                $response = $client->request('GET', 'Subscriptions');
                break;

            case 'subscribe':
                $response = $client->request(
                    'POST',
                    'Subscribe',
                    [
                        'form_params' => $params['form_params']
                    ]
                );
                break;

            case 'unsubscribe':
                $response = $client->request(
                    'POST',
                    'UnSubscribe',
                    [
                        'form_params' => $params['form_params']
                    ]
                );
                break;

            case 'ont':
                if ($params['action'] != 'DeviceTypes') {
                    $url = "Addresses/{$params['addressPublicId']}/Connection/Ont/{$params['action']}";

                    $formBody = [
                        "ontSerial" => "",
                        "ontDeviceType"  => ""
                    ];

                    foreach ($formBody as $key => $value) {
                        if (isset($params[$key])) {
                            $formBody[$key] = $params[$key];
                        }
                    }
                    $opt = ['body' => json_encode($formBody)];
                } else {
                    $url = 'Ont/Ont/' . $params['action'];
                    $opt = [];
                }

                $response = $client->request(
                    $params['method'],
                    $url,
                    $opt
                );
                break;
            case 'terminate':
                //\Log::error(json_encode($params));
                $uri = "Addresses/{$params['addressPublicId']}";
                $formBody = [];

                switch ($params['action']) {
                        // SWAP
                    case 0:
                        $uri .= "/Connection/Ont/Swap";
                        $oldOnt = [
                            'ontSerial' => isset($params['oldOntSerial']) ? $params['oldOntSerial'] : '',
                            'ontDeviceType' =>  isset($params['oldOntDeviceType']) ? $params['oldOntDeviceType'] : ''
                        ];
                        $newOnt = [
                            'ontSerial' => isset($params['newOntSerial']) ? $params['newOntSerial'] : '',
                            'ontDeviceType' =>  isset($params['newOntDeviceType']) ? $params['newOntDeviceType'] : ''
                        ];
                        $formBody = [
                            'oldOnt' => $oldOnt,
                            'newOnt' => $newOnt
                        ];
                        break;

                        // RETURN
                    case 1:
                        $uri .= "/Connection/Ont/Return";
                        $formBody = [
                            'ontSerial' => isset($params['ontSerial']) ? $params['ontSerial'] : '',
                            'ontDeviceType' =>  isset($params['ontSerial']) ? $params['ontSerial'] : ''
                        ];
                        break;

                        // RETURN DEFECT
                    case 2:
                        $uri .= "/Connection/Ont/ReturnDefect";
                        $formBody = [
                            'ontSerial' => isset($params['ontSerial']) ? $params['ontSerial'] : '',
                            'ontDeviceType' =>  isset($params['ontSerial']) ? $params['ontSerial'] : ''
                        ];
                        break;
                }

                $response = $client->request(
                    'POST',
                    $uri,
                    ['body' => json_encode($formBody)]
                );
                break;
        }

        return $response;
    }
}
