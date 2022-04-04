<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiKey;
use App\Http\Requests\ApiKeyRequest;
use App\Services\ApiKeyService;
use Illuminate\Http\Request;

class ApiKeyController extends BaseController
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ApiKeyService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //
        return $this->sendPaginateOrResult(
            $this->service->list($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ApiKeyRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ApiKeyRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendServiceResponse(
            $this->service->create($data)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  ApiKey $apiKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ApiKey $apiKey)
    {
        return $this->sendServiceResponse(
            $this->service->show($apiKey)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ApiKeyRequest  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ApiKey $apiKey, ApiKeyRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendServiceResponse(
            $this->service->update($data, $apiKey)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ApiKey  $apiKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ApiKey $apiKey)
    {
        return $this->sendServiceResponse(
            $this->service->delete($apiKey)
        );
    }

    /**
     * Display a listing of the resource for a Tenant.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showForTenant($tenantId)
    {
        return $this->sendPaginateOrResult(
            $this->service->listForTenant($tenantId)
        );
    }

    /**
     * Display a listing of the resource for a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showForUser($userId)
    {
        return $this->sendPaginateOrResult(
            $this->service->listForUser($userId)
        );
    }
}
