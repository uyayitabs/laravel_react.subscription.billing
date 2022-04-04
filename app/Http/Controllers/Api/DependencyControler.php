<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AddressTypeResource;
use App\Http\Resources\AreaCodeResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\PersonTypeResource;
use App\Http\Resources\PlanSubscriptionLineTypeResource;
use App\Http\Resources\PlanLineResource;
use App\Http\Resources\RelationTypeResource;
use App\Models\AddressType;
use App\Models\AreaCode;
use App\Models\Country;
use App\Models\PaymentCondition;
use App\Models\Person;
use App\Models\PersonType;
use App\Models\PlanSubscriptionLineType;
use App\Models\Plan;
use App\Models\Product;
use App\Models\RelationType;
use App\Models\Role;
use App\Models\Tenant;

class DependencyControler extends BaseController
{
    /**
     * get all related dependecies to the relations
     *
     * @return json
     */
    public function relations()
    {
        $dependencies = [
            'relation_types' => RelationTypeResource::collection(
                RelationType::select('id', 'type')->get()
            ),
            'payment_conditions' => PaymentCondition::active()
                ->where('tenant_id', currentTenant('id'))
                ->select('id', 'description', 'net_days')
                ->get()
        ];

        return response()->json($dependencies);
    }

    /**
     * get all related dependecies to the persons
     *
     * @return json
     */
    public function persons()
    {
        $dependencies = [
            'person_types' => PersonTypeResource::collection(
                PersonType::select('id', 'type')->get()
            ),
            'countries' => CountryResource::collection(
                Country::select('numeric', 'name')->get()
            )
        ];

        return response()->json($dependencies);
    }

    /**
     * get all related dependecies to the address
     *
     * @return json
     */
    public function address()
    {
        $dependencies = [
            'address_types' => AddressTypeResource::collection(
                AddressType::select('id', 'type')->get()
            ),
            'countries' => CountryResource::collection(
                Country::select('numeric', 'name')->get()
            )
        ];

        return response()->json($dependencies);
    }

    /**
     * get all related dependecies to the plans
     *
     * @return json
     */
    public function planLines()
    {
        $dependencies = [
            'plan_line_types' => PlanSubscriptionLineTypeResource::collection(
                PlanSubscriptionLineType::all()
            )
        ];

        $plan_lines = [];
        if (request()->query('plan') != '' && request()->query('plan') != 'undefined') {
            $plan = Plan::find(request()->query('plan'));
            $lineQuery = $plan->planLines();
            if (request()->query('plan_line') != '' && request()->query('plan_line') != 'undefined') {
                $lineQuery->where('id', '<>', request()->query('plan_line'));
            }
            $lines = $lineQuery->select('product_id')->get();
            $pids = [];
            foreach ($lines as $line) {
                if ($line->product_id > 0) {
                    $pids[] = $line->product_id;
                }
            }
            $products = Product::whereNotIn('id', $pids)
                ->get();

            $planLines = $plan->parent ? $plan
                ->parent->planLines()
                ->withRelations(['product'])
                ->get() : null;
            $plan_lines = $planLines ? PlanLineResource::collection($planLines) : null;
        } else {
            $products = Product::get();
        }

        $productItems = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'description' => $product->description,
                'vat_code' => [
                    "vat_percentage" => $product->vat_code ? $product->vat_code->vat_percentage : null,
                    "description" => $product->vat_code ? $product->vat_code->description : null,
                    "active_from" => $product->vat_code ? dateFormat($product->vat_code->active_from) : null,
                ]
            ];
        });

        $dependencies['products'] = ['data' => $productItems];
        $dependencies['plan_lines'] = $plan_lines;

        return response()->json($dependencies);
    }

    /**
     * get all related dependecies to the plans
     *
     * @return json
     */
    public function plans()
    {
        $plans = Plan::has('planLines')
            ->where('tenant_id', currentTenant('id'))
            ->select('id', 'description')
            ->get();

        $dependencies = [
            'plans' => PlanSubscriptionLineTypeResource::collection($plans),
            'area_codes' => AreaCodeResource::collection(
                AreaCode::select('id')->get()
            ),
            'companies' => CompanyResource::collection(
                Tenant::all()
            )
        ];
        return response()->json($dependencies);
    }

    /**
     * get all related dependecies to the plans
     *
     * @return json
     */
    public function subscriptionLines()
    {
        $planLineTypes = PlanSubscriptionLineTypeResource::collection(
            PlanSubscriptionLineType::all()
        );

        $dependencies = [
            'products' => [],
            'subscription_lines' => [],
            'line_types' => $planLineTypes
        ];

        return response()->json($dependencies);
    }

    public function countries()
    {
        $resource = CountryResource::collection(
            Country::select('numeric', 'name')->get()
        );
        return response()->json($resource);
    }

    public function cities($country)
    {
        $cities = Country::where('numeric', $country)
            ->first()
            ->cities()
            ->get();
        return $this->sendResponse($cities, '');
    }

    public function roles()
    {
        $roles = collect(Role::get())->map(
            function ($role) {
                return ['id' => $role->id, 'slug' => $role->slug];
            }
        );

        return $this->sendResponse($roles, '');
    }

    public function groups()
    {
        $persons = Person::with('user')
            ->whereHas(
                'relations',
                function ($query) {
                    $query->where('tenant_id', 7);
                }
            )
            ->has('user')
            ->get();

        $users = collect($persons)->map(function ($person) {
            return ['id' => $person->user->id, 'name' => $person->full_name];
        });

        $roles = collect(Role::get())->map(function ($role) {
            return ['id' => $role->id, 'slug' => $role->slug];
        });

        return $this->sendResponse(['users' => $users, 'roles' => $roles], '');
    }
}
