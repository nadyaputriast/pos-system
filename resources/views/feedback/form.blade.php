<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Feedback Produk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background: #f7f7f7; font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0; }
        .container {
            max-width: 520px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px #e0e0e0;
            padding: 32px 28px 24px 28px;
        }
        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }
        .header img {
            height: 128px;
        }
        h2 { color: #2d3748; margin-bottom: 10px; }
        .product-block {
            background: #f1f5f9; border-radius: 6px; padding: 14px 16px; margin-bottom: 22px;
        }
        .product-block strong { color: #2563eb; }
        label { display: block; margin-top: 12px; color: #444; font-weight: 500; }
        textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-top: 6px;
            font-size: 15px; background: #fafafa; resize: vertical; min-height: 60px;
        }
        .star-rating {
            direction: rtl; display: inline-flex; font-size: 22px; margin-top: 6px;
        }
        .star-rating input[type="radio"] { display: none; }
        .star-rating label {
            color: #ddd; cursor: pointer; transition: color 0.2s;
        }
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #fbbf24;
        }
        button {
            margin-top: 22px; width: 100%; background: #3182ce; color: #fff; border: none;
            padding: 12px 0; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background: #2563eb; }
        .success { color: #16a34a; margin-bottom: 10px; }
        .error { color: #dc2626; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo">
            <h2>Feedback Produk</h2>
        </div>
        <div style="margin-bottom: 18px;">
            <strong>Invoice #{{ $invoice->id }}</strong><br>
            Client: {{ $invoice->client->name }}
        </div>
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $err)
                    {{ $err }}<br>
                @endforeach
            </div>
        @endif
        <form method="POST" action="/feedback">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            <input type="hidden" name="client_id" value="{{ $invoice->client_id }}">
            @foreach($invoice->products as $product)
                <div class="product-block">
                    <strong>{{ $product->name }}</strong>
                    <div>Qty: {{ $product->pivot->qty }}</div>
                    <input type="hidden" name="products[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                    <label>Rating:</label>
                    <span class="star-rating">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}-{{ $product->id }}" name="products[{{ $product->id }}][rating]" value="{{ $i }}" required>
                            <label for="star{{ $i }}-{{ $product->id }}">&#9733;</label>
                        @endfor
                    </span>
                    <label>Komentar:</label>
                    <textarea name="products[{{ $product->id }}][comment]" placeholder="Tulis komentar untuk produk ini..."></textarea>
                </div>
            @endforeach
            <button type="submit">Kirim Semua Feedback</button>
        </form>
    </div>
</body>
</html>