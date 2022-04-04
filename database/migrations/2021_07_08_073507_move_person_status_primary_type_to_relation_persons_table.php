<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MovePersonStatusPrimaryTypeToRelationPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations_persons', function (Blueprint $table) {
            $table->unsignedBigInteger('person_type_id')->default(1);
            $table->smallInteger('status')->change();
            $table->boolean('primary');
            $table->foreign('person_type_id')->references('id')->on('person_types');
        });

        DB::statement('UPDATE relations_persons rp
    inner join persons p on rp.person_id = p.id
        set
            rp.person_type_id = p.person_type_id,
            rp.status = p.status,
            rp.primary = p.primary');

        Schema::table('persons', function (Blueprint $table) {
            $table->dropForeign('persons_person_type_id_foreign');
            $table->dropColumn('person_type_id');
            $table->dropColumn('status');
            $table->dropColumn('primary');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('activated')->after('enabled')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->unsignedBigInteger('person_type_id')->default(1);
            $table->string('status');
            $table->tinyInteger('primary');
            $table->foreign('person_type_id')->references('id')->on('person_types');
        });

        DB::statement('UPDATE persons p
    inner join relations_persons rp on rp.person_id = p.id
        set
            p.person_type_id = rp.person_type_id,
            p.status = rp.status,
            p.primary = rp.primary');

        Schema::table('relations_persons', function (Blueprint $table) {
            $table->dropForeign('relations_persons_person_type_id_foreign');
            $table->dropColumn('person_type_id');
            $table->string('status')->change();
            $table->dropColumn('primary');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
    }
}
