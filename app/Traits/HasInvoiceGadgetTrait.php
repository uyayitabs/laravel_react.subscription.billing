<?php

namespace App\Traits;

use App\Models\CdrUsageCost;
use App\Services\StatusService;

trait HasInvoiceGadgetTrait
{
    public function getHasGadgetAttribute()
    {
        $salesInvoiceMeta = $this->salesInvoiceMetas()->where('key', 'reminder_status')->first();
        return !$salesInvoiceMeta || 'sent_to_collection_agency' != $salesInvoiceMeta->value;
    }

    public function getGadgetsAttribute()
    {
        $gadgets = [];

        $statusService = new StatusService();
        $pdfCreatedStatus = $statusService->getStatusId('invoice', 'Creating PDF');

        if ($this->invoice_status >= $pdfCreatedStatus) {
            $data[] = $this->gadgetMenu(
                'Send invoice email',
                'Confirm',
                [
                    'label' => 'Please confirm',
                    'url' => route('subscriptions.email_invoice', ['invoice_id' => $this->id]),
                    'msg' => 'Are you sure you want to send this invoice by email?',
                    'show_success_popup' => false
                ]
            );
        }

        $salesInvoiceMeta = $this->salesInvoiceMetas()->where('key', 'reminder_status')->first();
        if (!$salesInvoiceMeta || 'sent_to_collection_agency' != $salesInvoiceMeta->value) {
            $salesInvoiceMetaValue = $salesInvoiceMeta ? $salesInvoiceMeta->value : null;
            switch ($salesInvoiceMetaValue) {
                case 'first_reminder_sent':
                    $reminderLabel = 'Send 2nd reminder';
                    break;
                case 'second_reminder_sent':
                    $reminderLabel = 'Send warning';
                    break;
                case 'warning_sent':
                    $reminderLabel = 'Send final notice';
                    break;
                case 'final_notice_sent':
                    $reminderLabel = 'Sent to collection agency';
                    break;
                default:
                    $reminderLabel = 'Send reminder';
                    break;
            }

            $gadgetUrl = route('sales_invoices.send_reminder', ['sales_invoice' => $this->id]);

            if ($salesInvoiceMetaValue == 'final_notice_sent') {
                $data[] = $this->gadgetMenu(
                    $reminderLabel,
                    'Confirm',
                    [
                        'label' => 'Please confirm',
                        'url' =>  $gadgetUrl,
                        'msg' => 'Has this invoice been sent to a collection agency?',
                        'show_success_popup' => false
                    ]
                );
            } else {
                $data[] = $this->gadgetMenu(
                    $reminderLabel,
                    'LinkAction',
                    [
                        'url' =>  $gadgetUrl
                    ]
                );
            }
        }

        $gadgets[] = [
            'label' => '',
            'data' => $data,
            'type' => 'DropdownMenu'
        ];

        return $gadgets;
    }
}
