<?php

namespace App\Services;

use App\Models\CdrUsageCost;
use Illuminate\Support\Facades\Storage;
use Logging;
use App\Mail\CdrSummaryMail;
use App\Models\Relation;
use App\Models\SalesInvoiceLine;
use App\Models\SalesInvoice;
use App\Models\Subscription;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\CdrUsageCostResource;
use Illuminate\Support\Facades\Cache;

class CdrUsageCostService
{
    protected $queueJobService;

    public function __construct()
    {
        $this->queueJobService = new QueueJobService();
    }

    public function saveCSV($params)
    {
        ini_set('memory_limit', '-1');
        $cdr = $params['cdr'];
        $filename = $params['filename'];

        $filePath = 'private/cdr/' . $filename;
        if (Storage::exists($filePath)) {
            return ['success' => false, 'message' => 'CDR csv file ' . $filename . ' has already been uploaded.'];
        }

        try {
            $base64_str = substr($cdr, strpos($cdr, ",") + 1);
            $csv = base64_decode($base64_str);
            $res = Storage::put($filePath, $csv);
        } catch (Exception $e) {
            Logging::exceptionWithMessage($e, 'Could not write given CSV: ' . $filename, 20);
            return [
                'success' => false,
                'message' => 'Could not write given CSV: ' . $filename
            ];
        }

        if ($res) {
            $exists = Storage::exists($filePath);

            if (!$exists) {
                return ['success' => false, 'message' => 'Invalid or corrupted file.'];
            }
        }

        return ['success' => true];
    }

    public function startCsvImportJob($csvFilename, $tenantId, $userId)
    {
        //Try open file with stream
        try {
            $filePath = Storage::path('private/cdr/' . $csvFilename);
            if (($stream = fopen($filePath, 'r')) !== false) {
                $headers = fgetcsv($stream, 0, ',');
                if (!$this->validateCSVHeaders($headers)) {
                    fclose($stream);
                    Storage::delete('private/cdr/' . $csvFilename);
                    return [
                        'success' => false,
                        'message' => 'Column headers of CSV are invalid'
                    ];
                }

                $this->queueJobService->create('cdr:import_csv', [
                    '--file' => $filePath,
                    '--tenant_id' => $tenantId,
                    '--user_id' => $userId], 'cdr', request()->user()->id);
            }
        } catch (Exception $e) {
            Logging::exceptionWithMessage($e, 'Could not read given CSV: ' . $csvFilename, 20);
            Storage::delete('private/cdr/' . $csvFilename);
            return [
                'success' => false,
                'message' => 'Could not read given CSV: ' . $csvFilename
            ];
        }

        return [
            'success' => true,
            'message' => 'Csv processed successfully.'
        ];
    }


    public function processCSVImport($csvFilePath, $tenantId): array
    {
        $processed = 0;
        $headers = $failed = [];

        try {
            if (($stream = fopen($csvFilePath, 'r')) !== false) {
                //Try creating Cdrs
                $headers = fgetcsv($stream, 0, ',');
                while (($cdr = fgetcsv($stream)) !== false) {
                    //Invalid CDR, not enough fields
                    if (count($headers) !== count($cdr)) {
                        $failed[] = $cdr;
                        continue;
                    }

                    //Insert CDR into database
                    $data = array_combine($headers, $cdr);
                    try {
                        if (!$this->addUsageCostFromCdr($data, $tenantId)) {
                            $failed[] = $cdr;
                        } else {
                            $processed++;
                        }
                    } catch (Exception $e) {
                        $failed[] = $cdr;
                        Logging::exceptionWithData($e, 'Unexpected exception while reading CDR ' . $csvFilePath, $cdr, 20);
                    }
                }
                fclose($stream);
            }
        } catch (Exception $e) {
            Logging::exceptionWithMessage($e, 'Could not read given CSV: ' . $csvFilePath, 20);
        }

        $data = [
            'filepath' => $csvFilePath,
            'processed' => $processed
        ];

        // Set the costs of phonecalls to us (Teleplaza) to zero
        DB::update("UPDATE cdr_usage_costs SET total_cost=0 WHERE `datetime` > '2020-01-01' AND recipient LIKE '%207605040';");

        if (count($failed)) {
            // Generate CSV with phone number issues
            $dir = pathinfo($csvFilePath, PATHINFO_DIRNAME);
            $fileName = pathinfo($csvFilePath, PATHINFO_FILENAME);
            $fileExtension = pathinfo($csvFilePath, PATHINFO_EXTENSION);
            $errorFile = "{$dir}/{$fileName}-error.{$fileExtension}";
            File::put($errorFile, '');
            if (($stream = fopen($errorFile, 'w')) !== false) {
                fputcsv($stream, $headers);
                foreach ($failed as $f) {
                    fputcsv($stream, $f);
                }
                fclose($stream);
            }
            $data['failed'] = count($failed);
            $data['failed_filepath'] = $errorFile;
        }

        Logging::information(
            'CDR - IMPORT POST',
            [
                'processed' => $processed,
                'failed' => count($failed)
            ],
            20
        );

        return $data;
    }

