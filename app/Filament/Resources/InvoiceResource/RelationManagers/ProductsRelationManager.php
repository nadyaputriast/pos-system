<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Produk'),
                Tables\Columns\TextColumn::make('pivot.qty')->label('Qty'),
                Tables\Columns\TextColumn::make('pivot.subtotal')->label('Subtotal')->money('IDR', true),
            ]);
    }
}