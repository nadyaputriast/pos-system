<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($id)
    {
        $invoice = Invoice::with(['products', 'client', 'lastPayment'])->findOrFail($id);
        $pdf = Pdf::loadView('invoices.print', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }

    public function print($id)
    {
        $invoice = Invoice::with(['products', 'client'])->findOrFail($id);
        $pdf = Pdf::loadView('invoices.print', compact('invoice'));
        return $pdf->stream('invoice-' . $invoice->id . '.pdf'); // tampil langsung di browser
        // atau:
        // return $pdf->download('invoice-'.$invoice->id.'.pdf'); // untuk download langsung
    }
}