    public function addUsageCostFromCdr(array $csvData, $tenantId)
    {
        $sender = getE164PhoneNumber($csvData['Afzender']);
        $recipient = getE164PhoneNumber($csvData['Bestemming']);

        if (is_null($sender) || is_null($recipient)) {
            Logging::error(
                'CDR Import error; Sender / Recipient not a PhoneNumber',
                $csvData,
                20,
                1,
                0
            );
            return null;
        }

        $paramCustomerNumber = $csvData['Klantnummer'];
        $customerNumber = trim(
            preg_replace_array(
                "/[A-Z]$/",
                [''],
                preg_replace_array(
                    "/^FV/",
                    ['FP'],
                    $paramCustomerNumber
                )
            )
        );
        $relation = $subscription = null;
        //Get Relation
        if (!empty($customerNumber)) {
            $relation = Relation::where([['customer_number', $customerNumber], ['tenant_id', $tenantId]])->first();
        }
        //Get Subscription
        if (isset($relation)) {
            $subscription = Subscription::where([['relation_id', $relation->id], ['status', 1]])->first();
        }
        //If CDR could not be bound, it fails
        if (!isset($relation) || !isset($subscription)) {
            Logging::error(
                'CDR - Import failed; No Relation / No Subscription / Inactive Subscription ' . $customerNumber,
                $csvData,
                20,
                1,
                0
            );
            return null;
        }

        $datetime = Carbon::parse($csvData['Datum'] . " " . $csvData['Tijdstip'], 'Europe/Amsterdam')->setTimeZone('UTC');

        if (!isset($csvData['Totaaltarief']) || $csvData['Totaaltarief'][0] === ',') {
            $csvData['Totaaltarief'] = "0" . $csvData['Totaaltarief'];
        }
        if (!isset($csvData['Starttarief']) || $csvData['Starttarief'][0] === ',') {
            $csvData['Starttarief'] = "0" . $csvData['Starttarief'];
        }
        if (!isset($csvData['Minuuttarief']) || $csvData['Minuuttarief'][0] === ',') {
            $csvData['Minuuttarief'] = "0" . $csvData['Minuuttarief'];
        }
        $totalCosts = tofloat($csvData['Totaaltarief']) / 100;
        $startCosts = tofloat($csvData['Starttarief']) / 100;
        $minuteCosts = tofloat($csvData['Minuuttarief']) / 100;
        return CdrUsageCost::create([
            'unique_id' => $csvData['UniqueId'],
            'customer_number' => $customerNumber,
            'relation_id' => $relation->id,
            'subscription_id' => $subscription->id,
            'channel_id' => $csvData['ChannelId'],
            'sender' => $sender,
            'recipient' => $recipient,
            'duration' => $csvData['Duur'],
            'platform' => $csvData['Platform'],
            'total_cost' => $totalCosts,
            'start_cost' => $startCosts,
            'minute_cost' => $minuteCosts,
            'traffic_class' => !empty(trim($csvData['Verkeersklasse'])) ? $csvData['Verkeersklasse'] : null,
            'direction' => !empty(trim($csvData['Richting'])) ? $csvData['Richting'] : null,
            'extension' => !empty(trim($csvData['Extensie'])) ? $csvData['Extensie'] : null,
            'roaming' => !empty(trim($csvData['Roaming'])) ? $csvData['Roaming'] : null,
            'bundle' => !empty(trim($csvData['Bundel'])) ? $csvData['Bundel'] : null,
            'order_number' => !empty(trim($csvData['Ordernummer'])) ? $csvData['Ordernummer'] : null,
            'datetime' => $datetime
        ]);
    }

