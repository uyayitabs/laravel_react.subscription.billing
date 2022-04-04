<?php

namespace App\Http\Controllers\Api;

use App\Models\Group;
use App\Models\BillingRun;
use App\Http\Requests\BillingRunApiRequest;
use App\Services\BillingRunService;
use App\Services\StatusService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Spatie\QueryBuilder\QueryBuilder;

class BillingRunController extends BaseController
{
    protected $billingRunService;

    public function __construct()
    {
        $this->billingRunService = new BillingRunService();
    }

    /**
     * Return a paginated list of companies
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = $this->billingRunService->list(request());
        return $this->sendPaginate($query);
    }

    /**
     * Store a newly created BillingRun
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BillingRunApiRequest $request)
    {
        $data = jsonRecode($request->all());
        $result = $this->billingRunService->create($data);

        if (array_key_exists("errorMessage", $result) && $result["errorMessage"]) {
            return $this->sendError(
                $result['errorMessage'],
                request(BillingRun::$fields),
                422
            );
        }

        return $this->sendResult($result['data'], 'BillingRun created successfully');
    }

    /**
     * Return the specified BillingRun
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(BillingRun $billingRun)
    {
        $query = QueryBuilder::for(BillingRun::where('id', $billingRun->id))
            ->allowedIncludes(BillingRun::$scopes);

        return $this->sendSingleResult($query, 'Billing run retrieved successfully.');
    }

    /**
     * Update the specified BillingRun
     *
     * @param \App\Models\BillingRun $billingRun
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BillingRun $billingRun, BillingRunApiRequest $request)
    {
        // $this->authorize('update', $billingRun);
        $datas = jsonRecode($request->all(BillingRun::$fields));
        return $this->sendSingleResult(
            $this->billingRunService->update($datas, $billingRun),
            'Billing run updated successfully.'
        );
    }

    /**
     * Remove the specified BillingRun
     *
     * @param \App\Models\BillingRun $billingRun
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BillingRun $billingRun)
    {
        $result = $this->billingRunService->delete($billingRun);
        if ($result['success']) {
            return $this->sendResult($billingRun, 'Billing run deleted successfully.');
        }

        return $this->sendError($result['errorMessage'], []);
    }
}
