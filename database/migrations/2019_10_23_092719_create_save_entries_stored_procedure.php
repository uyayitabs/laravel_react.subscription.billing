<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaveEntriesStoredProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS `SaveEntries`;
            CREATE PROCEDURE `SaveEntries`(
                tenantId INT,
                paramDate VARCHAR(25),
                numberRangeZeroPad INT, 
                numberRangePrefix VARCHAR(25)
            )
            BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE vJournalDate DATE;
                DECLARE vJournalId INT;
                DECLARE vLineDescription VARCHAR(255);
                DECLARE vRelationId INT;
                DECLARE vInvoiceId INT;
                DECLARE vInvoiceLineId INT;
                DECLARE vAccountId INT;
                DECLARE vPeriodId INT;
                DECLARE vCredit DECIMAL(12, 5);
                DECLARE vVatCodeId INT;
                DECLARE vVatPercentage DECIMAL(12, 5);
                DECLARE vVatPrice DECIMAL(12, 5);
            
                DECLARE cur_invoice_lines CURSOR FOR 
                    SELECT j.`date`,
                           j.id `journal_id`,
                           sil.description,
                           si.relation_id,
                           j.invoice_id,
                           sil.id `invoice_line_id`,
                           tp.account_id,
                           (SELECT id FROM accounting_periods WHERE tenant_id = j.tenant_id AND date_from <= paramDate and date_to >= paramDate),
                           sil.price `credit`,
                           sil.vat_code `vatcode_id`,
                           sil.vat_percentage,
                           sil.price_vat `vat_amount`
                    FROM journals `j` 
                    LEFT JOIN sales_invoices `si` ON j.invoice_id = si.id 
                    LEFT JOIN sales_invoice_lines `sil` ON j.invoice_id = sil.sales_invoice_id 
                    LEFT JOIN tenant_products `tp` on sil.product_id = tp.product_id 
                    WHERE j.`date` = paramDate;
                    
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
                
                
                OPEN cur_invoice_lines;
                    invoice_line_loop : LOOP
                        FETCH cur_invoice_lines INTO vJournalDate, vJournalId, vLineDescription, vRelationId, vInvoiceId, vInvoiceLineId, vAccountId, vPeriodId, vCredit, vVatCodeId, vVatPercentage, vVatPrice;
                        
                        IF done = 1 THEN
                            LEAVE invoice_line_loop;
                        END IF;
                        
                        INSERT INTO 
                            entries (
                                entry_no, 
                                `date`,
                                journal_id,
                                description,
                                relation_id,
                                invoice_id,
                                invoice_line_id,
                                account_id,
                                period_id,
                                credit,
                                vatcode_id,
                                vat_percentage,
                                vat_amount,
                                created_at,
                                updated_at
                            ) 
                            VALUES (
                                GenerateNumberRange(tenantId, "entry_no", numberRangeZeroPad, numberRangePrefix, 1),
                                vJournalDate,
                                vJournalId,
                                CONCAT("Revenue entry ", vLineDescription),
                                vRelationId,
                                vInvoiceId,
                                vInvoiceLineId,
                                vAccountId,
                                vPeriodId,
                                vCredit,
                                vVatCodeId,
                                vVatPercentage,
                                vVatPrice,
                                now(),
                                now()
                            );
                    END LOOP invoice_line_loop;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `SaveEntries`');
    }
}
