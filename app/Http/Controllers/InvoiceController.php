<?php

namespace App\Http\Controllers;
use App\Models\Invoice;


class InvoiceController extends Controller
{
    public function print($id)
    {
        $invoice = Invoice::with(['products', 'client'])->findOrFail($id);

        return view('invoices.print', compact('invoice'));
    }
}