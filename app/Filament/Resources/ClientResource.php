<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipe Klien')
                    ->options([
                        'perseorangan' => 'Perseorangan',
                        'perusahaan' => 'Perusahaan',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('company')
                    ->label('Nama Perusahaan')
                    ->required(fn($get) => $get('type') === 'perusahaan')
                    ->maxLength(255)
                    ->visible(fn($get) => $get('type') === 'perusahaan')
                    ->default(fn($get) => $get('type') === 'perseorangan' ? 'Perseorangan' : ''),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('company')->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->label('Tipe Klien')
                    ->options([
                        'perseorangan' => 'Perseorangan',
                        'perusahaan' => 'Perusahaan',
                    ])
                    ->query(function ($query, $state) {
                        if ($state === 'perseorangan') {
                            return $query->whereRaw('LOWER(company) = ?', ['perseorangan']);
                        }
                        if ($state === 'perusahaan') {
                            return $query->whereRaw('LOWER(company) != ?', ['perseorangan']);
                        }
                        return $query;
                    }),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
