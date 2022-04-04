<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaveJournalsStoredProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS `SaveJournals`;
            CREATE PROCEDURE `SaveJournals`(
                tenantId INT,
                paramDate VARCHAR(25), 
                numberRangeZeroPad INT, 
                numberRangePrefix VARCHAR(25)
            )
            BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE vTenantId INT;
                DECLARE vInvoiceId INT;
                DECLARE vDate DATE;
                DECLARE vJournalCount INT;
            
                DECLARE cur_invoices CURSOR FOR 
                    SELECT id,tenant_id,date FROM sales_invoices WHERE `date` = paramDate;
                
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
                
                OPEN cur_invoices;
                    invoice_loop : LOOP
                        FETCH cur_invoices INTO vInvoiceId,vTenantId,vDate;
                        
                        IF done = 1 THEN
                            LEAVE invoice_loop;
                        END IF;
                        
                        INSERT INTO 
                            journals(journal_no, tenant_id, invoice_id, `date`, description,created_at,updated_at) 
                            VALUES (
                                GenerateNumberRange(tenantId, "journal_no", numberRangeZeroPad, numberRangePrefix, 1),
                                vTenantId,
                                vInvoiceId,
                                vDate,
                                CONCAT("Revenue ", DATE_FORMAT(vDate, "%m-%Y")),
                                now(),
                                now()
                            );
                    END LOOP invoice_loop;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS `SaveJournals`');
    }
}
