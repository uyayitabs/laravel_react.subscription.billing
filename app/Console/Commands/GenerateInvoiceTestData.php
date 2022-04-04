<?php

namespace App\Console\Commands;

use App\Models\Relation;
use App\Models\RelationsPerson;
use App\Models\TenantProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateInvoiceTestData extends Command
{
    protected $signature = 'generate:invoicing-data {--tenant_id=}';

    protected $description = 'Generate new relations, subscriptions (lines, & line prices) ';

    public function handle(): void
    {
        $tenantIdParam = $this->option('tenant_id');
        if (!empty($tenantIdParam)) {
            DB::connection()->disableQueryLog();
            foreach (Relation::where('tenant_id', 8)->get() as $relation) {
                // Create new relation for $tenantId = 8;
                $newRelation = $relation->replicate();
                $newRelation->customer_number = generateNumberFromNumberRange($tenantIdParam, 'customer_number', true);
                $newRelation->tenant_id = $tenantIdParam;
                $newRelation->save();

                // Create person(s) for the relation
                foreach ($relation->persons()->get() as $person) {
                    $newRelationPerson = new RelationsPerson();
                    $newRelationPerson->relation_id = $newRelation->id;
                    $newRelationPerson->person_id = $person->id;
                    $newRelationPerson->status = true;
                    $newRelationPerson->save();
                }

                // Create addresss(es) for the relation
                foreach ($relation->addresses()->get() as $address) {
                    $newAddress = $address->replicate();
                    $newAddress->relation_id = $newRelation->id;
                    $newAddress->save();
                }

                // For each subscription of a relation,
                // replicate (including subscription lines and subscription line prices)
                foreach ($relation->subscriptions()->get() as $subscription) {
                    $newSubscription = $subscription->replicate();
                    $newSubscription->relation_id = $newRelation->id;
                    $newSubscription->save();

                    // For each subscription lines
                    foreach ($subscription->subscriptionLines()->get() as $subscriptionLine) {
                        $newSubscriptionLine = $subscriptionLine->replicate();
                        $newSubscriptionLine->subscription_id = $newSubscription->id;
                        $newSubscriptionLine->save();

                        if ($subscriptionLine->subscriptionLinePrice()->count()) {
                            $newSubscriptionLinePrice = $subscriptionLine
                                ->subscriptionLinePrice()
                                ->first()
                                ->replicate();
                            $newSubscriptionLinePrice->subscription_line_id = $newSubscriptionLine->id;
                            $newSubscriptionLinePrice->save();
                        }
                    }
                }
            }

            foreach (TenantProduct::where('tenant_id', 8)->get() as $tenantProduct) {
                $newTenantProduct = $tenantProduct->replicate();
                $newTenantProduct->tenant_id = $tenantIdParam;
                $newTenantProduct->save();
            }
            DB::connection()->enableQueryLog();
        }
    }
}
