<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->url(fn() => route('filament.admin.resources.clients.index'))
                    ->sortable()
                    ->searchable()
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('invoice.id')
                    ->label('Invoice')
                    ->formatStateUsing(fn($state) => 'Invoice #' . $state)
                    ->url(fn() => route('filament.admin.resources.invoices.index'))
                    ->sortable()
                    ->searchable()
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state)),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Komentar')
                    ->limit(80),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]); // Tidak ada bulk action
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedback::route('/'),
        ];
    }
}