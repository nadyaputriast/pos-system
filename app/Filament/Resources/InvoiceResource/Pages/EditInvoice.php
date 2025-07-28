<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected array $productsData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->productsData = $data['products'] ?? [];
        unset($data['products']);
        return $data;
    }

    protected function afterSave(): void
    {
        // Sync ulang data produk ke tabel pivot
        $this->record->products()->sync([]);
        foreach ($this->productsData as $item) {
            $this->record->products()->attach($item['product_id'], [
                'qty' => $item['qty'] ?? 1,
                'subtotal' => $item['subtotal'] ?? 0,
            ]);
        }
    }
}