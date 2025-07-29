<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 text-gray-800">
   

    <div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-md">
        <div class="flex justify-between items-start mb-10">
            <div>
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-32 h-auto">
                <h2 class="text-3xl font-bold" style="color: #02aaf1;">INVOICE</h2>
                <p>Invoice No: <strong>#{{ $invoice->id }}</strong></p>
                <p>Date: {{ $invoice->created_at->format('d-m-Y') }}</p>
            </div>
            <div>
                <p class="font-semibold">Invoice to:</p>
                <p>{{ $invoice->client->name }}</p>
                <p>{{ $invoice->client->email }}</p>
            </div>
        </div>

        <table class="w-full text-left border-collapse mb-8">
            <thead>
                <tr style="background-color: #02aaf1; color: white;">
                    <th class="p-3">Item Description</th>
                    <th class="p-3 text-right">Unit Price</th>
                    <th class="p-3 text-right">Qty</th>
                    <th class="p-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->products as $product)
                <tr class="border-b">
                    <td class="p-3">
                        {{ $product->name }}<br>
                        <span class="text-sm text-gray-500">{{ $product->pivot->description }}</span>
                    </td>
                    <td class="p-3 text-right">Rp {{ number_format($product->price, 2, ',', '.') }}</td>
                    <td class="p-3 text-right">{{ $product->pivot->qty }}</td>
                    <td class="p-3 text-right">Rp {{ number_format($product->pivot->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mb-6">
            {{-- <div class="flex justify-between mb-2">
                <span class="font-semibold">Sub Total:</span>
                <span>Rp {{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
            </div> --}}
            <div class="flex justify-between mb-2">
                <span class="font-semibold">Tax (PPN 11%):</span>
                <span>Rp {{ number_format($invoice->ppn, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="font-semibold">Tax (PPH 5%):</span>
                <span>Rp {{ number_format($invoice->pph, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="font-semibold">Discount:</span>
                <span>Rp {{ number_format($invoice->discount ?? 0, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold p-3 " style="background-color: #02aaf1; color: white;">
                <span>Grand Total:</span>
                <span>Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <p class="mb-10">
            <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}
            @if($invoice->bank_name)
                ({{ $invoice->bank_name }})
            @endif
        </p>

        <div class="text-right mt-16">
            <p class="mb-4">Authorized Signature:</p>
            
            <div class="inline-block mb-4">
                <img src="{{ asset('img/ttd_direktur.png') }}" alt="Tanda Tangan" class="w-32 h-auto">
            </div>

            <p class="font-semibold">{{ $invoice->authorized_by ?? 'Manager' }}</p>
        </div>

        <div class="text-sm text-gray-500 mt-10">
            <p>CV. Sinar Teknologi Indonesia -  Jl. Diponegoro No.165a, Dauh Puri Klod, Kec. Denpasar Bar., Kota 
denpasar,Bali, 80114.</p>
            <p>Telp: +62 821-4440-9789 | Email: sintekstudio@gmail.com</p>
        </div>
    </div>

     <div class="text-center mb-6 mt-3">
        <button onclick="window.print()" class="hover:bg-[#02aaf1] bg-white text-black py-2 px-4 rounded">
            🖨️ Cetak Invoice
        </button>
    </div>
</body>
</html>