    public function getCdrUsageCosts(SalesInvoice $salesInvoice)
    {
        $cdrs = Cache::rememberForever('salesInvoiceCdr' . $salesInvoice->id, function () use ($salesInvoice) {
            return CdrUsageCost::whereHas('salesInvoiceLine', function ($query) use ($salesInvoice) {
                $query->where('sales_invoice_id', $salesInvoice->id);
            })
                ->orderBy('datetime', 'desc')
                ->select([
                    'duration',
                    'id',
                    'datetime',
                    'sender',
                    'recipient',
                    'total_cost',
                    'sales_invoice_line_id'
                ])
                ->get();
        });

        $total_cost = count($cdrs) > 0 ? (1 + $cdrs[0]->salesInvoiceLine->vat_percentage) * $cdrs->pluck('total_cost')->sum() : 0;

        return [
            'total_cost' => $total_cost,
            'items' => CdrUsageCostResource::collection($cdrs)
        ];
    }

    public function getCdrUsageCostsPdf($salesInvoiceLineId)
    {
        $salesInvoiceLine = SalesInvoiceLine::find($salesInvoiceLineId);

        return [
            "file_exists" => File::exists($salesInvoiceLine->call_summary_file_fullpath),
            "file" => $salesInvoiceLine->call_summary_file_fullpath
        ];
    }

    /**
     *
     * @param SalesInvoiceLine $salesInvoiceLine
     * @param bool $generateHTMLFile
     * @return string
     */
    public function generateCdrSummaryPdf(SalesInvoiceLine $salesInvoiceLine, $generateHTMLFile = false)
    {
        $salesInvoice = $salesInvoiceLine->salesInvoice;
        $relation = $salesInvoice->relation()->first();
        $tenant = $salesInvoice->tenant()->first();
        $cdrUsageCosts = $salesInvoiceLine->cdrUsageCosts()->get();
        $period = $salesInvoiceLine->period;

        $invoiceData = [
            'invoice_line_period_start' => $period['start'],
            'invoice_line_period_stop' => $period['stop']
        ];
        $cdrSumTotalCosts = number_format((1 + $salesInvoiceLine->vat_percentage) * $cdrUsageCosts->sum('total_cost'), 2);
        $cdrData = [];
        foreach ($cdrUsageCosts as $lineCosts) {
            $datetime = $lineCosts->datetime->setTimezone("Europe/Amsterdam");

            $cdrData[] = [
                'date' => $datetime->copy()->format('Y-m-d'),
                'time' => $datetime->copy()->format('H:i:s'),
                'sender' => $lineCosts->sender,
                'recipient' => $lineCosts->recipient,
                'duration' => gmdate("H:i:s", $lineCosts->duration),
                'total_cost' => number_format((1 + $salesInvoiceLine->vat_percentage) * $lineCosts->total_cost, 2)
            ];
        }

        $data = compact(
            "invoiceData",
            "cdrSumTotalCosts",
            "cdrData",
        );

        // Get cdr_summary pdf template from DB
        $pdfTemplate = $tenant->getPdfTemplate('cdr_summary')->first();

        // Prepare invoice HTML using $pdfTemplate->main_html;
        $invoiceHTML = getStringBladeView($pdfTemplate->main_html, $data);

        $pdfDirPath = $salesInvoiceLine->call_summary_file_dir_path;
        $pdfFullpath = $salesInvoiceLine->call_summary_file_fullpath;

        if (!File::isDirectory($pdfDirPath)) {
            try {
                File::makeDirectory($pdfDirPath, 0775, true, true);
            } catch (Exception $exception) {
                Logging::exceptionWithData(
                    $exception,
                    'CDR SUMMARY EXCEPTION - FOLDER CREATION',
                    [
                        'relation_id' => $relation->id,
                        'invoice_id' => $salesInvoice->id,
                    ],
                    20,
                    0,
                    $salesInvoice->tenant_id,
                    'invoice',
                    $salesInvoice->id
                );
            }
        }

        if ($generateHTMLFile) {
            $htmlOutputFile = str_replace(".pdf", ".html", $pdfFullpath);
            if (file_exists($htmlOutputFile)) {
                File::delete($htmlOutputFile);
            }
            file_put_contents($htmlOutputFile, $invoiceHTML);
        }

        if (file_exists($pdfFullpath)) {
            File::delete($pdfFullpath);
        }

        try {
            SnappyPdf::loadHTML($invoiceHTML)
                ->setPaper('a4')
                ->setOptions([
                    'no-background' => false,
                    'background' => true,
                    'disable-javascript' => true,
                    'print-media-type' => false,
                    'disable-smart-shrinking' => true,
                    'lowquality' => false,
                    'header-html' => $pdfTemplate->header_html,
                    'margin-top' => "3.81cm",
                    'margin-right' => 0,
                    'footer-html' => $pdfTemplate->footer_html,
                    'margin-bottom' => "2.54cm",
                    'margin-left' => 0,
                ])
                ->save($pdfFullpath);
            return $pdfFullpath;
        } catch (Exception $exception) {
            Logging::exceptionWithData(
                $exception,
                "CDR SUMMARY EXCEPTION - PDF CREATION",
                [
                    'relation_id' => $relation->id,
                    'invoice_id' => $salesInvoice->id,
                ],
                20,
                0,
                $salesInvoice->tenant_id,
                'invoice',
                $salesInvoice->id
            );
            return '';
        }
    }

