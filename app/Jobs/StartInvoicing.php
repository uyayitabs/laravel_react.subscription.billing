<?php

namespace App\Jobs;

use App\Models\BillingRun;
use App\Exceptions\NoPriceFoundException;
use Logging;
use App\Models\SalesInvoice;
use App\Models\Subscription;
use App\Services\SalesInvoiceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class StartInvoicing implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $subscriptionIds = [];
    protected $processingDate;
    protected $salesInvoiceService;
    protected $billingRunId;
    protected $userId;

    private $noPriceFoundMessages = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $subscriptionIds, $processingDate, $billingRunId, $userId)
    {
        $this->subscriptionIds = $subscriptionIds;
        $this->processingDate = $processingDate;
        $this->billingRunId = $billingRunId;
        $this->userId = $userId;
        $this->salesInvoiceService = new SalesInvoiceService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $billingRun = BillingRun::find($this->billingRunId);
            $subscriptions = Subscription::whereIn("id", $this->subscriptionIds)->get();

            foreach ($subscriptions as $index => $subscription) {
                Logging::debug(
                    'CURRENTLY INVOICING ' . $subscription->id,
                    [
                        'subscription_id' => $subscription->id,
                        'relation_id' => $subscription->relation_id
                    ],
                    17,
                    0,
                    $subscription->relation->tenant_id,
                    'subscription',
                    $subscription->id
                );

                // Check if this is the main billing run
                $isMainBillingRun = false;
                $billingRunDate = Carbon::parse($this->processingDate);
                $tenantBillingDay = $billingRun->tenant->billing_day;
                $tenantBillingDate = $billingRunDate->copy();
                $tenantBillingDate->day = $tenantBillingDay;
                $endOfMonth = $billingRunDate->copy();
                $endOfMonth->addMonth()->day = 0;
                if (
                    !BillingRun::whereBetween('date', [$tenantBillingDate, $endOfMonth])->where([
                    ['tenant_id', $billingRun->tenant_id],
                    ['id', '!=', $billingRun->id]
                    ])->exists() &&
                    $billingRunDate->day >= $tenantBillingDay
                ) {
                    $isMainBillingRun = true;
                }

                try {
                    $this->salesInvoiceService->createSalesInvoices(
                        $subscription,
                        $billingRunDate,
                        $this->billingRunId,
                        $isMainBillingRun
                    );
                } catch (NoPriceFoundException $e) {
                    $this->noPriceFoundMessages[] = $e->getMessage();
                } catch (Exception $e) {
                    Logging::exceptionWithMessage($e, 'Unable to make an invoice', 7);
                }

                // If last record of the Subscriptions send BillingRun status email
                if ($index == $subscriptions->count() - 1) {
                    $this->sendInvoiceCheckMail($billingRun);

                    $billingRun->update([
                        'status_id' => 12 // invoices_created
                    ]);
                }
            }
        } catch (Exception $exception) {
            if (isset($billingRun)) {
                // Send email - invoicing failed
                $invoiceService = new SalesInvoiceService();
                $invoiceService->sendGenerateInvoiceErrorMail(
                    $this->userId,
                    $this->billingRunId,
                    [
                        'adminToolsUrl' => null,
                        'errorData' => $exception->getTraceAsString()
                    ],
                    false
                );
                $billingRun->update([
                    'status_id' => 11 // invoices_failed
                ]);

                Logging::exceptionWithData(
                    $exception,
                    'INVOICING - EXCEPTION',
                    [
                        'billing_run_id' => $this->billingRunId,
                        'subscription_ids' => $this->subscriptionIds
                    ],
                    17,
                    0
                );
            }
        }
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        Logging::exceptionWithData(
            $exception,
            'Exception - Invoicing',
            [
                'billing_run_id' => $this->billingRunId,
                'subscription_ids' => $this->subscriptionIds
            ],
            17
        );
        $this->delete();
    }

    public function sendInvoiceCheckMail($billingRun)
    {
        $billingRunId = $billingRun->id;
        //Eerste facturen(geen deposit) LIMIT 5
        //Count distinct salesinvoice id  where billing_run
        $firstInvoices = json_decode(json_encode(DB::select("
select distinct(sales_invoices.id), sales_invoices.price, sales_invoices.relation_id
from sales_invoices where billing_run_id = ?
and id in (
    select sales_invoice_id
    from sales_invoice_lines
    group by sales_invoice_id, subscription_id
    having count(sales_invoice_id) = 1)
LIMIT 5", [$billingRunId])), true);

        //Laatste facturen              LIMIT 5
        $lastInvoices = json_decode(json_encode(DB::select("
select distinct(sales_invoices.id), sales_invoices.price, sales_invoices.relation_id
from sales_invoices where id in (
  select sales_invoice_id from sales_invoice_lines, subscription_lines
  where billing_run_id = ?
  and invoice_stop = subscription_lines.subscription_stop
  and subscription_line_id = subscription_lines.id)
LIMIT 5", [$billingRunId])), true);

        //Hoogste bedrag                    LIMIT 5
        $highestAmount = json_decode(json_encode(DB::select("
select distinct(id), sales_invoices.price, sales_invoices.relation_id
from sales_invoices
where billing_run_id = ?
order by price desc limit 5", [$billingRunId])), true);

        //Laagste bedrag > 0                LIMIT 5
        $lowestAboveZero = json_decode(json_encode(DB::select("
select distinct(id), sales_invoices.price, sales_invoices.relation_id
from sales_invoices
where billing_run_id = ?
  and price > 0
order by price asc limit 5", [$billingRunId])), true);

        //Laagste bedrag < 0                LIMIT 5
        $lowestAmount = json_decode(json_encode(DB::select("
select distinct(id), sales_invoices.price, sales_invoices.relation_id
from sales_invoices
where billing_run_id = ?
  and price < 0
order by price asc limit 5", [$billingRunId])), true);

        //Niet eerste factuur, Quantity > 1, Subscription stop == null
        $catchUp = json_decode(json_encode(DB::select("
select distinct(sales_invoices.id), sales_invoices.price, sales_invoices.relation_id
from sales_invoices, sales_invoice_lines, subscription_lines
where billing_run_id = ?
and sales_invoices.id = sales_invoice_lines.sales_invoice_id
and sales_invoice_lines.subscription_line_id = subscription_lines.id
and sales_invoice_lines.quantity > 1
and sales_invoice_lines.invoice_start != subscription_lines.subscription_start
and subscription_lines.subscription_stop != subscription_lines.subscription_stop limit 5", [$billingRunId])), true);

        //Separate deposit              LIMIT 5 if possible
        $deposit = json_decode(json_encode(DB::select("
select distinct(sales_invoices.id), sales_invoices.price, sales_invoices.relation_id from sales_invoices where id in (
  select sales_invoice_id from sales_invoice_lines, subscription_lines
  where billing_run_id = ?
  and sales_invoices.id = sales_invoice_lines.sales_invoice_id
  and subscription_line_id = subscription_lines.id
  and sales_invoice_line_type = 6)
LIMIT 5", [$billingRunId])), true);

        //    Facturen met totaalbedrag 0       LIMIT 5
        $totalZero = json_decode(json_encode(DB::select("
select distinct(sales_invoices.id), sales_invoices.price, sales_invoices.relation_id from sales_invoices, sales_invoice_lines, subscription_lines
where billing_run_id = ?
and sales_invoices.id = sales_invoice_lines.sales_invoice_id
and sales_invoice_lines.subscription_line_id = subscription_lines.id
and sales_invoices.price = 0 limit 5", [$billingRunId])), true);

        //Willekeurige normale facturen LIMIT 5
        $randomNormal = json_decode(json_encode(DB::select("
select distinct(sales_invoices.id), sales_invoices.price, sales_invoices.relation_id from sales_invoices, sales_invoice_lines, subscription_lines
where billing_run_id = ?
and sales_invoices.id = sales_invoice_lines.sales_invoice_id
and sales_invoice_lines.subscription_line_id = subscription_lines.id
and sales_invoice_lines.quantity = 1
and sales_invoice_lines.invoice_start != subscription_lines.subscription_start
and sales_invoice_lines.invoice_stop != subscription_lines.subscription_stop
order by rand() limit 5", [$billingRunId])), true);

        $sumInclVat = DB::select("select sum(price_total) as sum from sales_invoices where billing_run_id = ?", [$billingRunId])[0]->sum;
        $sumExclVat = DB::select("select sum(price) as sum from sales_invoices where billing_run_id = ?", [$billingRunId])[0]->sum;

        $invoiceLists = [
            ['name' => 'First invoices', 'values' => $firstInvoices],
            ['name' => 'Last invoices', 'values' => $lastInvoices],
            ['name' => 'Invoices with highest total', 'values' => $highestAmount],
            ['name' => 'Invoices with lowest total above 0', 'values' => $lowestAboveZero],
            ['name' => 'Invoices with lowest total', 'values' => $lowestAmount],
            ['name' => 'Invoices with more than one month billed', 'values' => $catchUp],
            ['name' => 'Deposit invoices', 'values' => $deposit],
            ['name' => 'Invoices with Total 0', 'values' => $totalZero],
            ['name' => 'Random normal invoices', 'values' => $randomNormal]
        ];

        foreach ($invoiceLists as &$invoiceList) {
            if (empty($invoiceList['values'])) {
                continue;
            }
            foreach ($invoiceList['values'] as &$invoice) {
                if (!$invoice) {
                    continue;
                }
                $invoice['url'] = config('app.front_url') . "/#/subscriptions/{$invoice['relation_id']}/{$invoice['id']}/invoices";
            }
        }

        Logging::information('This is what im trying to mail', $invoiceLists, 17);

        $invoiceCount = SalesInvoice::where('billing_run_id', $billingRunId)->count();
        $subscriptionCount = DB::select("select count(distinct subscription_id) as count from sales_invoice_lines where sales_invoice_id in (select id from sales_invoices where billing_run_id = ?)", [$billingRunId])[0]->count;
        $this->salesInvoiceService->sendInvoiceCheckMail(
            $this->userId,
            $billingRun,
            $invoiceLists,
            $invoiceCount,
            $subscriptionCount,
            $sumExclVat,
            $sumInclVat,
            $this->noPriceFoundMessages
        );
    }
}
