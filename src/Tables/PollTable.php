<?php

namespace Caresome\FilamentPoll\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PollTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns(static::columns())
            ->filters(static::filters())
            ->recordActions(static::recordActions())
            ->toolbarActions(static::toolbarActions())
            ->modifyQueryUsing(fn ($query) => $query->withTrashed());
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('title')
                ->searchable()
                ->sortable()
                ->weight(FontWeight::Bold),

            TextColumn::make('total_votes')
                ->label('Total Votes')
                ->sortable(),

            IconColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->sortable(),

            IconColumn::make('multiple_choice')
                ->label('Multiple')
                ->boolean(),

            TextColumn::make('closes_at')
                ->placeholder('-')
                ->label('Closes')
                ->dateTime('M d, Y h:i A')
                ->sortable(),

            TextColumn::make('created_at')
                ->dateTime('M d, Y h:i A')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function filters(): array
    {
        return [
            TrashedFilter::make(),

            TernaryFilter::make('is_active')
                ->label('Active'),

            Filter::make('closes_at')
                ->schema([
                    DatePicker::make('closes_from')
                        ->label('Closes From'),
                    DatePicker::make('closes_until')
                        ->label('Closes Until'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['closes_from'], fn ($q) => $q->whereDate('closes_at', '>=', $data['closes_from']))
                        ->when($data['closes_until'], fn ($q) => $q->whereDate('closes_at', '<=', $data['closes_until']));
                }),
        ];
    }

    public static function recordActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }

    public static function toolbarActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ]),
        ];
    }
}
