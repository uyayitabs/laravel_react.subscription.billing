<?php

namespace App\Console\Commands;

use App\Mail\DirectDebitReversalReport;
use App\Mail\ManualPaymentReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Mail;

class ReportPayments extends Command
{
    protected $signature = 'grid:report-payments';

    protected $description = 'Reports payments (direct debit reversals + manual payments)';

    public function handle(): void
    {
        $lastCheckedDate = Carbon::parse(now()->yesterday()->format('Y-m-d 00:00:00'), 'UTC')
            ->setTimezone('Europe/Amsterdam');
        $this->reportDirectDebitReversals($lastCheckedDate->copy());
        $this->reportManualPayments($lastCheckedDate->copy());
    }

    private function reportDirectDebitReversals($lastCheckedDate): void
    {
        $paymentService = new PaymentService();
        $payments = $paymentService->getDirectDebitReversals($lastCheckedDate);
        $timest = Carbon::parse(now()->format('Y-m-d H:i:s'), 'UTC')
            ->setTimezone('Europe/Amsterdam');

        if (!empty($payments)) {
            $files = [];
            $lastId = 0;
            foreach ($payments as $tenant) {
                foreach ($tenant->payments as $payment) {
                    if ($payment->id > $lastId) {
                        $lastId = $payment->id;
                    }
                }
                $filename = 'Storno_lijst_' . str_replace(' ', '_', $tenant->name) .
                    '_' . $timest->format('d.m.Y_H.i') . '.csv';
                $filename = $paymentService->generateCsvFileReversals($filename, $tenant->payments);
                $files[] = $filename;
            }
            Mail::send(new DirectDebitReversalReport($payments, $timest, $lastCheckedDate, $files));
            // After sending mail, delete the generated CSV file(s) (NOTE: From original code)
            foreach ($files as $filename) {
                unlink($filename);
            }
        } else {
            Mail::send(new DirectDebitReversalReport($payments, $timest, $lastCheckedDate, []));
        }
    }
    private function reportManualPayments($lastCheckedDate): void
    {
        $paymentService = new PaymentService();
        $payments = $paymentService->getManualPayments($lastCheckedDate);

        $timest = Carbon::parse(now()->format('Y-m-d H:i:s'), 'UTC')
            ->setTimezone('Europe/Amsterdam');

        if (!empty($payments)) {
            $files = [];
            foreach ($payments as $tenant) {
                $filename = 'Handmatige_betalingen_' . str_replace(' ', '_', $tenant->name) .
                    '_' . $timest->format('d.m.Y_H.i') . '.csv';
                $filename = $paymentService->generateCsvFileManualPayments($filename, $tenant->payments);
                $files[] = $filename;
            }
            Mail::send(new ManualPaymentReport($payments, $timest, $lastCheckedDate, $files));
            // After sending mail, delete the generated CSV file(s) (NOTE: From original code)
            foreach ($files as $filename) {
                unlink($filename);
            }
        } else {
            Mail::send(new ManualPaymentReport($payments, $timest, $lastCheckedDate, []));
        }
    }
}
