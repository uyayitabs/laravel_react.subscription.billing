<?php

namespace App\Policies;

use Logging;
use App\Models\Tenant;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class BasePolicy
{
    protected $client;

    /**
     * Initialize
     */
    public function __construct()
    {
        // Prepare the client
        $this->client = new Client([
            'base_uri' => config('chat.url')
        ]);
    }

    public function permitted(User $user, Tenant $tenant, $socket_id = null)
    {
        // check if the current tenant is same with the accessing tenant
        $permitted = currentTenant('id') == $tenant->id;

        // return if same tenant
        if ($permitted) {
            return $permitted;
        }

        //Check if user has a relation to tenant
        $permittedTenants = [];
        $tenantUsers = $user->tenantUsers()->get();
        foreach ($tenantUsers as $tu) {
            $permittedTenants = array_merge($permittedTenants, $tu->tenant->getChildrenList());
        }

        if (!array_search($tenant->id, $permittedTenants)) {
            return false;
        }

        // grab the socketid that was include in the http header
        if (!$socket_id) {
            $socket_id = request()->header('socketid');
        }

        // save the last visited tenant
        $user->last_tenant_id = $tenant->id;
        $user->save();

        $tenant = jsonRecode($tenant);
        Arr::pull($tenant, 'payment_conditions');
        try {
            // send a tenant change in the socket id to trigger the switch tenant in the frontend base on the user who login
            $this->client->request('POST', 'tenant', [
                'form_params' => [
                    'socket_id' => $socket_id,
                    'tenant' => $tenant
                ]
            ]);
        } catch (\Exception $e) {
            Logging::exception(
                $e,
                1,
                0,
                currentTenant('id')
            );
        }

        return true;
    }
}
