<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\L2FiberService;

class L2FiberController extends Controller
{
    protected $service;

    /**
     * l2fiber api commands
     *
     * @return json
     */
    public function cmd(Request $req, $cmd, $action = null)
    {
        $params = [];
        $service = new L2FiberService();

        if ($cmd == 'ont') {
            $params['method'] = $req->method();
            $params['addressPublicId'] = $req->addressPublicId;

            if ($req->action) {
                $params['action'] = $req->action;
            }
            if ($req->ontSerial) {
                $params['ontSerial'] = $req->ontSerial;
            }
            if ($req->ontDeviceType) {
                $params['ontDeviceType'] = $req->ontDeviceType;
            }
        } elseif ($cmd == 'addresses') {
            $params['method'] = 'GET';
            if ($req->changedSince) {
                $params['changedSince'] = $req->changedSince;
            }
            if ($req->offset) {
                $params['offset'] = $req->offset;
            }
            if ($req->limit) {
                $params['limit'] = $req->limit;
            }
        } elseif ($cmd == 'availability') {
            $params['method'] = 'GET';
            if ($req->postalCode) {
                $params['postalCode'] = $req->postalCode;
            }
            if ($req->streetNr) {
                $params['streetNr'] = $req->streetNr;
            }
            if ($req->streetNrAddition) {
                $params['streetNrAddition'] = $req->streetNrAddition;
            }
            if ($req->room) {
                $params['room'] = $req->room;
            }
        } elseif ($cmd == 'connection') {
            $params['method'] = $req->method();
            $params['addressPublicId'] = $req->addressPublicId;
            if ($req->customerId) {
                $params['customerId'] = $req->customerId;
            }
            if ($req->bandwidth) {
                $params['bandwidth'] = $req->bandwidth;
            }
            if ($req->hasIpTv) {
                $params['hasIpTv'] = $req->hasIpTv;
            }
            if ($req->hasCaTv) {
                $params['hasCaTv'] = $req->hasCaTv;
            }
            if ($req->option82Label) {
                $params['option82Label'] = $req->option82Label;
            }
            if ($req->requestedActivationDate) {
                $params['requestedActivationDate'] = $req->requestedActivationDate;
            }
            if ($req->terminationDate) {
                $params['terminationDate'] = $req->terminationDate;
            }
        } elseif ($cmd == 'subscriptions') {
            $params['method'] = 'GET';
        } elseif ($cmd == 'subscribe' || $cmd == 'unsubscribe') {
            $params['method'] = 'POST';
            $params['form_params'] = ['callbackUrl' => $req->callbackUrl];
        } elseif ($cmd == 'terminate') {
            if ($req->action) {
                $params['action'] = $req->action;
            }
            if ($req->addressPublicId) {
                $params['addressPublicId'] = $req->addressPublicId;
            }
            if ($req->ontSerial) {
                $params['ontSerial'] = $req->ontSerial;
            }
            if ($req->ontDeviceType) {
                $params['ontDeviceType'] = $req->ontDeviceType;
            }
            if ($req->newOntSerial) {
                $params['newOntSerial'] = $req->newOntSerial;
            }
            if ($req->newOntDeviceType) {
                $params['newOntDeviceType'] = $req->newOntDeviceType;
            }
            if ($req->oldOntSerial) {
                $params['oldOntSerial'] = $req->oldOntSerial;
            }
            if ($req->oldOntDeviceType) {
                $params['oldOntDeviceType'] = $req->oldOntDeviceType;
            }
        }
        return $service->l2fiber($cmd, $params);
    }
}
