<!-- filepath: resources/views/invoices/print.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 120px 30px 80px 30px;
        }

        body {
            font-family: sans-serif;
            color: #333;
            margin: 0;
        }

        .header,
        .footer {
            width: 100%;
            position: fixed;
            left: 0;
            right: 0;
            background: #fff;
        }

        .header {
            top: 0;
            height: 110px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .footer {
            bottom: 0;
            height: 70px;
            font-size: 0.9rem;
            color: #666;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 10px;
        }

        .content {
            margin-top: 120px;
            margin-bottom: 80px;
        }

        .logo {
            width: 120px;
            height: auto;
        }

        h2 {
            color: #02aaf1;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 0 0.5rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        th,
        td {
            padding: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        thead {
            background: #02aaf1;
            color: #fff;
        }

        .grand-total {
            background: #02aaf1;
            color: #fff;
            font-weight: bold;
            padding: 0.75rem;
            border-radius: 0.25rem;
            text-align: right;
            margin-top: 1rem;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .summary-table td {
            border: none;
            padding: 0.25rem 0.5rem;
            font-size: 1rem;
        }

        .summary-label {
            text-align: right;
            font-weight: bold;
            width: 80%;
        }

        .summary-value {
            text-align: right;
            width: 20%;
        }

        .signature {
            text-align: right;
            margin-top: 2rem;
        }

        .signature img {
            width: 100px;
        }
    </style>
</head>

<body>
    <div class="header">
        <table width="100%" style="border: none;">
            <tr>
                <td style="width:130px; vertical-align:top; border:none;">
                    <img src="{{ public_path('storage/img/logo.png') }}" alt="Logo" class="logo">
                </td>
                <td style="vertical-align:top; border:none;">
                    <h2>INVOICE</h2>
                    <p style="margin:0;">Invoice No: <strong>#{{ $invoice->id }}</strong></p>
                    <p style="margin:0;">Date: {{ $invoice->created_at->format('d-m-Y') }}</p>
                </td>
                <td style="vertical-align:top; text-align:right; border:none;">
                    <p style="font-weight: bold; margin:0;">Invoice to:</p>
                    <p style="margin:0;">{{ $invoice->client->name }}</p>
                    <p style="margin:0;">{{ $invoice->client->email }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div style="width:100%;">
            <p style="margin:0;">CV. Sinar Teknologi Indonesia - Jl. Diponegoro No.165a, Dauh Puri Klod, Kec. Denpasar
                Bar., Kota Denpasar, Bali, 80114.</p>
            <p style="margin:0;">Telp: +62 821-4440-9789 | Email: sintekstudio@gmail.com</p>
        </div>
    </div>

    <div class="content">
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
                @foreach ($invoice->products->take(15) as $product)
                    <tr>
                        <td>
                            {{ $product->name }}<br>
                            <span style="font-size: 0.9em; color: #666;">{{ $product->pivot->description }}</span>
                        </td>
                        <td style="text-align:right;">Rp {{ number_format($product->price, 2, ',', '.') }}</td>
                        <td style="text-align:right;">{{ $product->pivot->qty }}</td>
                        <td style="text-align:right;">Rp {{ number_format($product->pivot->subtotal, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td class="summary-label">Tax (PPN 11%):</td>
                <td class="summary-value">Rp {{ number_format($invoice->ppn, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Tax (PPH 5%):</td>
                <td class="summary-value">Rp {{ number_format($invoice->pph, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Discount:</td>
                <td class="summary-value">Rp {{ number_format($invoice->discount ?? 0, 2, ',', '.') }}</td>
            </tr>
        </table>

        <div class="grand-total">
            Grand Total: Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}
        </div>

        <p style="margin-bottom: 2rem; margin-top:2rem;">
            <strong>Payment Method:</strong>
            {{ $invoice->lastPayment?->payment_method ? ucfirst(str_replace('_', ' ', $invoice->lastPayment->payment_method)) : '-' }}
            @if ($invoice->lastPayment?->bank_name)
                ({{ $invoice->lastPayment->bank_name }})
            @endif
        </p>

        <div class="signature">
            <p style="margin-bottom: 10px;">Authorized Signature:</p>
            <img src="{{ public_path('storage/img/ttd_direktur.png') }}" alt="Tanda Tangan">
            <p style="font-weight: bold;">{{ $invoice->authorized_by ?? 'Direktur' }}</p>
        </div>
    </div>
</body>

</html>
