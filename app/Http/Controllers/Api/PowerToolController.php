<?php

namespace App\Http\Controllers\Api;

use App\Services\PowerToolService;

class PowerToolController extends BaseController
{
    protected $powerToolService;

    public function __construct()
    {
        $this->powerToolService = new PowerToolService();
    }

    /**
     * validate subscription
     */
    public function validateSubscription()
    {
        $validation = $this->powerToolService->validateSubscription();

        if ($validation == 21) {
            return $this->sendError('Subscription not found', [], 500);
        }
        if ($validation == 22) {
            return $this->sendError('Subscription has no JSON data', [], 500);
        }
        return $this->sendResponse([], 'Valid');
    }

    /**
     * Reset m7 json_data
     */
    public function resetM7()
    {
        $deleted = $this->powerToolService->resetM7();
        if ($deleted == 21) {
            return $this->sendError('Subscription not found', [], 500);
        }
        if ($deleted == 22) {
            return $this->sendError('Subscription has no JSON data', [], 500);
        }
        return $this->sendResponse([], 'JSON data was successfully removed');
    }

    /**
     * Deprovision entire service
     */
    public function closeAccount()
    {
        $closedAccount = $this->powerToolService->closeAccount();
        if (!isset($closedAccount['Result'])) {
            return $this->sendError('Error closing account:' . $closedAccount['msg'], [], 500);
        }
        if ($closedAccount['Result'] != 'SUCCESS') {
            return $this->sendError('Error closing account:' . $closedAccount['Exception'], [], 500);
        }
        return $this->sendResponse([], 'Account was successfully closed');
    }

    /**
     * Deprovision Smartcard
     */
    public function deprovisionStb()
    {
        $closedAccount = $this->powerToolService->closeAccount();
        if (!isset($closedAccount['Result'])) {
            return $this->sendError('Error deprovisioning settop box:' . $closedAccount['msg'], [], 500);
        }
        if ($closedAccount['Result'] != 'SUCCESS') {
            return $this->sendError('Error deprovisioning settop box:' . $closedAccount['Exception'], [], 500);
        }
        return $this->sendResponse([], 'Settop box was successfully deprovisioned');
    }

    /**
     * fix subscription
     */
    public function fixSubscription()
    {
        $result = $this->powerToolService->fixSubscription();
        if ($result == 21) {
            return $this->sendError('Can’t update subscription: Subscription not found', [], 500);
        }
        if ($result == 22) {
            return $this->sendError('Can’t update subscription: Subscription has no JSON data', [], 500);
        }
        if ($result == 31) {
            return $this->sendError('Can’t update subscription: Missing customer number', [], 500);
        }
        if ($result == 32) {
            return $this->sendError('Can’t update subscription: Missing email', [], 500);
        }
        if ($result == 33) {
            return $this->sendError('Can’t update subscription: Missing password', [], 500);
        }
        return $this->sendResponse([], 'The subscription’s data has been updated, so the subscription appears to be provisioned.');
    }
}
