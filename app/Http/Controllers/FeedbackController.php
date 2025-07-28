<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function showForm(Request $request)
    {
        $invoiceId = $request->query('invoice');
        $productId = $request->query('product');
        $invoice = Invoice::findOrFail($invoiceId);
        $product = Product::findOrFail($productId);

        return view('feedback.form', compact('invoice', 'product'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.rating' => 'required|integer|min:1|max:5',
            'products.*.comment' => 'nullable|string|max:1000',
            'client_id' => 'nullable|exists:clients,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        foreach ($request->products as $productFeedback) {
        Feedback::create([
            'invoice_id' => $request->invoice_id,
            'product_id' => $productFeedback['product_id'],
            'client_id' => $request->client_id,
            'rating' => $productFeedback['rating'],
            'comment' => $productFeedback['comment'] ?? null,
        ]);
    }

        return redirect()->back()->with('success', 'Terima kasih atas feedback Anda!');
    }
}
