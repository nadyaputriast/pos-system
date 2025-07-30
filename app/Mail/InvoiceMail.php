<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $subjectLine;

    public function __construct(Invoice $invoice, $subjectLine = null)
    {
        $this->invoice = $invoice;
        $this->subjectLine = $subjectLine ?? 'Invoice #' . $invoice->id;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.invoice');
    }
}