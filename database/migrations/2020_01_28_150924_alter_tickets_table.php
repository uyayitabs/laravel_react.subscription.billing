<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function(Blueprint $table) {
            $table->string('ticket_number')->nullable()->after('id');
			$table->bigInteger('address_id')->unsigned()->nullable()->index()->after('person_id');
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index()->after('address_id');
            $table->bigInteger('parent_ticket_id')->unsigned()->nullable()->after('tenant_id');
			$table->bigInteger('ticket_group_id')->unsigned()->nullable()->after('parent_ticket_id');
            $table->integer('priority')->nullable()->after('ticket_group_id');
            $table->string('status')->nullable()->after('priority');
            $table->dateTime('resume_at')->nullable()->after('status');
            $table->string('category')->nullable()->after('resume_at');
            $table->string('type')->nullable()->after('category');
			$table->longText('tags')->nullable()->after('type');
			$table->bigInteger('created_by')->unsigned()->nullable()->index()->after('tags');
            $table->bigInteger('closed_by')->unsigned()->nullable()->index()->after('created_by');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function(Blueprint $table) {
            $table->dropColumn('ticket_number')->change();
            $table->dropColumn('address_id')->change();
            $table->dropColumn('tenant_id')->change();
            $table->dropColumn('parent_ticket_id')->change();
            $table->dropColumn('ticket_group_id')->change();
            $table->dropColumn('priority')->change();
            $table->dropColumn('status')->change();
            $table->dropColumn('resume_at')->change();
            $table->dropColumn('category')->change();
            $table->dropColumn('type')->change();
            $table->dropColumn('tags')->change();
            $table->dropColumn('created_by')->change();
            $table->dropColumn('closed_by')->change();
		});
    }
}
