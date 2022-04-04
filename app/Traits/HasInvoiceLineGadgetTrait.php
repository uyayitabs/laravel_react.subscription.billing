<?php

namespace App\Traits;

use Logging;

trait HasInvoiceLineGadgetTrait
{
    public function getHasGadgetAttribute()
    {
        $lineType = (int) $this->sales_invoice_line_type;
        $cdrCount = $this->cdrUsageCosts()->count();
        return ($lineType == 8 && $cdrCount > 0);
    }

    public function getGadgetsAttribute()
    {
        $gadgets = [];
        try {
            switch ((int) $this->sales_invoice_line_type) {
                    // cdr_usage_cost
                case 8:
                    $gadgetUrl = config('app.url');
                    $gadgetUrl .=  "/api/sales_invoice_lines/{$this->id}/gadget/cdr/SendEmail";

                    $data[] = $this->gadgetMenu(
                        'Send to customer',
                        'Confirm',
                        [
                            'label' => 'Send to customer',
                            'url' =>  $gadgetUrl,
                            'msg' => 'Send call details to customer?',
                            'show_success_popup' => true
                        ]
                    );
                    $hasGadget = true;

                    $gadgets[] = [
                        'label' => '',
                        'data' => $data,
                        'type' => 'DropdownMenu'
                    ];

                    break;
            }
        } catch (\Exception $e) {
            Logging::exception(
                $e->getMessage(),
                1,
                1,
                $this->salesInvoice->tenant_id
            );
        }
        return $gadgets;
    }
}
