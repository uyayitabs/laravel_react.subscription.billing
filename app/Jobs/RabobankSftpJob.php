<?php

namespace App\Jobs;

use Logging;
use App\Models\TenantBankAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\RabobankSftpMail;
use App\Services\RabobankPaymentService;
use Exception;

class RabobankSftpJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $rabobankPaymentService;
    protected $tenantBankAccount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantBankAccount)
    {
        $this->rabobankPaymentService = new RabobankPaymentService();
        $this->tenantBankAccount = $tenantBankAccount;
    }

    /**
     * Execute the job.
     *
     * @param TenantBankAccount $tenantBankAccount
     * @return void
     */
    public function handle()
    {
        // download first the new zip files
        $this->downloadNewZipFiles();

        //process the zip files
        $this->processZipFiles($this->tenantBankAccount);
    }

    /**
     * Download new zip files from Rabobank API,
     * send email about downloaded Zip files
     *
     * @return void
     */
    private function downloadNewZipFiles()
    {
        $state = '';
        $filenames = [];
        try {
            $files = Storage::disk('rabobank')->allFiles('Inbox/');
            $transfered = 0;
            Logging::information('Rabobank process', $files, 9, 1);
            foreach ($files as $file) {
                $zipFilename = str_replace("Inbox/", "", $file);
                $privatePath = "private/rabobank/$zipFilename";

                $fileExists = Storage::disk('local')->exists($privatePath);
                if (!$fileExists) {
                    $saved = Storage::disk('local')->put($privatePath, Storage::disk('rabobank')->get($file));
                    $filenames[] = $zipFilename;
                    if ($saved) {
                        $transfered += 1;
                        $this->removeFile($file);
                    }
                }
            }
            $subject = 'Rabobank betalingen';
            $state = 'ok';
            $message = '';
            if ($transfered == 0) {
                $subject = 'Geen bestanden bij Rabobank';
                $message = 'Er waren geen bestanden bij de Rabobank om te downloaden.';
                $state = 'empty';
            }
        } catch (\Exception $e) {
            $subject = 'Error bij downloaden bij Rabobank';
            $message = 'Er was een probleem bij het downloaden: ' . $e->getMessage();
        }
        $data = [
            'state' => $state,
            'message' => $message,
            'files' => $filenames,
            'tenant' => ''
        ];
        try {
            Logging::information('Rabobank process mail', $data, 9, 1);
            $message = (new RabobankSftpMail($data, $subject));
            $emails = config('rabobank.admins_email');
            $mail = Mail::to($emails);
            $mail->queue($message);
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
            Logging::exception(
                $e,
                9,
                0
            );
        }
    }

    /**
     * Process Rabobank zip file,
     * extract XML file from the zip file
     * save bank_files record using XML file information
     * process XML content and store payments record
     *
     * @return void
     */

    private function processZipFiles(TenantBankAccount $tenantBankAccount)
    {
        $rabobankDir = storage_path("app/private/rabobank");
        foreach (File::allFiles($rabobankDir) as $filePathName) {
            if (File::extension($filePathName) == "zip") {
                // Save bank_files record if not yet existing
                $processXmlFile = $this->rabobankPaymentService->saveBankFile($filePathName, $tenantBankAccount);

                // Process XML file contents
                if ($processXmlFile) {
                    $xmlFile = $this->rabobankPaymentService->getRabobankXmlFile($filePathName);
                    ProcessRabobankPayment::dispatchNow($xmlFile, $tenantBankAccount);
                }
            }
        }
    }

    /**
     * Delete file
     *
     * @param mixed $file
     */
    private function removeFile($file)
    {
        if ('production' == config('app.env')) {
            Storage::disk('rabobank')->delete($file);
        }
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        $this->delete();
    }
}
