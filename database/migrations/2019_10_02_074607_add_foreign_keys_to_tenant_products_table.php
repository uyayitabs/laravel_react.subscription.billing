<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTenantProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tenant_products', function(Blueprint $table)
		{
			$table->foreign('product_id', 'company_products_product_id_foreign')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('tenant_id', 'company_products_tenant_id_foreign')->references('id')->on('tenants')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('vat_code_id', 'company_products_vat_code_id_foreign')->references('id')->on('vat_codes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('account_id', 'company_products_account_id_foreign')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tenant_products', function(Blueprint $table)
		{
			$table->dropForeign('company_products_product_id_foreign');
			$table->dropForeign('company_products_tenant_id_foreign');
			$table->dropForeign('company_products_vat_code_id_foreign');
			$table->dropForeign('company_products_account_id_foreign');
		});
	}

}
