<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Brand;
use App\DataViewModels\TenantUser;
use Logging;
use Illuminate\Http\Request;

class ApiKeyService
{
    public function list(Request $request)
    {
        return \Querying::for(ApiKey::class)
            ->setSelectables($request->get('select'))
            ->setFilter($request->get('filter'))
            ->setSortable($request->get('sort'))
            ->defaultSort('-id')
            ->make()
            ->getQuery();
    }

    public function create(array $data)
    {
        $attributes = filterArrayByKeys($data, ApiKey::$fields);
        $attributes['key'] = (string)\Str::Uuid();
        $apiKey = ApiKey::create($attributes);
        Logging::information('Create ApiKey', $attributes, 1, 1);
        return ['success' => true, 'message' => 'Api key was created successfully', 'data' => $apiKey];
    }

    public function show($id)
    {
        $apiKey = ApiKey::find($id);
        return ['success' => true, 'message' => 'Api key was retrieved successfully', 'data' => $apiKey];
    }

    public function update(array $data, ApiKey $apiKey)
    {
        $log['old_values'] = $apiKey->getRawDBData();

        $apiKey->update($data);
        $log['new_values'] = $apiKey->getRawDBData();
        $log['changes'] = $apiKey->getChanges();

        Logging::information('Update ApiKey', $log, 1, 1);

        return ['success' => true, 'message' => 'Api key was updated successfully', 'data' => $apiKey];
    }

    public function delete($id)
    {
        $apiKey = ApiKey::find($id)->first();
        $apiKey->delete();
        Logging::information('Delete ApiKey', $id, 1, 1);

        return ['success' => true, 'message' => 'Api key was deleted successfully', 'data' => $apiKey];
    }

    public function listForTenant($tenantId)
    {
        $tenantUsers = TenantUser::where('tenant_id', $tenantId)->pluck('user_id');
        return ApiKey::whereIn('user_id', $tenantUsers);
    }

    public function listForUser($userId)
    {
        return ApiKey::where('user_id', $userId);
    }
}
