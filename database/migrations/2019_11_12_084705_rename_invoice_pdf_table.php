<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameInvoicePdfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('invoice_pdf_templates')) { 
            Schema::rename("invoice_pdf_templates", "pdf_templates");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('pdf_templates')) { 
            Schema::rename("pdf_templates", "invoice_pdf_templates");
        }
    }
}
