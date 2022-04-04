<?php

namespace App\Services;

use App\Models\BillingRun;
use App\DataViewModels\BillingRunSummary;
use App\Repositories\Repository;
use App\Models\SalesInvoiceLine;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use Illuminate\Database\Eloquent\Builder;
use Querying;
use Logging;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Mail\PainDirectDebitMail;
use App\Models\QueueJob;
use App\Models\Relation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Spatie\ArrayToXml\ArrayToXml;
use Spatie\QueryBuilder\QueryBuilder;
use Intervention\Validation\Validator;
use Intervention\Validation\Rules\Iban;

class BillingRunService
{
    protected $vatCodeService;

    public function __construct()
    {
        $this->vatCodeService = new VatCodeService();
    }

    public function list(Request $request)
    {
        return QueryBuilder::for(BillingRun::class, $request)
            ->allowedFields(BillingRun::$fields)
            ->allowedIncludes(BillingRun::$scopes)
            ->defaultSort('-id')
            ->allowedSorts(BillingRun::$fields);
    }


    public function create($data)
    {
        $attributes = filterArrayByKeys($data, BillingRun::$fields);
        if (!array_key_exists('tenant_id', $attributes)) {
            return ["errorMessage" => 'Empty parameter field tenant_id'];
        }

        if (!array_key_exists('tenant_id', $attributes)) {
            return ["errorMessage" => 'Empty parameter field date'];
        }

        if (
            BillingRun::where([                     //if incomplete billing run exists->throw error
            ['tenant_id', $attributes['tenant_id']],
            ['status_id', '<', 22]])->exists()
        ) {
            return ["errorMessage" => 'There is already an incomplete billing run.'];
        }

        if (
            BillingRun::where([                     //if incomplete billing run exists->throw error
            ['tenant_id', $attributes['tenant_id']],
            ['date', '>', $attributes['date']]])->exists()
        ) {
            return ["errorMessage" => 'Cannot create a billing run before or on previous completed runs.'];
        }

        $attributes['date'] = Carbon::parse($attributes['date']);
        $billingRun = BillingRun::create($attributes);
        Logging::information('Create Billing Run', $data, 1, 1);

        return ['data' => $billingRun];
    }

    public function update(array $data, BillingRun $billingRun)
    {
        $log['old_values'] = $billingRun->getRawDBData();
        $billingRun->update($data);
        $log['new_values'] = $billingRun->getRawDBData();
        $log['changes'] = $billingRun->getChanges();

        Logging::information('Update Billing Run', $log, 1, 1);

        return $billingRun;
    }

