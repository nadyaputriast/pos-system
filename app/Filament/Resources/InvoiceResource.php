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
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    // Step 1: Client
                    Forms\Components\Wizard\Step::make('Client')
                        ->schema([
                            Forms\Components\Select::make('client_id')
                                ->relationship('client', 'name')
                                ->required(),
                        ]),
                    // Step 2: Products
                    Forms\Components\Wizard\Step::make('Products')
                        ->schema([
                            Forms\Components\Repeater::make('products')
                                ->label('Products')
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Products')
                                        ->options(Product::all()->pluck('name', 'id'))
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $product = Product::find($state);
                                            $set('price', $product?->price ?? 0);
                                            $set('qty', 1);
                                            $set('subtotal', $product?->price ?? 0);

                                            // Update global fields
                                            $items = $get('../../products') ?? [];
                                            $subtotal = collect($items)->sum(
                                                fn($item) => (is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0) *
                                                    (is_numeric($item['qty'] ?? null) ? (float) $item['qty'] : 0)
                                            );
                                            $ppn = round($subtotal * 0.11);
                                            $pph = round($subtotal * 0.05);
                                            $tax = $ppn + $pph;
                                            $grandTotal = $subtotal + $ppn + $pph;

                                            $set('../../subtotal', $subtotal);
                                            $set('../../ppn', $ppn);
                                            $set('../../pph', $pph);
                                            $set('../../tax', $tax);
                                            $set('../../total_amount', $grandTotal);
                                        }),
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

                                            // Update global fields
                                            $items = $get('../../products') ?? [];
                                            $subtotal = collect($items)->sum(
                                                fn($item) => (is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0) *
                                                    (is_numeric($item['qty'] ?? null) ? (float) $item['qty'] : 0)
                                            );
                                            $ppn = round($subtotal * 0.11);
                                            $pph = round($subtotal * 0.05);
                                            $tax = $ppn + $pph;
                                            $grandTotal = $subtotal + $ppn + $pph;

                                            $set('../../subtotal', $subtotal);
                                            $set('../../ppn', $ppn);
                                            $set('../../pph', $pph);
                                            $set('../../tax', $tax);
                                            $set('../../total_amount', $grandTotal);
                                        }),
                                    Forms\Components\TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(true),
                                ])
                                ->createItemButtonLabel('Tambah Produk')
                                ->columns(4)
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $subtotal = collect($state)->sum(
                                        fn($item) => (is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0) *
                                            (is_numeric($item['qty'] ?? null) ? (float) $item['qty'] : 0)
                                    );
                                    $ppn = round($subtotal * 0.11);
                                    $pph = round($subtotal * 0.05);
                                    $tax = $ppn + $pph;
                                    $grandTotal = $subtotal + $ppn + $pph;

                                    $set('../../subtotal', $subtotal);
                                    $set('../../ppn', $ppn);
                                    $set('../../pph', $pph);
                                    $set('../../tax', $tax);
                                    $set('../../total_amount', $grandTotal);
                                }),
                            // Field global di luar repeater
                            Forms\Components\TextInput::make('ppn')
                                ->label('Total PPN (11%)')
                                ->numeric()
                                ->default(0)
                                ->disabled()
                                ->dehydrated(true)
                                ->reactive(),
                            Forms\Components\TextInput::make('pph')
                                ->label('Total PPH (5%)')
                                ->numeric()
                                ->default(0)
                                ->disabled()
                                ->dehydrated(true)
                                ->reactive(),
                            Forms\Components\TextInput::make('tax')
                                ->label('Tax (PPN + PPH)')
                                ->numeric()
                                ->default(0)
                                ->disabled()
                                ->dehydrated(true)
                                ->reactive(),
                            Forms\Components\TextInput::make('total_amount')
                                ->label('Grand Total')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->disabled()
                                ->dehydrated(true)
                                ->reactive(),
                        ]),
                    // Step 3: Pembayaran
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
                                ->required(),

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
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('products_list')
                    ->label('Produk Dibeli')
                    ->getStateUsing(function ($record) {
                        return $record->products
                            ->map(function ($product) {
                                return $product->name . ' (' . $product->pivot->qty . ')';
                            })
                            ->implode(', ');
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('bank_name'),
                Tables\Columns\TextColumn::make('paid_amount')->money('IDR', true),
                Tables\Columns\TextColumn::make('tax')->money('IDR', true),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR', true),
                Tables\Columns\TextColumn::make('deadline')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pelunasan')
                    ->label('Pelunasan')
                    ->icon('heroicon-o-currency-dollar')
                    ->visible(fn($record) => $record->status === 'cicil')
                    ->form([
                        Forms\Components\TextInput::make('sisa_pembayaran')
                            ->label('Sisa Pembayaran')
                            ->numeric()
                            ->disabled()
                            ->default(fn($record) => ($record->total_amount - ($record->paid_amount ?? 0))),
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Transfer',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('bank_name')
                            ->label('Bank')
                            ->options([
                                'BCA' => 'BCA',
                                'BNI' => 'BNI',
                                'BRI' => 'BRI',
                                'Mandiri' => 'Mandiri',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->visible(fn(callable $get) => $get('payment_method') === 'bank_transfer'),
                        Forms\Components\FileUpload::make('payment_proof')
                            ->label('Bukti Transfer')
                            ->directory('payment_proofs')
                            ->maxSize(2048)
                            ->visible(fn(callable $get) => $get('payment_method') === 'bank_transfer'),
                    ])
                    ->action(function ($record, $data) {
                        // Update invoice: lunas, paid_amount, payment_method, bank_name, payment_proof
                        $record->update([
                            'status' => 'lunas',
                            'paid_amount' => $record->total_amount,
                            'payment_method' => $data['payment_method'],
                            'bank_name' => $data['bank_name'] ?? null,
                            'payment_proof' => $data['payment_proof'] ?? null,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->color('success'),
                Tables\Actions\Action::make('kirim')
                    ->label('Kirim Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(function ($record) {
                        $clientEmail = $record->client->email ?? null;
                        if ($clientEmail) {
                            Mail::to($clientEmail)->send(new InvoiceMail($record));
                        }
                    })
                    ->requiresConfirmation()
                    ->color('success'),
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
