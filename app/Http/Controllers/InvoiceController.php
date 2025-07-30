<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($id)
    {
        $invoice = Invoice::with(['products', 'client'])->findOrFail($id);
        $pdf = Pdf::loadView('invoices.print', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }

    public function print($id)
    {
        $invoice = Invoice::with(['products', 'client'])->findOrFail($id);

        return view('invoices.print', compact('invoice'));
    }
}