    public function sendCdrSummaryEmail(SalesInvoiceLine $salesInvoiceLine)
    {
        $salesInvoice = $salesInvoiceLine->salesInvoice()->first();
        $customer = $salesInvoice->relation()->first();
        $invoicePerson = $salesInvoice->invoicePerson()->first();

        if (filter_var($invoicePerson->customer_email, FILTER_VALIDATE_EMAIL)) {
            $pdfFile = $salesInvoiceLine->call_summary_file_fullpath;
            if (File::exists($pdfFile)) {
                $email = new CdrSummaryMail(
                    [
                        "user_fullname" => $invoicePerson->full_name,
                        "datePeriodDescription" => generateInvoiceDate($salesInvoice->id)
                    ],
                    $pdfFile,
                    $salesInvoice->id
                );
                Mail::to($invoicePerson->customer_email)->queue($email);
                return [
                    'success' => true,
                    'message' => "Email sent."
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Email not sent."
                ];
            }
        } else {
            Logging::error(
                'CDR SUMMARY - NO INVOICE PERSON EMAIL',
                [
                    'relation_id' => $customer->id,
                    'person_id' => $invoicePerson->id,
                    'invoice_id' => $salesInvoice->id
                ],
                17,
                0,
                $salesInvoice->tenant_id,
                'invoice',
                $salesInvoice->id
            );
            return [
                'success' => false,
                'message' => "Email not sent."
            ];
        }
    }

    private function validateCSVHeaders($headers)
    {
        if (!in_array('UniqueId', $headers, true)) {
            return false;
        }
        if (!in_array('Klantnummer', $headers, true)) {
            return false;
        }
        if (!in_array('Datum', $headers, true)) {
            return false;
        }
        if (!in_array('Tijdstip', $headers, true)) {
            return false;
        }
        if (!in_array('Afzender', $headers, true)) {
            return false;
        }
        if (!in_array('Bestemming', $headers, true)) {
            return false;
        }
        if (!in_array('Totaaltarief', $headers, true)) {
            return false;
        }
        if (!in_array('Starttarief', $headers, true)) {
            return false;
        }
        if (!in_array('Minuuttarief', $headers, true)) {
            return false;
        }
        if (!in_array('ChannelId', $headers, true)) {
            return false;
        }
        if (!in_array('Duur', $headers, true)) {
            return false;
        }
        if (!in_array('Platform', $headers, true)) {
            return false;
        }
        if (!in_array('Verkeersklasse', $headers, true)) {
            return false;
        }
        if (!in_array('Richting', $headers, true)) {
            return false;
        }
        if (!in_array('Extensie', $headers, true)) {
            return false;
        }
        if (!in_array('Roaming', $headers, true)) {
            return false;
        }
        if (!in_array('Bundel', $headers, true)) {
            return false;
        }
        if (!in_array('Ordernummer', $headers, true)) {
            return false;
        }
        return true;
    }
}
