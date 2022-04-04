<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterAddressesTableHouseNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $currentSqlMode = DB::select("SHOW VARIABLES LIKE 'sql_mode';")[0]->Value;
        $nonStrictSqlMode = str_replace(',STRICT_TRANS_TABLES', '', $currentSqlMode);
        DB::statement("SET SESSION sql_mode='{$nonStrictSqlMode}'");

        Schema::table('addresses', function (Blueprint $table) {
            $table->string('house_number', 10)->change();
            $table->string('house_number_suffix', 10)->change();
        });

        DB::statement("SET SESSION sql_mode='{$currentSqlMode}'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('house_number', 35)->change();
            $table->string('house_number_suffix', 35)->change();
        });
    }
}
