<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class PortalSalesInvoiceResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $list;

    public function __construct($resource, $message, $success, $list = false)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->success = $success;
        $this->list = $list;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $invoiceStatus = [];
        if ($this->status) {
            $invoiceStatus['id'] = $this->status->id;
            $invoiceStatus['status'] = $this->status->status;
        }


        // List API resource
        if ($this->list) {
            $firstLine = $this->salesInvoiceLines()->first();
            return [
                'id' => $this->id,
                'invoice_no' => $this->invoice_no,
                'date' => dateFormat($this->date),
                'invoice_status' =>  $invoiceStatus,
                'price_total_inc_vat' => $this->price_total,
                'price_total_exc_vat' => $this->price,
                'download_invoice' => route('portal.invoice_pdf', ['sales_invoice' => $this->id]),
                'invoice_pdf_name' => $this->invoice_filename,
                'start_date' => dateFormat($firstLine->invoice_start),
                'end_date' => dateFormat($firstLine->invoice_stop)
            ];
        } else { // Details API resource
            $invoiceLines = $recurringLines = $oneOffLines = $usageCostLines = [];

            // recurring invoice lines
            foreach ($this->periodicCostLines()->get() as $salesInvoiceLine) {
                $recurringLines[] = [
                    'id' => $salesInvoiceLine->id,
                    'description' => $salesInvoiceLine->description,
                    'start_date' => dateFormat($salesInvoiceLine->invoice_start),
                    'end_date' => dateFormat($salesInvoiceLine->invoice_stop),
                    'price_excl_vat' => $salesInvoiceLine->price,
                    'price_incl_vat' => $salesInvoiceLine->price_total
                ];
            }
            $invoiceLines['recurring']['items'] = $recurringLines;
            $invoiceLines['recurring']['price_excl_vat'] = $this->periodicCostLines()->sum('price');
            $invoiceLines['recurring']['price_inc_vat'] = $this->periodicCostLines()->sum('price_total');

            // one-off invoice lines
            foreach ($this->oneOffCostLines()->get() as $salesInvoiceLine) {
                $oneOffLines[] = [
                    'id' => $salesInvoiceLine->id,
                    'description' => $salesInvoiceLine->description,
                    'start_date' => dateFormat($salesInvoiceLine->invoice_start),
                    'end_date' => dateFormat($salesInvoiceLine->invoice_stop),
                    'price_excl_vat' => $salesInvoiceLine->price,
                    'price_incl_vat' => $salesInvoiceLine->price_total,
                ];
            }
            $invoiceLines['one_off']['items'] = $oneOffLines;
            $invoiceLines['one_off']['price_excl_vat'] = $this->oneOffCostLines()->sum('price');
            $invoiceLines['one_off']['price_inc_vat'] = $this->oneOffCostLines()->sum('price_total');

            // usage costs
            $usageCostInvoiceLine = $this->usageCostLines()->first();
            $usageCostPdfUrl = $usageCostPdfName = '';
            if (!blank($usageCostInvoiceLine)) {
                $usageCostPdfUrl = route('portal.invoice_usage_cost_pdf', ['sales_invoice' => $this->id]);
                $usageCostPdfName = $usageCostInvoiceLine->call_summary_filename;
                foreach ($usageCostInvoiceLine->cdrUsageCosts()->get() as $usageCost) {
                    $sender = $usageCost->sender;
                    if (!Str::contains($sender, ['+'])) {
                        $sender = getE164PhoneNumber($usageCost->sender);
                    }
                    $recipient = $usageCost->recipient;
                    if (!Str::contains($recipient, ['+'])) {
                        $recipient = getE164PhoneNumber($usageCost->recipient);
                    }
                    $usageCostLines[] = [
                        'datetime' => dateFormat($usageCost->datetime, 'Y-m-d H:i:s'),
                        'sender' => $sender,
                        'recipient' => $recipient,
                        'duration' => dateFormat($usageCost->duration, 'H:i:s'),
                        'price_excl_vat' => $usageCost->total_cost,
                        'price_incl_vat' => (1 + $usageCostInvoiceLine->vat_percentage) * $usageCost->total_cost,
                    ];
                }
            }

            $invoiceLines['usage_costs']['items'] = $usageCostLines;
            $invoiceLines['usage_costs']['description'] = !blank($usageCostInvoiceLine) ? $usageCostInvoiceLine->description : null;
            $invoiceLines['usage_costs']['period'] = !blank($usageCostInvoiceLine) ? $usageCostInvoiceLine->period : null;
            $invoiceLines['usage_costs']['price_excl_vat'] = !blank($usageCostInvoiceLine) ? $usageCostInvoiceLine->price : 0;
            $invoiceLines['usage_costs']['price_inc_vat'] = !blank($usageCostInvoiceLine) ? $usageCostInvoiceLine->price_total : 0;

            // Details API resource
            return [
                'id' => $this->id,
                'invoice_no' => $this->invoice_no,
                'date' => dateFormat($this->date),
                'due_date' => dateFormat($this->due_date),
                'description' => $this->description,
                'price_total_excl_vat' => $this->price,
                'price_total_incl_vat' => $this->price_total,
                'invoice_status' => $invoiceStatus,
                'invoice_lines' => $invoiceLines,
                'download_invoice' => route('portal.invoice_pdf', ['sales_invoice' => $this->id]),
                'invoice_pdf_name' => $this->invoice_filename,
                'download_usage_cost' => $usageCostPdfUrl,
                'usage_cost_pdf_name' => $usageCostPdfName,
            ];
        }
    }
}