    public function delete(BillingRun $billingRun)
    {
        $statusService = new StatusService();
        $statusCreateId = $statusService->getStatusId('billing_run', 'creating_invoices');
        $statusFinalizeId = $statusService->getStatusId('billing_run', 'finalizing_invoices');
        $statusDeletingId = $statusService->getStatusId('billing_run', 'deleting_billing_run');

        if ($billingRun->status_id === $statusDeletingId) {
            return ['success' => false, 'errorMessage' => 'This billing run is already being deleted!'];
        }
        if ($billingRun->status_id === $statusCreateId || $billingRun->status_id >= $statusFinalizeId) {
            return ['success' => false, 'errorMessage' => 'Cannot delete billing run with this status.'];
        }

        $billingRun->update([
            'status_id' => $statusDeletingId
        ]);

        SubscriptionLine::whereHas('salesInvoiceLines', function (Builder $query) use ($billingRun) {
            $query->whereHas('salesInvoice', function (Builder $query) use ($billingRun) {
                $query->where('billing_run_id', $billingRun->id);
            });
        })->update(['last_invoice_stop' => null]);

        SalesInvoice::where([
            ['billing_run_id', $billingRun->id],
            ['invoice_status', '>', 0]
        ])->update(['billing_run_id' => null]);

        $billingRun->delete();

        SubscriptionLine::whereNull('last_invoice_stop')->update(['last_invoice_stop' => \DB::raw("(select if((sales_invoice_lines.invoice_start > sales_invoice_lines.invoice_stop)
        OR (sales_invoice_lines.quantity < 0), date_sub(sales_invoice_lines.invoice_start, interval 1 day), sales_invoice_lines.invoice_stop)
from sales_invoice_lines where sales_invoice_lines.subscription_line_id = subscription_lines.id and subscription_lines.subscription_line_type not in (2,6) order by id desc limit 1)")]);

        Logging::information('Delete Billing Run', $billingRun, 1, 1);
        return ['success' => true, 'message' => 'Billing run was deleted successfully'];
    }

    public function show($id)
    {
        return QueryBuilder::for(BillingRun::where('id', $id))
            ->allowedFields(BillingRun::$fields)
            ->allowedIncludes(BillingRun::$scopes);
    }

    public function count()
    {
        return [];
    }

    function getBillingRunDatesByStatusId($statusId, Request $request)
    {
        $query = Querying::for(BillingRun::class)
            ->setFilter($request->get('filter'))
            ->setSortable(str_replace('billing_run_id', 'id', $request->get('sort')))
            ->setSelectables(str_replace('billing_run_id', 'id', $request->get('select')))
            ->setSearch($request->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery();
        $billingRuns = $query->where('tenant_id', currentTenant('id'));

        if (isset($statusId)) {
            $billingRuns->where('status_id', $statusId);
        }

        return $billingRuns;
    }

    function billingRunsSummary($statusId, Request $request)
    {
        $query = Querying::for(BillingRunSummary::class)
            ->setFilter($request->get('filter'))
            ->setSortable(str_replace('billing_run_id', 'id', $request->get('sort')))
            ->setSelectables(str_replace('billing_run_id', 'id', $request->get('select')))
            ->setSearch($request->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery();
        $billingRuns = $query->where('tenant_id', currentTenant('id'));

        if (isset($statusId)) {
            $billingRuns->where('status_id', $statusId);
        }

        return $billingRuns;
    }

    function getInvoiceStats($tenantId, $date, $type = 0)
    {
        /**
         * $type = 0 (unfiltered invoices)
         * $type = 1 (for billing run)
         */
        if (in_array($tenantId, [7, 8, 9])) {
            switch ($type) {
                case 1:
                    $billingRun = BillingRun::where([['tenant_id', $tenantId], ['date', $date]])->first();
                    $salesInvoices = SalesInvoice::invoicesByBillingRun($billingRun);
                    break;
                case 0:
                default:
                    $inputDate = Carbon::parse($date)->format("Y-m-d");
                    $salesInvoices = SalesInvoice::where('tenant_id', $tenantId)
                        ->whereRaw("date LIKE '%{$inputDate}%'");
                    break;
            }

            $relationCountQuery = clone $salesInvoices;
            $firstInvoiceQuery = clone $salesInvoices;

            $firstInvoiceSql = "(SELECT count(relation_id) FROM sales_invoices `b` ";
            $firstInvoiceSql .= "WHERE sales_invoices.relation_id = b.relation_id) = 1";
            $firstInvoiceQuery->whereRaw($firstInvoiceSql);

            $responseData = [
                'invoice_count' => ($salesInvoices->count()),
                'customer_count' => ($relationCountQuery->distinct('relation_id')->count()),
                'total_incl_vat' => $salesInvoices->sum('price_total'),
                'total_excl_vat' => $salesInvoices->sum('price'),
                'average_invoice_total' => $salesInvoices->avg('price_total'),
                'max_invoice_total' => $salesInvoices->max('price_total'),
                'min_invoice_total' => $salesInvoices->min('price_total'),
                'first_invoice_count' => $firstInvoiceQuery->count()
            ];

            return response()->json([
                'success' => true,
                'message' => "",
                'data' => $responseData,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => "No invoice data found.",
            'data' => [],
        ], 500);
    }

    public function generatePainDirectDebitXML(BillingRun $billingRun, $userId)
    {
        $statusService = new StatusService();

        $user = User::find($userId);
        $userPerson = $user->person()->first();
        $tenant = Tenant::find($billingRun->tenant_id);

        if (
            !$billingRun->dd_file || !File::exists($billingRun->dd_file)
        ) {
            $xmlFile = $this->createPainDebitXmlFile($billingRun, $tenant);
        } else {
            $xmlFile = $billingRun->dd_file;
        }

        if (File::exists($xmlFile)) {
            // [STEP #2] VALIDATE XML file generated using PAIN .xsd file
            $xsdFile = storage_path('app/private/xsd/pain.008.001.02.xsd');
            $validationResult = $this->validateXMLViaXSD($xmlFile, $xsdFile);

            // VALID XML
            if ($validationResult['xml_is_valid']) {
                if ($billingRun) {
                    // dd_file_created
                    $sendingInvoice = $statusService->getStatusId('billing_run', 'sending_invoices');
                    if ($billingRun->status_id > $sendingInvoice) {
                        $billingRun->update([
                            'dd_file' => $xmlFile,
                            'status_id' => $statusService->getStatusId('billing_run', 'dd_file_created')
                        ]);
                    } else {
                        $billingRun->update([
                            'dd_file' => $xmlFile
                        ]);
                    }

                    Logging::information(
                        'INVOICING - BILLING RUN (dd_file_created)',
                        [
                            "billing_run_id" => $billingRun->id,
                            "date" => $billingRun->date->copy()->format("Y-m-d"),
                        ],
                        17,
                        1,
                        $tenant->id,
                        'billing_run',
                        $billingRun->id
                    );
                }
            } else { // INVALID XML
                $billingRun->update([
                    'dd_file' => $xmlFile,
                    'status_id' => $statusService->getStatusId('billing_run', 'dd_file_failed'),
                    'last_error' => substr(json_encode($validationResult['xml_errors']), 0, 2000)
                ]);
            }

            // Send Email
            if (filter_var($userPerson->getAttribute('customer_email'), FILTER_VALIDATE_EMAIL)) {
                $ddFileURL = config('app.front_url') . "/#/billing-run";
                $params = [
                    "company_name" => $tenant->name,
                    "user_fullname" => $userPerson->first_name,
                    "bill_run_date" => $billingRun->date->copy()->format('Y-m-d'),
                    "billingRunId" => $billingRun->id,
                    "dd_file_url" => $ddFileURL
                ];
                $message = (new PainDirectDebitMail(
                    $params,
                    $tenant->id
                ));
                Mail::to($userPerson->getAttribute('customer_email'))->queue($message);
            } else {
                Logging::error(
                    'INVOICING - BILLING RUN (invalid user email)',
                    [
                        "billing_run_id" => $billingRun->id,
                        "date" => $billingRun->date->copy()->format("Y-m-d"),
                        "person_id" => $userPerson->id,
                    ],
                    17,
                    0,
                    $tenant->id,
                    'billing_run',
                    $billingRun->id
                );
            }
        }
    }

    private function createPainDebitXmlFile($billingRun, $tenant)
    {
        $statusService = new StatusService();
        //If direct debit file doesn't exist, create it
        $billingRun->update([
            'status_id' => $statusService->getStatusId('billing_run', 'creating_dd_file')
        ]);
        Logging::information(
            'INVOICING - BILLING RUN (creating_dd_file)',
            [
                "billing_run_id" => $billingRun->id,
                "date" => $billingRun->date->copy()->format("Y-m-d"),
            ],
            17,
            1,
            $tenant->id,
            'billing_run',
            $billingRun->id
        );
        $now = now();
        $processingDate = $billingRun->date;

        $salesInvoices = SalesInvoice::invoicesByBillingRun($billingRun);
        $salesInvoiceCount = $salesInvoices->count();
        $salesInvoiceTotal = floatval(0);
        $requiredControlDate = $processingDate->copy()->addDays(1)->format('Y-m-d');

        $messageIdentification = "GRID/{$tenant->id}/{$now->copy()->format('Y-m-d-H:i')}";
        $createdDateTime = $now->format('Y-m-d') . 'T' . $now->format('H:i:s');
        $paymentInfoId = "{$processingDate->copy()->format('Ymd')}NL30RABO0337235341";

        $tenantName = "Fiber Nederland";

        $data = [
            'CstmrDrctDbtInitn' => [
                'GrpHdr' => [
                    'MsgId' => $messageIdentification,
                    'CreDtTm' => $createdDateTime,
                    'NbOfTxs' => $salesInvoiceCount,
                    'CtrlSum' => $salesInvoiceTotal,
                    'InitgPty' => [
                        'Nm' => strtoupper($tenantName)
                    ]
                ],
                'PmtInf' => [
                    'PmtInfId' => $paymentInfoId,
                    'PmtMtd' => 'DD',
                    'BtchBookg' => 'true',
                    'NbOfTxs' => $salesInvoiceCount,
                    'CtrlSum' => $salesInvoiceTotal,
                    'PmtTpInf' => [
                        'SvcLvl' => [
                            'Cd' => 'SEPA',
                        ],
                        'LclInstrm' => [
                            'Cd' => 'CORE',
                        ],
                        'SeqTp' => 'RCUR',
                    ],
                    'ReqdColltnDt' => $requiredControlDate,
                    'Cdtr' => [
                        'Nm' => $tenantName
                    ],
                    'CdtrAcct' => [
                        'Id' => [
                            'IBAN' => 'NL30RABO0337235341'
                        ]
                    ],
                    'CdtrAgt' => [
                        'FinInstnId' => [
                            'BIC' => 'RABONL2U',
                        ]
                    ],
                    'ChrgBr' => 'SLEV',
                    'CdtrSchmeId' => [
                        'Id' => [
                            'PrvtId' => [
                                'Othr' => [
                                    'Id' => 'NL12ZZZ737869500000',
                                    'SchmeNm' => [
                                        'Prtry' => 'SEPA',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'DrctDbtTxInf' => []
                ]
            ]
        ];

        foreach ($salesInvoices->get() as $salesInvoice) {
            $relation = $salesInvoice->relation;
            $invoicePerson = $salesInvoice->invoicePerson()->first();

            $mandateId = $relation->getAttribute('mndt_id');
            $totalInvoiced = number_format($salesInvoice->price_total, 2, '.', '');
            $salesInvoiceTotal += tofloat($totalInvoiced);
            $bankAccount = $relation->bankAccount()->first();
            $iban = $relation->getIban(true);

            if (!empty($bankAccount->description)) {
                $debtorName = $bankAccount->description;
            } else {
                $debtorName = $invoicePerson->getAttribute('full_name');
            }


            if (($mandateId && (int)$mandateId !== -1) && $totalInvoiced > 0 && $iban) {
                $nameOfTenant = $tenant->name;

                $dateOfSignature = $relation->getAttribute('dt_of_sgntr');
                $endToEndId = $processingDate->format('Ymd') . "NL30RABO0337235341" . $salesInvoice->id;
                $remittanceInfo = $relation->customer_number . '/' . $salesInvoice->invoice_no;
                $remittanceInfo .= ' ' . generateInvoiceDate($salesInvoice->id) . ' ' . $nameOfTenant;

                $data['CstmrDrctDbtInitn']['PmtInf']['DrctDbtTxInf'][] = [
                    'PmtId' => [
                        'EndToEndId' => $endToEndId,
                    ],
                    'InstdAmt' => [
                        '_attributes' => [
                            'Ccy' => 'EUR'
                        ],
                        '_value' => $totalInvoiced
                    ],
                    'DrctDbtTx' => [
                        'MndtRltdInf' => [
                            'MndtId' => $mandateId,
                            'DtOfSgntr' => $dateOfSignature,
                            'AmdmntInd' => 'false',
                        ]
                    ],
                    'DbtrAgt' => [
                        'FinInstnId' => [
                            'Othr' => [
                                'Id' => 'NOTPROVIDED',
                            ]
                        ]
                    ],
                    'Dbtr' => [
                        'Nm' => Str::limit($debtorName, 70, ''),
                    ],
                    'DbtrAcct' => [
                        'Id' => [
                            'IBAN' => $iban,
                        ]
                    ],
                    'RmtInf' => [
                        'Ustrd' => $remittanceInfo,
                    ],
                ];
            }
        }

        // <CtrlSum></CtrlSum>
        $data['CstmrDrctDbtInitn']['GrpHdr']['CtrlSum'] = number_format($salesInvoiceTotal, 2, '.', '');
        $data['CstmrDrctDbtInitn']['PmtInf']['CtrlSum'] = number_format($salesInvoiceTotal, 2, '.', '');

        // [STEP #1] Generate XML from PHP array data
        $xmlDocument = ArrayToXml::convert($data, [
            'rootElementName' => 'Document',
            '_attributes' => [
                'xmlns' => 'urn:iso:std:iso:20022:tech:xsd:pain.008.001.02',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
            ],
        ], true, 'UTF-8', '1.0', ['formatOutput' => true]);

        $dateTime = now()->format('Ymd_His');
        $xmlFile = storage_path("app/private/dd_files/{$tenant->id}/PAIN_NL30RABO0337235341_{$dateTime}.xml");

        $xmlFileDirpath = File::dirname($xmlFile);

        if (!File::isDirectory($xmlFileDirpath)) {
            File::makeDirectory($xmlFileDirpath, 0775, true, true);
        }

        File::put($xmlFile, $xmlDocument);

        // Save direct_debit_id
        $billingRun->update([
            'direct_debit_id' => $paymentInfoId
        ]);

        return $xmlFile;
    }

    function validateXMLViaXSD($xmlFile, $xsdFile)
    {
        libxml_use_internal_errors(true);

        $domDocument = new \DOMDocument();
        $domDocument->load($xmlFile);
        $xmlErrors = [];
        $xmlIsValid = $domDocument->schemaValidate($xsdFile);
        $csvErrorFile = "";

        if (!$xmlIsValid) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                $xmlErrors[] = [
                    'level' => $error->level,
                    'code' => $error->code,
                    'column' => $error->column,
                    'message' => $error->message,
                    'file' => $error->file,
                    'line' => $error->line,
                ];
            }
            libxml_clear_errors();
            $baseCsvFilename = Str::replaceArray("." . File::extension($xmlFile), ["-errors.csv"], File::basename($xmlFile));
            $csvErrorFile = File::dirname($xmlFile) . "/" . $baseCsvFilename;
            generateCSV(
                $csvErrorFile,
                [
                    'level',
                    'code',
                    'column',
                    'message',
                    'file',
                    'line',
                ],
                $xmlErrors
            );
        }
        return [
            'xml_is_valid' => $xmlIsValid,
            'xml_errors' => $xmlErrors,
            'error_csv_file' => $csvErrorFile,
        ];
    }

    public function downloadPainDirectDebitXML($id)
    {
        $statusService = new StatusService();
        $billingRun = BillingRun::find($id);
        if ($billingRun && !$billingRun->last_error) {
            $file = $billingRun->dd_file;
        } else {
            $xmlFile = $billingRun->dd_file;
            $baseCsvFilename = Str::replaceArray("." . File::extension($xmlFile), ["-errors.csv"], File::basename($xmlFile));
            $file = File::dirname($xmlFile) . "/" . $baseCsvFilename;
        }

        if (!File::exists($file)) {
            return null;
        }

        $billingRun->update([
            'status_id' => $statusService->getStatusId('billing_run', 'closed'),
        ]);

        Logging::information(
            'INVOICING - BILLING RUN (downloaded_dd_file)',
            [
                "billing_run_id" => $billingRun->id,
                "date" => $billingRun->date,
            ],
            17,
            1,
            $billingRun->tenant_id,
            'billing_run',
            $billingRun->id
        );

        return $file;
    }

    public function createInvoiceQueueJob($billingRun, $userId)
    {
        $statusService = new StatusService();

        $queueJob = QueueJob::create([
            'job' => 'invoice:process_billing',
            'user_id' => $userId,
            'status_id' => 100,
            'tenant_id' => $billingRun->tenant_id,
            'data' => [
                '--billing_run_id' => $billingRun->id,
                '--user_id' => $userId
            ]
        ]);

        if ($queueJob) {
            $billingRun->update([
                'status_id' => $statusService->getStatusId('billing_run', 'creating_invoices') // Creating invoices
            ]);
        }

        return $queueJob;
    }

    public function createFinalizeInvoicesQueueJob($billingRun, $userId)
    {
        $statusService = new StatusService();

        $queueJob = QueueJob::create([
            'job' => 'invoice:finalize_invoices',
            'user_id' => $userId,
            'status_id' => 100,
            'tenant_id' => $billingRun->tenant_id,
            'data' => [
                '--billing_run_id' => $billingRun->id,
                '--user_id' => $userId
            ]
        ]);

        if ($queueJob) {
            $billingRun->update([
                'status_id' => $statusService->getStatusId('billing_run', 'finalizing_invoices') // Creating invoices
            ]);
        }

        return $queueJob;
    }

    public function createSendEmailQueueJob($billingRun, $userId)
    {
        $statusService = new StatusService();
        $data = [
            '--billing_run_id' => $billingRun->id,
        ];

        $queueJob = QueueJob::create([
            'job' => 'invoice:send_emails',
            'data' => $data,
            'user_id' => $userId,
            'status_id' => 100
        ]);

        if ($queueJob) {
            $billingRun->update([
                'status_id' => $statusService->getStatusId('billing_run', 'sending_invoices') // sending_invoices
            ]);
        }

        return $queueJob;
    }

    public function getRelationsInvalidIban($tenantId)
    {
        $relationsWithInvalidIban = [];
        $tenant = Tenant::find($tenantId);
        $relations = Relation::where('tenant_id', $tenantId)->get();

        foreach ($relations as $relation) {
            $bankAccount = $relation->bankAccount()->first();
            if ($bankAccount) {
                $validator = new Validator(new Iban());
                if (false == $validator->validate($bankAccount->iban)) {
                    $relationsWithInvalidIban[] = [
                        'relation_id' => $relation->id,
                        'customer_number' => $relation->customer_number,
                        'relation_name' => $relation->getAttribute('primary_person_full_name'),
                        'iban' => $bankAccount->iban
                    ];
                }
            }
        }

        if (count($relationsWithInvalidIban)) {
            $csvBasename = $tenant->getAttribute('slugged_name') . "-invalid-ibans-" . now()->format('YmdHis') . ".csv";
            $csvErrorFile = storage_path("app/private/iban_validation/{$csvBasename}");

            if (!File::isDirectory(File::dirname($csvErrorFile))) {
                File::makeDirectory(File::dirname($csvErrorFile), 0775, true, true);
            }

            generateCSV(
                $csvErrorFile,
                [
                    'relation_id',
                    'customer_number',
                    'relation_name',
                    'iban',
                ],
                $relationsWithInvalidIban
            );

            return $csvErrorFile;
        }
    }

    public function createPainXMLDDQueueJob($billingRunId, $userId)
    {
        $billingRun = BillingRun::find($billingRunId);

        if ($billingRun) {
            $data = [
                '--billing_run_id' => $billingRun->id,
                '--user_id' => $userId
            ];

            $queueJob = QueueJob::create([
                'job' => 'invoice:create_pain_dd_xml',
                'data' => $data,
                'user_id' => $userId,
                'status_id' => 100,
                'tenant_id' => $billingRun->tenant_id
            ]);

            return $queueJob;
        }
        return null;
    }
}
