<?php
namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected array $productsData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->productsData = $data['products'] ?? [];
        unset($data['products']);
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
    }
}