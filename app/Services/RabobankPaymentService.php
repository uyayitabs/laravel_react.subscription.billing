<?php

namespace App\Services;

use App\Models\BankFile;
use App\Mail\RabobankPaymentProcessingErrorMail;
use App\Models\Payment;
use App\Models\TenantBankAccount;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Madnest\Madzipper\Madzipper;
use PrettyXml\Formatter;
use simplehtmldom\HtmlDocument;

class RabobankPaymentService
{
    const ERROR_MAIL_SUBJECT = "Unexpected payment type found in Rabobank file";
    const ERROR_RECIPIENT = 'marisa.vanvelzen@xsprovider.nl';

    /**
     * Save bank_file data using a .zip file
     *
     * @param mixed $zipFile
     * @param TenantBankAccount $tenantBankAccount
     * @return bool
     */
    public function saveBankFile($zipFile, TenantBankAccount $tenantBankAccount)
    {
        $processXmlFile = false;
        $xmlFile = $this->getRabobankXmlFile($zipFile);

        // Process extracted XML file
        $bankFile = BankFile::where('filename', $xmlFile)->first();
        if ($bankFile) {
            // Check if $xmlFile is already existing, if not extract zip file
            if (File::exists($xmlFile) == false) {
                $this->unzipRabobankFile($zipFile);
            } else {
                File::delete($zipFile);
            }
        } else {
            // Save new BankFile record in DB
            BankFile::create([
                'tenant_bank_account_id' => $tenantBankAccount->id,
                'filename' => $xmlFile,
                'status' => 'new'
            ]);

            // Unzip the file
            if (File::exists($xmlFile) == false) {
                $this->unzipRabobankFile($zipFile);
            }

            $processXmlFile = true;
        }
        return $processXmlFile;
    }

    /**
     * Get XML file dir path
     *
     * @param mixed $zipFile
     * @return string
     */
    public function getRabobankXmlDir($zipFile)
    {
        return File::dirname($zipFile);
    }

    /**
     * Get XML file path
     *
     * @param mixed $zipFile
     * @return string
     */
    public function getRabobankXmlFile($zipFile)
    {
        $xmlDir = $this->getRabobankXmlDir($zipFile);
        return $xmlDir . "/" . File::name($zipFile) . ".xml";
        ;
    }

    /**
     * Unzip a zip file, delete excess header.xml file
     *
     * @param mixed $zipFile
     */
    protected function unzipRabobankFile($zipFile)
    {
        $xmlDir = $this->getRabobankXmlDir($zipFile);

        // Unzip zip file
        $zipper = new Madzipper();
        $zipper->make($zipFile)->extractTo(File::dirname($zipFile));

        // Delete header.xml file
        $headerXmlFile = $xmlDir . "/header.xml";
        if (File::exists($headerXmlFile)) {
            File::delete($headerXmlFile);
        }
        File::delete($zipFile);
    }


