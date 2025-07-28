<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 30px;">
    <div style="max-width: 700px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 32px;">
        <h2 style="color: #2d3748;">Invoice #{{ $invoice->id }}</h2>
        <p><strong>Client:</strong> {{ $invoice->client->name }}<br>
        <strong>Email:</strong> {{ $invoice->client->email }}</p>
        <hr>
        <h3 style="margin-bottom: 8px;">Detail Produk</h3>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f1f1f1;">
                    <th align="left">Produk</th>
                    <th align="center">Qty</th>
                    <th align="right">Harga Satuan</th>
                    <th align="right">Subtotal</th>
                    <th align="center">Feedback</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->products as $product)
                <tr style="border-bottom: 1px solid #eee;">
                    <td>{{ $product->name }}</td>
                    <td align="center">{{ $product->pivot->qty }}</td>
                    <td align="right">Rp {{ number_format($product->price, 2, ',', '.') }}</td>
                    <td align="right">Rp {{ number_format($product->pivot->subtotal, 2, ',', '.') }}</td>
                    <td align="center">
                        <a href="{{ url('/feedback?invoice='.$invoice->id.'&product='.$product->id) }}" style="color: #3182ce; text-decoration: underline;">Beri Feedback</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <table width="100%" cellpadding="4" cellspacing="0" style="margin-bottom: 20px;">
            <tr>
                <td align="right"><strong>Total PPN (11%):</strong></td>
                <td align="right">Rp {{ number_format($invoice->ppn, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td align="right"><strong>Total PPH (5%):</strong></td>
                <td align="right">Rp {{ number_format($invoice->pph, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td align="right"><strong>Grand Total:</strong></td>
                <td align="right"><strong>Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}</strong></td>
            </tr>
        </table>
        <p style="font-size: 13px; color: #888;">Tanggal Invoice: {{ $invoice->created_at->format('d M Y') }}</p>
        <p style="font-size: 13px; color: #888;">Status: <strong>{{ ucfirst($invoice->status) }}</strong></p>
        <p style="font-size: 13px; color: #888;">Metode Pembayaran: <strong>{{ ucfirst($invoice->payment_method) }}</strong></p>
        <hr>
        <p>Terima kasih telah berbelanja di <strong>CV. Sinar Teknologi Indonesia</strong>!</p>
    </div>
</body>
</html>