<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Payment;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected array $productsData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->productsData = $data['products'] ?? [];
        unset($data['products']);

        // Hapus field pembayaran awal agar tidak masuk ke tabel invoices
        unset(
            $data['payment_amount'],
            $data['payment_method'],
            $data['bank_name'],
            // $data['payment_proof'], // Remove the comment - this needs to be unset!
            $data['payment_note']
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->productsData)) {
            $this->record->products()->sync([]);
            foreach ($this->productsData as $item) {
                $this->record->products()->attach($item['product_id'], [
                    'qty' => $item['qty'] ?? 1,
                    'subtotal' => $item['subtotal'] ?? 0,
                ]);
            }
        }

        $data = $this->data;

        if (($data['payment_amount'] ?? 0) > 0) {
            // Ambil payment_proof dari raw state jika null
            $paymentProof = $data['payment_proof'] ?? null;
            if (is_array($paymentProof)) {
                $paymentProof = $paymentProof[0] ?? null;
            }

            Payment::create([
                'invoice_id'      => $this->record->id,
                'amount'          => $data['payment_amount'],
                'payment_method'  => $data['payment_method'],
                'bank_name'       => $data['bank_name'] ?? null,
                'payment_proof'   => $paymentProof,
                'paid_at'         => now(),
            ]);
        }
    }
}
