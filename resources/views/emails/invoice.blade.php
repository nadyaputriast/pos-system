<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }}</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; padding: 20px; color: #333;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px;">

        <table width="100%" style="margin-bottom: 20px;">
            <tr>
                <td>
                    <h2 style="color: #02aaf1;">INVOICE</h2>
                    <p>Invoice No: <strong>#{{ $invoice->id }}</strong></p>
                    <p>Date: {{ $invoice->created_at->format('d-m-Y') }}</p>
                </td>
                <td align="right">
                    <p style="font-weight: bold;">Invoice to:</p>
                    <p>{{ $invoice->client->name }}</p>
                    <p>{{ $invoice->client->email }}</p>
                </td>
            </tr>
        </table>

        <table width="100%" cellpadding="8" cellspacing="0"  style="border-collapse: collapse; margin-bottom: 20px;">
            <thead style="background-color: #02aaf1; color: white;">
                <tr>
                    <th align="left">Item Description</th>
                    <th align="right">Unit Price</th>
                    <th align="right">Qty</th>
                    <th align="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->products as $product)
                <tr>
                    <td>
                        {{ $product->name }}<br>
                        <small style="color: #666;">{{ $product->pivot->description }}</small>
                    </td>
                    <td align="right">Rp {{ number_format($product->price, 2, ',', '.') }}</td>
                    <td align="right">{{ $product->pivot->qty }}</td>
                    <td align="right">Rp {{ number_format($product->pivot->subtotal, 2, ',', '.') }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>

        <table width="100%" style="margin-bottom: 20px;">
            <tr>
                <td><strong>Tax (PPN 11%)</strong></td>
                <td align="right">Rp {{ number_format($invoice->ppn, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Tax (PPH 5%)</strong></td>
                <td align="right">Rp {{ number_format($invoice->pph, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Discount</strong></td>
                <td align="right">Rp {{ number_format($invoice->discount ?? 0, 2, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #02aaf1; color: white;">
                <td><strong>Grand Total</strong></td>
                <td align="right"><strong>Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}</strong></td>
            </tr>
        </table>

        <p style="margin-bottom: 30px;">
            <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}
            @if($invoice->bank_name)
                ({{ $invoice->bank_name }})
            @endif
        </p>

        <div style="text-align: right; margin-top: 40px;">
            <p style="margin-bottom: 10px;">Authorized Signature:</p>
            <img src="{{ asset('img/ttd_direktur.png') }}" alt="Signature" style="width: 120px; display: inline-block; margin-bottom: 10px;">
            <p style="font-weight: bold;">{{ $invoice->authorized_by ?? 'Direktur' }}</p>
        </div>

        <div style="margin-top: 40px; font-size: 12px; color: #666;">
            <p>CV. Sinar Teknologi Indonesia - Jl. Diponegoro No.165a, Dauh Puri Klod, Denpasar, Bali, 80114.</p>
            <p>Telp: +62 821-4440-9789 | Email: sintekstudio@gmail.com</p>
        </div>
    </div>

</body>
</html>