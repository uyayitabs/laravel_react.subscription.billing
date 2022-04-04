<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterJsonDataBackendApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('json_data', function($table)
        {
            $table->string('backend_api', 120)->nullable()->after('product_id');
        });

        DB::statement('UPDATE json_data SET backend_api="brightblue" WHERE JSON_EXTRACT(json_data, "$.brightblue")  IS NOT NULL');
        DB::statement('UPDATE json_data SET backend_api="m7" WHERE JSON_EXTRACT(json_data, "$.m7")  IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('json_data', function(Blueprint $table)
		{
            $table->dropColumn('backend_api');
        });
    }
}
