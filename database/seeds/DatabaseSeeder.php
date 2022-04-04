<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        ini_set("memory_limit", "-1");

        Eloquent::unguard();

        // Areas related
        $this->call(AreaCodeStatusesTableSeeder::class);
        $this->call(AreaCodesTableSeeder::class);

        // Zipcode related
        $this->call(ZipcodeAreacodesTableSeeder::class);
        $this->call(ZipcodesTableSeeder::class);

        // Country related
        $this->call(CountriesTableSeeder::class);

        // Tenants related
        $this->call(TenantsTableSeeder::class);
        $this->call(NumberRangesTableSeeder::class);

        // Persons related
        $this->call(PersonTypesTableSeeder::class);
        $this->call(PersonsTableSeeder::class);

        // Relations related
        $this->call(RelationTypesTableSeeder::class);
        $this->call(RelationsTableSeeder::class);
        $this->call(RelationsPersonsTableSeeder::class);

        // Products related
        $this->call(ProductsTypesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(VatCodesTableSeeder::class);

        // Addresses related
        $this->call(AddressTypesTableSeeder::class);
        $this->call(AddressesTableSeeder::class);

        // Plans related
        $this->call(PlansTableSeeder::class);
        $this->call(PlanSubscriptionLineTypesTableSeeder::class);
        $this->call(PlanLinesTableSeeder::class);
        $this->call(PlanLinePricesTableSeeder::class);

        // Subscriptions related
        $this->call(SubscriptionsTableSeeder::class);
        $this->call(SubscriptionLinesTableSeeder::class);
        $this->call(SubscriptionLinePricesTableSeeder::class);

        // Roles
        $this->call(RoleTableSeeder::class);

        // Users
        $this->call(UsersTableSeeder::class);

        // Groups
        $this->call(GroupsTableSeeder::class);

        // Role + Group Relaetions
        $this->call(UserGroupTableSeeder::class);
        $this->call(GroupRoleTableSeeder::class);

        // SalesInvoices related
        $this->call(SalesInvoicesTableSeeder::class);
        $this->call(SalesInvoiceLinesTableSeeder::class);

        $this->call(CitiesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        
        $this->call(JsonDataTableSeeder::class);
        $this->call(WarehousesTableSeeder::class);
        $this->call(SerialsTableSeeder::class);

        // Journals related
        $this->call(AccountsTableSeeder::class);
        $this->call(FiscalYearsTableSeeder::class);
        $this->call(AccountingPeriodsTableSeeder::class);
        $this->call(JournalsTableSeeder::class);
        $this->call(EntriesTableSeeder::class);

        $this->call(FiberTenantProductsTableSeeder::class);

        // Severity related
        $this->call(SeverityTableSeeder::class);
        $this->call(FacilityTableSeeder::class);

        Artisan::call("passport:install --force");

        Eloquent::reguard();
        
    }
}
