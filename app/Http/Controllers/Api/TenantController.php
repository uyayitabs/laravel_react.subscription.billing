<?php

namespace App\Http\Controllers\Api;

use App\Models\BillingRun;
use App\Models\Group;
use Logging;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Http\Requests\TenantApiRequest;
use App\Services\BillingRunService;
use App\Services\TenantService;
use App\Services\GroupService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Spatie\QueryBuilder\QueryBuilder;

class TenantController extends BaseController
{
    protected $service;
    protected $billingRunService;
    protected $groupService;
    protected $subscriptionService;

    public function __construct()
    {
        $this->service = new TenantService();
        $this->groupService = new GroupService();
        $this->billingRunService = new BillingRunService();
    }

    /**
     * Return a paginated list of companies
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $query = $this->service->list(request());
        return $this->sendPaginate($query);
    }

    /**
     * Store a newly created tenant
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TenantApiRequest $request)
    {
        $datas = jsonRecode($request->all(Tenant::$fields));
        if (array_key_exists('invoicing_name', $datas)) {
            $datas['settings'] = [
                'invoicing_name' => $datas['invoicing_name']
            ];
        }
        $tenant = Tenant::create($datas);
        Logging::information('Create Tenant', $tenant, 1, 1);

        //create group for new tenant
        $data = [
            'group' => [
                'name' => 'Admin',
                'description' => 'Admin Users for ' . $tenant->name,
                'tenant_id' => $tenant->id
            ],
            'roles' => [
                'plan' => ['value' => '11'],
                'product' => ['value' => '11'],
                'relation' => ['value' => '11'],
                'salesinvoice' => ['value' => '11'],
                'subscription' => ['value' => '11'],
                'tenant' => ['value' => '11'],
                'user' => ['value' => '11']
            ]
        ];
        $this->groupService->create($data);
        Logging::information('Create Group', $data, 1, 1);

        $query = QueryBuilder::for(Tenant::where('id', $tenant->id))
            ->allowedIncludes(Tenant::$scopes);

        return $this->sendSingleResult($query, 'Tenant created successfully.');
    }


    /**
     * Return the specified tenant
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tenant $tenant)
    {
        $this->authorize('view', $tenant);
        $query = QueryBuilder::for(Tenant::where('id', $tenant->id))
            ->allowedIncludes(Tenant::$scopes);

        return $this->sendSingleResult($query, 'Tenant retrieved successfully.');
    }

    /**
     * Update the specified tenant
     *
     * @param \App\Models\Tenant $tenant
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Tenant $tenant, TenantApiRequest $request)
    {
        $this->authorize('update', $tenant);

        $log['old_values'] = $tenant->getRawDBData();
        $currentSettings = is_array($tenant->settings) ? $tenant->settings : [];

        $datas = jsonRecode($request->all(Tenant::$fields));
        if (array_key_exists('invoicing_name', $datas)) {
            $currentSettings['invoicing_name'] = $datas['invoicing_name'];
            $datas['settings'] = $currentSettings;
        }
        $tenant->update($datas);

        $log['new_values'] = $tenant->getRawDBData();
        $log['changes'] = $tenant->getChanges();

        Logging::information('Update Tenant', $log, 1, 1);

        $query = QueryBuilder::for(Tenant::where('id', $tenant->id))
            ->allowedIncludes(Tenant::$scopes);


        return $this->sendSingleResult($query, 'Tenant updated successfully.');
    }

    /**
     * Remove the specified tenant
     *
     * @param \App\Models\Tenant $tenant
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return $this->sendResult($tenant, 'Tenant deleted successfully.');
    }

    /**
     * Return the list plans with id and name
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $companies = Tenant::select('id', 'name');

        return $this->sendResults($companies, 'Tenant lists retrieved successfully.');
    }

    /**
     * Get tenant record counts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $query = Tenant::where('id', currentTenant('id'))->count();

        return $this->sendResult($query, 'Tenant counts retrieved successfully.');
    }

    /**
     * Get tenant record counts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function doSwitch(Tenant $tenant)
    {
        $currentUser = request()->user();
        $currentUser->last_tenant_id = $tenant->id;
        $currentUser->save();

        return $this->sendResult($tenant, '');
    }

    public function listThrees()
    {
        $relations = request()->user()->person->relations;
        $query = Tenant::with('childrenRecursive', 'parent')->whereIn('id', $relations->pluck('tenant_id'));

        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $filter = request()->filter['keyword'];
            $query->search($filter);
        }

        return $this->sendResult($query->get());
    }

    /**
     * Get child tenants
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function my()
    {
        $relations = request()->user()->person->relations;

        $query = Tenant::with('childrenRecursive', 'parent')->whereIn('id', $relations->pluck('tenant_id'));
        $model = $query->get();

        $results = $model->toArray();
        $mytenants = $this->flattenTenants($results);


        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $tenant_ids = [];
            $filter = request()->filter['keyword'];

            $tenant_ids = collect($mytenants)->map(function ($item, $key) {
                return $item['id'];
            });

            $query = Tenant::whereIn('id', $tenant_ids);
            $query->search($filter);

            $model = $query->get();

            $results = $model->toArray();

            $mytenants = $results;
        }

        $tenants = [];
        foreach ($mytenants as $tenant) {
            $parent = [];
            if ($tenant['parent_id'] && isset($tenant['parent'])) {
                $perentTenant = $tenant['parent'];
                $parent = [
                    'id' => $perentTenant['id'],
                    'parent_id' => $perentTenant['parent_id'],
                    'name' => $perentTenant['name'],
                    'billing_day' => $perentTenant['billing_day'],
                    'billing_schedule' => $perentTenant['billing_schedule'],
                    'invoice_start_calculation' => $perentTenant['invoice_start_calculation'],
                    'use_accounting' => $perentTenant['use_accounting'],
                    'default_country_id' => $perentTenant['default_country_id']
                ];
            }
            $tenants[] = [
                'id' => $tenant['id'],
                'parent_id' => $tenant['parent_id'],
                'name' => $tenant['name'],
                'billing_day' => $tenant['billing_day'],
                'billing_schedule' => $tenant['billing_schedule'],
                'invoice_start_calculation' => $tenant['invoice_start_calculation'],
                'use_accounting' => $tenant['use_accounting'],
                'default_country_id' => $tenant['default_country_id'],
                'parent' => $parent
            ];
        }

        return $this->sendResult($tenants);
    }

    private function flattenTenants($tenants, &$results = [])
    {
        if (!empty($tenants)) {
            foreach ($tenants as $tenant) {
                $children = $tenant['children_recursive'];
                unset($tenant['children_recursive']);
                $results[] = $tenant;
                $this->flattenTenants($children, $results);
            }
        }

        return $results;
    }

    /**
     * Return a list of groups
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups(Tenant $tenant)
    {
        $this->authorize('view', $tenant);
        $groups = $this->groupService->list()
            ->with(['groupRoles'])
            ->get()
            ->toArray();

        foreach ($groups as $i => $group) {
            $roles = [];
            foreach ($group['group_roles'] as $role) {
                $role_name = $role['role']['slug'];
                $roles[$role_name] = (object)['value' => $role['write'] . $role['read']];
            }
            $groups[$i]['roles'] = $roles;
            unset($groups[$i]['group_roles']);
        }

        return $this->sendResult($groups, '');
    }

    /**
     * Store a newly created group
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createGroup()
    {
        $attributes = request([
            'name',
            'description'
        ]);
        $attributes['tenant_id'] = currentTenant('id');

        $data = [
            'group' => $attributes,
            'roles' => request('roles')
        ];
        Logging::information('Create Group', $data, 1, 1);

        $group = $this->groupService->create($data);
        return $this->sendResponse($group, 'Group created successfully.');
    }

    /**
     * Update the specified tenant
     *
     * @param \App\Models\Group $group
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGroup(Tenant $tenant, Group $group)
    {
        // return $this->sendResponse($group, 'Group updated successfully.');

        $attributes = request([
            'name',
            'description'
        ]);
        $data = [
            'group' => $attributes,
            'roles' => request('roles')
        ];
        Logging::information('Update Group', $data, 1, 1);

        $this->groupService->update($group, $data);
        $query = QueryBuilder::for(Group::where('id', $group->id))
            ->allowedIncludes(Group::$scopes);

        return $this->sendSingleResult($query, 'Group updated successfully.');
    }

    /**
     * Return a list of product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function products()
    {
        $tenantProducts = TenantProduct::where('tenant_id', currentTenant('id'))->get();
        $list = $tenantProducts->map(function ($tenantProduct) {
            return [
                'id' => $tenantProduct->product->id,
                'description' => $tenantProduct->product->description,
                'product_type_id' => $tenantProduct->product->product_type_id,
                'vat_code' => [
                    'vat_percentage' => $tenantProduct->vatCode ? $tenantProduct->vatCode->vat_percentage : 0,
                    'description' => $tenantProduct->vatCode ? $tenantProduct->vatCode->description : 'BTW 0%'
                ],
                'price' => $tenantProduct->price
            ];
        });
        return $this->sendResponse($list, 'Product lists retrieved successfully.');
    }

    /**
     * Return list of VatCode
     */
    public function vatCodes(Tenant $tenant)
    {
        $this->authorize('view', $tenant);
        return $this->service->vatCodes($tenant);
    }

    public function invoiceStats()
    {
        return $this->billingRunService->getInvoiceStats(
            request('tenant_id'),
            request('date'),
            request('type')
        );
    }

    public function createPainDDFile()
    {
        $queueJob = $this->billingRunService->createPainXMLDDQueueJob(
            request('billing_run_id'),
            request('user_id')
        );

        return response()->json([
            'success' => true,
            'message' => "When done, you will receive an email with a link to download the direct debit file.",
            'data' => $queueJob,
        ], 200);
    }

    public function downloadPainDDFile($id)
    {
        $ddFile = $this->billingRunService->downloadPainDirectDebitXML($id);
        if ($ddFile != null) {
            $response = Response::download(
                $ddFile,
                File::basename($ddFile),
                [
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Content-Type' => 'application/xml',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );
            return $response;
        }
        return $this->sendError("Direct Debit file not found.", []);
    }

    public function validateIbans($tenant)
    {
        $csvFile = $this->billingRunService->getRelationsInvalidIban($tenant);
        if ($csvFile) {
            return response()->file($csvFile);
        }

        return null;
    }
}
