<?php

namespace App\Console\Commands;

use App\Mail\MissingDirectDebitReport;
use App\Models\SalesInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReportMissingDirectDebit extends Command
{
    protected $signature = 'grid:report-missing-dd';

    protected $description = 'Reports relations with active subscr without bank accounts for direct debit';

    public function handle(): void
    {
        $list = new stdClass();
        $list->invoiced = new stdClass();
        $list->invoiced->title = 'Reeds gefactureerd';
        $list->invoiced->list = [];

        $list->new = new stdClass();
        $list->new->title = 'Nog niet gefactureerd';
        $list->new->list = [];

        // Populate invoiced->list and new->list
        foreach ($this->getData(1) as $item) {
            $item->invoices = $this->getInvoicesForRelation($item->relation_id);
            $item->total = collect($item->invoices)->sum('price_total');
            $item->invoiceCount = collect($item->invoices)->count();
            if ($item->invoiceCount > 0) {
                $list->invoiced->list[] = $item;
            } else {
                $list->new->list[] = $item;
            }
        }

        // Populate concept->list
        $list->concept = new stdClass();
        $list->concept->title = 'Subscription nog niet actief';
        $list->concept->list = [];
        foreach ($this->getData(0) as $item) {
            $item->invoiceCount = 0;
            $item->total = 0;
            $list->concept->list[] = $item;
        }

        // Send email
        Mail::send(new MissingDirectDebitReport($list));
    }
    private function getData($status): array
    {
        $sql = "SELECT
                    r.id AS relation_id, r.`customer_number`, r.`tenant_id`, t.name AS tenant,
                    s.id AS subscr_id, s.subscription_start AS subscr_start,
                    b.`iban`, b.`dd_default` AS bank_direct_debit,
                    (SELECT COUNT(*) FROM bank_accounts WHERE relation_id = r.id AND dd_default = 1) AS dd_bank_accounts
                FROM subscriptions s
                JOIN relations r ON s.`relation_id` = r.`id`
                JOIN tenants t ON t.`id` = r.`tenant_id`
                JOIN `bank_accounts` b ON r.id = b.`relation_id`
                LEFT JOIN payment_conditions p ON r.`payment_condition_id` = p.`id`
                LEFT JOIN payment_conditions p2 ON t.id = p2.`tenant_id` AND p2.`default` = 1
                WHERE s.`status` = $status
                    AND r.`tenant_id` IN (7, 8)
                    AND (p.id = 5 OR (p.id IS NULL AND p2.id = 5))
                    HAVING dd_bank_accounts = 0
                ORDER BY s.subscription_start";
        return DB::select($sql);
    }

    private function getInvoicesForRelation($relId): ?array
    {
        $data = SalesInvoice::where('relation_id', $relId)
            ->get(['id', 'date', 'invoice_no', 'description', 'price_total'])
            ->toArray();
        return json_decode(json_encode($data));
    }
}
