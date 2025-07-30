<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoice:send-reminders';
    protected $description = 'Kirim email reminder invoice H-3, H-2, H-1, dan H sebelum deadline jika belum lunas';

    public function handle()
    {
        $today = Carbon::today();

        foreach ([3, 2, 1, 0] as $h) {
            $targetDate = $today->copy()->addDays($h);
            $invoices = Invoice::where('status', '!=', 'lunas')
                ->whereDate('deadline', $targetDate)
                ->with('client')
                ->get();

            foreach ($invoices as $invoice) {
                if ($invoice->client && $invoice->client->email) {
                    $subject = "Reminder H-{$h}: Invoice #{$invoice->id} jatuh tempo {$invoice->deadline}";
                    if ($h === 0) {
                        $subject = "Reminder HARI H: Invoice #{$invoice->id} jatuh tempo HARI INI!";
                    }
                    Mail::to($invoice->client->email)
                        ->send(new InvoiceMail($invoice, $subject));
                }
            }
        }

        $this->info('Invoice reminders sent.');
    }
}