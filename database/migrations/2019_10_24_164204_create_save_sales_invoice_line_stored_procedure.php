<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaveSalesInvoiceLineStoredProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS `SaveSalesInvoiceLine`;
            CREATE PROCEDURE `SaveSalesInvoiceLine`(
                salesInvoiceId INT,
                productId INT,
                description VARCHAR(191),
                pricePerPiece DECIMAL(12, 5),
                quantity DECIMAL(5, 2),
                price DECIMAL(12, 5),
                vatCode INT,
                vatPercentage DECIMAL(2, 2),
                priceVat DECIMAL(12, 5),
                priceTotal DECIMAL(12, 5),
                subscriptionLineId int,
                subscriptionLineTypeId int,
                invoiceStart VARCHAR(50),
                invoiceStop VARCHAR(50)
            )
            BEGIN
                INSERT INTO sales_invoice_lines (
                    sales_invoice_id,
                    product_id,
                    description,
                    price_per_piece,
                    quantity,
                    price,
                    vat_code,
                    vat_percentage,
                    price_vat,
                    price_total,
                    subscription_line_id,
                    sales_invoice_line_type,
                    invoice_start,
                    invoice_stop,
                    created_at,
                    updated_at
                )
                VALUES (
                    salesInvoiceId,
                    productId,
                    description,
                    pricePerPiece,
                    quantity,
                    price,
                    vatCode,
                    vatPercentage,
                    priceVat,
                    priceTotal,
                    subscriptionLineId,
                    subscriptionLineTypeId,
                    invoiceStart,
                    invoiceStop,
                    now(),
                    now()
                );
            END;
;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `SaveSalesInvoiceLine`');
    }
}
