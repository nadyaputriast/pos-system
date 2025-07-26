<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Client')
                        ->schema([
                            Forms\Components\Select::make('client_id')
                                ->relationship('client', 'name')
                                ->required(),
                        ]),
                    Forms\Components\Wizard\Step::make('Products')
                        ->schema([
                            Forms\Components\Repeater::make('products')
                                ->label('Products')
                                ->relationship('products')
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Products')
                                        ->options(Product::all()->pluck('name', 'id'))
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $product = Product::find($state);
                                            $set('description', $product?->description ?? '');
                                            $set('price', $product?->price ?? 0);
                                            $set('qty', 1);
                                            $set('subtotal', $product?->price ?? 0);
                                        }),
                                    Forms\Components\TextInput::make('description')
                                        ->label('Deskripsi')
                                        ->disabled(),
                                    Forms\Components\TextInput::make('price')
                                        ->label('Harga')
                                        ->numeric()
                                        ->disabled(),
                                    Forms\Components\TextInput::make('qty')
                                        ->label('Qty')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $price = is_numeric($get('price')) ? (float) $get('price') : 0;
                                            $qty = is_numeric($state) ? (float) $state : 0;
                                            $set('subtotal', $price * $qty);
                                        }),
                                    Forms\Components\TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(false),
                                ])
                                ->createItemButtonLabel('Tambah Produk')
                                ->columns(5)
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Hitung total subtotal, ppn, pph dari semua produk
                                    $subtotal = collect($state)->sum(function ($item) {
                                        $price = is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0;
                                        $qty = is_numeric($item['qty'] ?? null) ? (float) $item['qty'] : 0;
                                        return $price * $qty;
                                    });
                                    $ppn = collect($state)->sum(function ($item) {
                                        $price = is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0;
                                        $qty = is_numeric($item['qty'] ?? null) ? (float) $item['qty'] : 0;
                                        return round($price * $qty * 0.11);
                                    });
                                    $pph = collect($state)->sum(function ($item) {
                                        $price = is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0;
                                        $qty = is_numeric($item['qty'] ?? null) ? (float) $item['qty'] : 0;
                                        return round($price * $qty * 0.05);
                                    });
                                    $grandTotal = $subtotal + $ppn + $pph;

                                    // Set ke field global di luar repeater
                                    $set('../../ppn', $ppn);
                                    $set('../../pph', $pph);
                                    $set('../../total_amount', $grandTotal);
                                }),
                            // Field global di luar repeater, masih di schema step Products
                            Forms\Components\TextInput::make('ppn')
                                ->label('Total PPN (11%)')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('pph')
                                ->label('Total PPH (5%)')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('total_amount')
                                ->label('Grand Total')
                                ->numeric()
                                ->required()
                                ->disabled()
                                ->dehydrated(),
                        ]),
                    Forms\Components\Wizard\Step::make('Pembayaran')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'cicil' => 'Cicil',
                                    'lunas' => 'Lunas',
                                ])
                                ->required()
                                ->reactive(),

                            Forms\Components\Select::make('payment_method')
                                ->options([
                                    'bank_transfer' => 'Transfer',
                                    'cash' => 'Cash',
                                ])
                                ->required()
                                ->reactive(),

                            Forms\Components\Select::make('bank_name')
                                ->options([
                                    'BCA' => 'BCA',
                                    'BNI' => 'BNI',
                                    'BRI' => 'BRI',
                                    'Mandiri' => 'Mandiri',
                                    'Lainnya' => 'Lainnya',
                                ])
                                ->visible(fn(callable $get) => $get('payment_method') === 'bank_transfer'),

                            Forms\Components\FileUpload::make('payment_proof')
                                ->directory('payment_proofs')
                                ->maxSize(2048)
                                ->visible(fn(callable $get) => $get('payment_method') === 'bank_transfer'),

                            Forms\Components\DatePicker::make('deadline')
                                ->required()
                                ->visible(fn(callable $get) => $get('status') === 'cicil'),

                            Forms\Components\TextInput::make('paid_amount')
                                ->numeric()
                                ->visible(fn(callable $get) => $get('status') === 'cicil'),
                            Forms\Components\Textarea::make('payment_note')
                                ->maxLength(255),
                        ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR', true),
                Tables\Columns\TextColumn::make('tax')->money('IDR', true),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('bank_name'),
                Tables\Columns\TextColumn::make('paid_amount')->money('IDR', true),
                Tables\Columns\TextColumn::make('ppn'),
                Tables\Columns\TextColumn::make('pph'),
                Tables\Columns\TextColumn::make('deadline')->date(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
