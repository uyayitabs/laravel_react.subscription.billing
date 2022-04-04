<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPdfTemplatesType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            $table->string('type', 55)->nullable()->comment('invoice,welcome_message,etc')->after('tenant_id');
            $table->text('notes')->nullable()->after('footer_html');
            $table->enum('version', ['draft', 'final', 'closed'])->after('footer_html');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('notes');
            $table->dropColumn('version');
        });
    }
}
