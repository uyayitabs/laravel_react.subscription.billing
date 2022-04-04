<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailTemplatesTableProductId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_templates', function ($table) {
            $table->bigInteger('product_id')->after('tenant_id')->unsigned()->nullable()->index('email_templates_product_id_index');
        });

        Schema::table('email_templates', function ($table) {
            $table->foreign('product_id', 'email_templates_product_id_foreign')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_templates', function ($table) {
            $table->dropForeign('email_templates_product_id_foreign');
        });


        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
}