    /**
     * Extract Rabobank payment entry details, save in payments table
     *
     * @param mixed $xmlFile
     */
    public function processXmlFile($xmlFile, TenantBankAccount $tenantBankAccount)
    {
        $bankFile = BankFile::where('filename', $xmlFile)->first();
        $xml  = new HtmlDocument($xmlFile, false);
        $ntrys = $xml->find('Ntry');
        // Entry <Ntry>
        foreach ($ntrys as $index => $ntry) {
            $creditDebitIndicator = '';
            $numberOfTransactions = null;
            $amount = $code = $date = null;
            $accountName = $accountIban = null;
            $batchId = $description = null;
            $returnCode = $returnReason = null;

            // CreditDebitIndicator <CdtDbtInd>
            $cdtDbtInd = $ntry->find('CdtDbtInd', 0);
            if ($cdtDbtInd) {
                if ($cdtDbtInd->innertext == 'CRDT') {
                    $creditDebitIndicator = '';
                } else {
                    $creditDebitIndicator = '-';
                }
            }

            // Amount <Amt>
            $amt = $ntry->find('Amt', 0);
            if ($amt) {
                $amount = $creditDebitIndicator . ((float) $amt->innertext);
            }

            // BankTransactionCode <BkTxCd>
            $bkTxCd = $ntry->find('BkTxCd', 0);
            if ($bkTxCd) {
                // Proprietary <Property>
                $property = $bkTxCd->find('Prtry', 0);
                if ($property) {
                    // Code <Cd>
                    $propertyCd = $property->find('Cd', 0);
                    if ($propertyCd) {
                        $code = (int) $propertyCd->innertext;
                    }
                }
            }

            // ValueDate <ValDt>
            $valDate = $ntry->find('ValDt', 0);
            if ($valDate) {
                // Date <Dt>
                $dt = $valDate->find('Dt', 0);
                if ($dt) {
                    $date = $dt->innertext;
                }
            }

            // EntryDetails <NtryDtls>
            $ntryDtls = $ntry->find('NtryDtls', 0);
            if ($ntryDtls) {
                // Batch <Btch>
                $btch = $ntryDtls->find('Btch', 0);
                if ($btch) {
                    // Number of Transactions
                    $nbOfTxs = $btch->find('NbOfTxs', 0);
                    if ($nbOfTxs) {
                        $numberOfTransactions = (int) $nbOfTxs->innertext;
                    }

                    // PaymentInfo <PmtInfId>
                    $pmtInfId = $btch->find('PmtInfId', 0);
                    if ($pmtInfId) {
                        $batchId = $pmtInfId->innertext;
                        $description = "SEPA withdrawal";
                    }

                    $rltdPties = $ntryDtls->find('RltdPties', 0);
                    if ($rltdPties) {
                        // Initiating Party <InitgPty>
                        $initgPty = $rltdPties->find('InitgPty', 0);
                        if ($initgPty) {
                            // Name <Nm>
                            $nm = $initgPty->find('Nm', 0);
                            if ($nm) {
                                $accountName = $nm->innertext;
                            }
                        }
                    }
                }

                // RelatedParties <RltdPties>
                $rltdPties = $ntryDtls->find('RltdPties', 0);
                if ($rltdPties) {
                    // Debtor <Dbtr>
                    $dbtr = $rltdPties->find('Dbtr', 0);
                    if ($dbtr) {
                        // Name <Nm>
                        $nm = $dbtr->find('Nm', 0);
                        if ($nm) {
                            $accountName = $nm->innertext;
                        }
                    }
                    // DebtorAccount <DbtrAcct>
                    $dbtrAcct = $rltdPties->find('DbtrAcct', 0);
                    if ($dbtrAcct) {
                        // Id <Id>
                        $id = $dbtrAcct->find('Id', 0);
                        if ($id) {
                            // IBAN <IBAN>
                            $iban = $id->find('IBAN', 0);
                            if ($iban) {
                                $accountIban = $iban->innertext;
                            }
                        }
                    }

                    // Creditor <Cdtr>
                    $cdtr = $rltdPties->find('Cdtr', 0);
                    if ($cdtr) {
                        // Name <Nm>
                        $nm = $cdtr->find('Nm', 0);
                        if ($nm) {
                            $accountName = $nm->innertext;
                        }
                    }
                    // CreditorAccount <CdtrAcct>
                    $cbtrAcct = $rltdPties->find('CdtrAcct', 0);
                    if ($cbtrAcct) {
                        // Id <Id>
                        $id = $cbtrAcct->find('Id', 0);
                        if ($id) {
                            // IBAN <IBAN>
                            $iban = $id->find('IBAN', 0);
                            if ($iban) {
                                $accountIban = $iban->innertext;
                            }
                        }
                    }
                }

                // RemittanceInformation <RmtInf>
                $rmtInf = $ntryDtls->find('RmtInf', 0);
                if ($rmtInf) {
                    // Unstructured <Ustrd>
                    $ustrd = $rmtInf->find('Ustrd', 0);
                    if ($ustrd) {
                        $description = $ustrd->innertext;
                    }
                }

                // ReturnInformation <RtrInf>
                $rtrInf = $ntryDtls->find('RtrInf', 0);
                if ($rtrInf) {
                    // Reason <Rsn>
                    $rsn = $rtrInf->find('Rsn', 0);
                    if ($rsn) {
                        // Code <Cd>
                        $rsnCd = $rsn->find('Cd', 0);
                        if ($rsnCd) {
                            $returnCode = $rsnCd->innertext;
                        }
                    }

                    // AdditionalInformation <AddtlInf>
                    $addtlInf = $rtrInf->find('AddtlInf', 0);
                    if ($addtlInf) {
                        $returnReason = $addtlInf->innertext;
                    }
                }
            }

            $paymentCodeType = null;

            switch ($code) {
                case 53: // B2B Direct Debit (Rabo-Rabo)
                case 64: // Core Direct Debit
                case 65: // Core Direct Debit (Rabo-Rabo)
                    $paymentCodeType = 'direct_debit';
                    break;

                case 680: // Core direct debit (Internet Banking)
                case 682; // B2B direct debit (Internet Banking)
                    $paymentCodeType = 'direct_debit_manual';
                    break;

                case 166: // Rejection Core direct debit
                case 169: // Rejection B2B direct debit
                    $paymentCodeType = 'direct_debit_rejection';
                    break;

                case 104: // Reversal Euro Direct Debit by customer
                case 631: // Reversal Core direct debit (bank)
                case 632: // Reversal B2B direct debit (bank)
                case 633: // Reversal Core direct debit (customer)
                    $paymentCodeType = 'direct_debit_reversal';
                    break;

                case 699: // Credit (Internet Banking)
                case 541: // Credit digital transfer
                    $paymentCodeType = 'credit_transfer';
                    break;

                case 25: // Digital transfer iDEAL (Online Banking)
                case 102: // iDEAL payment order
                case 544: // Digital transfer (Internet Banking)
                    $paymentCodeType = 'digital_transfer';
                    break;

                case 501: // Transfer (Internet Banking)
                case 578: // Transfer
                case 584: // Salary payment (per transaction) (Internet Banking)
                case 586: // Digital transfer (per transaction) (Internet Banking)
                case 588: // Salary payment (bulk) (Internet Banking)
                case 93: // Interest commission costs debit
                    $paymentCodeType = 'transfer';
                    break;

                default:
                    $paymentCodeType = null;
                    break;
            }

            if (!blank($paymentCodeType)) {
                // Save Payment with valid payment type
                Payment::create([
                    'bank_file_id' => $bankFile->id,
                    'tenant_bank_account_id' => $tenantBankAccount->id,
                    'date' => $date,
                    'amount' => $amount,
                    'account_iban' => $accountIban,
                    'account_name' => $accountName,
                    'descr' => $description,
                    'batch_id' => $batchId,
                    'batch_trx' => $numberOfTransactions,
                    'bank_code' => $code,
                    'type' => $paymentCodeType,
                    'return_code' => $returnCode,
                    'return_reason' => $returnReason,
                    'status_id' => 0
                ]);
                // After the last <Ntry> tag, update bank_file status to 'processed'
                if (count($ntrys) - 1 == $index) {
                    $bankFile->update(['status' => 'processed']);
                }
            } else { // Payment code type not recognized
                // Update bank_file status to 'error'
                $bankFile->update(['status' => 'error']);

                //Send error email to $this::ERROR_RECIPIENT
                $formatter = new Formatter();
                $xmlCodes = htmlspecialchars($formatter->format($ntry->outertext));
                $mail = new RabobankPaymentProcessingErrorMail([
                    "error_subject" => $this::ERROR_MAIL_SUBJECT,
                    "error_details" => $xmlCodes,
                    "code" => $code,
                    "xml_file" =>  $xmlFile,
                ]);
                Mail::to($this::ERROR_RECIPIENT)->queue($mail);
            }
        }
        // Update BankFile status
        $bankFile->refresh();
        if (!Str::contains($bankFile->status, ['processed', 'error'])) {
            $bankFile->update(['status' => 'processed']);
        }
    }
}
