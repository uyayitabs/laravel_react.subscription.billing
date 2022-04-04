<?php

namespace App\Services;

use App\DataViewModels\M7SubscriptionLine;
use App\DataViewModels\SubscriptionSummary;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeprovisioningService
{
    /**
     * Returns a list of M7 subscription lines that have an end date in the past, but have not been deprovisioned.
     *
     */
    public function checkDeprovisioning()
    {
        $subLines = $this->getSubscriptionLines();
        return $this->checkStatus($subLines);
    }

    /**
     * Returns a list of M7 subscription lines that have an end date in the past.
     *
     */
    private function getSubscriptionLines()
    {
        return \Querying::for(M7SubscriptionLine::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->get();
    }

    /**
     * Returns M7 JSON data related to a subscription or subscription line.
     * @param $type
     * @param $id
     * @return array
     */
    private function getM7JsonData($type, $id)
    {
        $query = DB::table('json_data AS j')
            ->select('j.json_data', 'j.created_at')
            ->where('j.backend_api', '=', 'm7');
        if ($type == 'subscr') {
            $query->where(function ($query) use ($id) {
                $query->where('j.subscription_id', '=', $id)->whereNull('j.subscription_line_id');
            });
        }
        if ($type == 'sline') {
            $query->where('j.subscription_line_id', '=', $id);
        }
        $query->orderBy('j.created_at', 'DESC');
        return $query->get()->toArray();
    }

    private function getComments($relationId)
    {
        return DB::table('notes')
            ->select('text')
            ->where('type', '=', 'relations')
            ->where('related_id', '=', $relationId)
            ->get()
            ->toArray();
    }

    private function checkStatus($subLines)
    {
        $list = [];
        $listNotOk = [];
        $subscrDataList = [];
        foreach ($subLines as $subLine) {
            $subLine->subscr_line_start = Carbon::parse($subLine->subscription_line_start)->format("d-m-Y");
            $subLine->subscr_line_end = Carbon::parse($subLine->subscription_line_end)->format("d-m-Y");

            $subscription = Subscription::find($subLine->subscription_id);
            if ($subscription) {
                $subLine->m7CustNumber = $subscription->customer_number; //M7 <CustomerNumber> in json_data (HasJsonDataM7Trait::getCustomerNumberAttribute())
            }

            // Check in comments if subscr is marked as deprovision ok
            $comments = $this->getComments($subLine->relation_id);
            foreach ($comments as $comment) {
                $deprovOkSubscr = $deprovOkSubscrLine = $deprovNotOkSubscr = $deprovNotOkSubscrLine = [];

                preg_match_all('/Deprovisioning OK subscr ' . $subLine->subscription_id . '/', $comment->text, $deprovOkSubscr);
                preg_match_all('/Deprovisioning OK subscr line ' . $subLine->subscription_line_id . '/', $comment->text, $deprovOkSubscrLine);
                preg_match_all('/Deprovisioning NOT OK subscr ' . $subLine->subscription_id . '/', $comment->text, $deprovNotOkSubscr);
                preg_match_all('/Deprovisioning NOT OK subscr line ' . $subLine->subscription_line_id . '/', $comment->text, $deprovNotOkSubscrLine);

                if (
                    (count($deprovOkSubscr) && $deprovOkSubscr[0]) ||
                    (count($deprovOkSubscrLine) && $deprovOkSubscrLine[0]) ||
                    (count($deprovNotOkSubscr) && $deprovNotOkSubscr[0]) ||
                    (count($deprovNotOkSubscrLine) && $deprovNotOkSubscrLine[0])
                ) {
                    $notOk = [];
                    preg_match_all('/NOT OK/', $comment->text, $notOk);
                    if (count($notOk) && $notOk[0]) {
                        $subscrData = $this->getM7JsonData('subscr', $subLine->subscription_id);
                        $listNotOk[] = $subLine;
                    }
                    continue 2;
                }
            }

            $subLineData = $this->getM7JsonData('sline', $subLine->subscription_line_id);

            if (!isset($subscrDataList[$subLine->subscription_id])) {
                $subscrData = $this->getM7JsonData('subscr', $subLine->subscription_id);
                $subscrDataList[$subLine->subscription_id] = $subscrData;
            } else {
                $subscrData = $subscrDataList[$subLine->subscription_id];
            }

            // No sub line data
            if (empty($subLineData)) {
                // No sub line data + no subscr data
                if (empty($subscrData)) {
                    $subLine->result = 'No subscr + subscr line data - probably not provisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // No sub line data, but subscr is provisioned
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Provisioned"')) {
                    $subLine->result = 'Subscr is provisioned, probably still provisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // No sub line data, but subscr is deprovisioned
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Deprovisioned"')) {
                    $subLine->result = 'Subscr is deprovisioned, probably deprovisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // No sub line data, but subscr is failed
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Failed"')) {
                    $subLine->result = 'Subscr failed, probably not provisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // No sub line data, other cases
                $subLine->result = 'Unclear';
                $subLine->needsCheck = true;
                $list[] = $subLine;
                continue;
            } else {
                // If last line contains "Deprovisioned", it's okay
                if (str_contains($subLineData[0]->json_data, '"status": "Deprovisioned"')) {
                    $subLine->result = 'Deprovisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // If last line contains "Provisioned", it's still provisioned
                if (str_contains($subLineData[0]->json_data, '"status": "Provisioned"')) {
                    $subLine->result = 'Still provisioned';
                    $subLine->needsCheck = true;
                    $list[] = $subLine;
                    continue;
                }

                // If last line contains "Failed", it wasn't provisioned
                if (str_contains($subLineData[0]->json_data, '"status": "Failed"')) {
                    $subLine->result = 'Failed provisioning';
                    $subLine->needsCheck = false;
                    continue;
                }

                // If last line contains "Provisioning", it got stuck during provisioning
                if (str_contains($subLineData[0]->json_data, '"status": "Provisioning"')) {
                    $subLine->result = 'Stuck during provisioning';
                    $subLine->needsCheck = false;
                    continue;
                }

                // If last line contains "Deprovisioning", it got stuck during deprovisioning
                if (str_contains($subLineData[0]->json_data, '"status": "Deprovisioning"')) {
                    $subLine->result = 'Stuck during deprovisioning';
                    $subLine->needsCheck = true;
                    $list[] = $subLine;
                    continue;
                }

                // --- Checks using subscr ---

                // Subscr is deprovisioned
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Deprovisioned"')) {
                    $subLine->result = 'Subscr is deprovisioned, probably deprovisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // If subscr data contains "Provisioned", it's still provisioned
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Provisioned"')) {
                    $subLine->result = 'Subscr is provisioned, probably still provisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // If subscr data contains "Disconnected", it's may still be provisioned
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Disconnected"')) {
                    $subLine->result = 'Subscr is disconnected, may still be provisioned';
                    $subLine->needsCheck = true;
                    $list[] = $subLine;
                    continue;
                }

                // If subscr data contains "Failed", it's not provisioned
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Failed"')) {
                    $subLine->result = 'Subscr is failed, not provisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // If subscr data contains "Pending", it's not provisioned
                if (count($subscrData) == 1 && str_contains($subscrData[0]->json_data, '"status": "Pending"')) {
                    $subLine->result = 'Subscr is pending, not provisioned';
                    $subLine->needsCheck = false;
                    continue;
                }

                // Other case
                $subLine->result = 'Unclear';
                $subLine->needsCheck = true;
                $list[] = $subLine;
            }
        }
        unset($subLine);

        $res = new \stdClass();
        $res->main = $list;
        $res->notOk = $listNotOk;
        return $res;
    }

    public function generateCsvFile($filename, $data)
    {
        $filename = storage_path("app/private/reports/$filename");

        $columns = ['Sub. ID', 'Sub. line ID', 'Tenant', 'Description', 'Start date', 'End date', 'Comment', 'Link to subscr', 'CD cust. nr.', 'Customer Number'];

        $fh = fopen($filename, 'a');
        fputcsv($fh, $columns, ';');

        foreach ($data as $row) {
            $values = [
                $row->subscription_id,
                $row->subscription_line_id,
                $row->tenant,
                $row->descr,
                $row->subscr_line_start,
                $row->subscr_line_end,
                $row->result,
                config("app.front_url") . "/#/relations/$row->relation_id/$row->subscription_id/subscriptions",
                $row->m7CustNumber,
                $row->customer_number,
            ];
            fputcsv($fh, $values, ';');
        }
        fclose($fh);

        return $filename;
    }
}
