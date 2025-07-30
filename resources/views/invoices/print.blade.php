<!-- filepath: resources/views/invoices/print.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f3f4f6;
            color: #333;
            padding: 2rem;
        }
        .invoice-container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2.5rem;
        }
        .logo {
            width: 128px;
            height: auto;
        }
        h2 {
            color: #02aaf1;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        th, td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        thead {
            background: #02aaf1;
            color: #fff;
        }
        .summary {
            background: #02aaf1;
            color: #fff;
            font-weight: bold;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .font-semibold {
            font-weight: 600;
        }
        .grand-total {
            background: #02aaf1;
            color: #fff;
            font-weight: bold;
            padding: 0.75rem;
            border-radius: 0.25rem;
        }
        .signature {
            text-align: right;
            margin-top: 3rem;
        }
        .signature img {
            width: 120px;
        }
        .footer {
            margin-top: 2.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        .print-btn {
            display: block;
            margin: 2rem auto 0 auto;
            background: #02aaf1;
            color: #fff;
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
            }
            .invoice-container {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .footer {
                color: #000 !important;
            }
            .print-btn, .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div>
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
                <h2>INVOICE</h2>
                <p>Invoice No: <strong>#{{ $invoice->id }}</strong></p>
                <p>Date: {{ $invoice->created_at->format('d-m-Y') }}</p>
            </div>
            <div>
                <p style="font-weight: bold;">Invoice to:</p>
                <p>{{ $invoice->client->name }}</p>
                <p>{{ $invoice->client->email }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align:right;">Unit Price</th>
                    <th style="text-align:right;">Qty</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->products as $product)
                <tr>
                    <td>
                        {{ $product->name }}<br>
                        <span style="font-size: 0.9em; color: #666;">{{ $product->pivot->description }}</span>
                    </td>
                    <td style="text-align:right;">Rp {{ number_format($product->price, 2, ',', '.') }}</td>
                    <td style="text-align:right;">{{ $product->pivot->qty }}</td>
                    <td style="text-align:right;">Rp {{ number_format($product->pivot->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div>
            <div class="flex-between">
                <span class="font-semibold">Tax (PPN 11%):</span>
                <span>Rp {{ number_format($invoice->ppn, 2, ',', '.') }}</span>
            </div>
            <div class="flex-between">
                <span class="font-semibold">Tax (PPH 5%):</span>
                <span>Rp {{ number_format($invoice->pph, 2, ',', '.') }}</span>
            </div>
            <div class="flex-between">
                <span class="font-semibold">Discount:</span>
                <span>Rp {{ number_format($invoice->discount ?? 0, 2, ',', '.') }}</span>
            </div>
            <div class="grand-total flex-between" style="margin-top:1rem;">
                <span>Grand Total:</span>
                <span>Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <p style="margin-bottom: 2rem;">
            <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}
            @if($invoice->bank_name)
                ({{ $invoice->bank_name }})
            @endif
        </p>

        <div class="signature">
            <p style="margin-bottom: 10px;">Authorized Signature:</p>
            <img src="{{ asset('img/ttd_direktur.png') }}" alt="Tanda Tangan">
            <p style="font-weight: bold;">{{ $invoice->authorized_by ?? 'Manager' }}</p>
        </div>

        <div class="footer">
            <p>CV. Sinar Teknologi Indonesia -  Jl. Diponegoro No.165a, Dauh Puri Klod, Kec. Denpasar Bar., Kota Denpasar, Bali, 80114.</p>
            <p>Telp: +62 821-4440-9789 | Email: sintekstudio@gmail.com</p>
        </div>
    </div>

    <button onclick="window.print()" class="print-btn no-print">
        🖨️ Cetak Invoice
    </button>
</body>
</html>