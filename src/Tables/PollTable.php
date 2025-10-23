<?php

namespace Caresome\FilamentPoll\Tables;

use Caresome\FilamentPoll\Models\Poll;
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
                ->description(fn (Poll $record) => str()->limit($record->description, 50))
                ->label(__('filament-poll::filament-poll.tables.columns.title'))
                ->searchable()
                ->sortable()
                ->weight(FontWeight::Medium),

            TextColumn::make('total_votes')
                ->label(__('filament-poll::filament-poll.tables.columns.total_votes'))
                ->sortable(),

            IconColumn::make('is_active')
                ->label(__('filament-poll::filament-poll.tables.columns.active'))
                ->boolean()
                ->sortable(),

            IconColumn::make('multiple_choice')
                ->label(__('filament-poll::filament-poll.tables.columns.multiple'))
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('closes_at')
                ->placeholder('-')
                ->label(__('filament-poll::filament-poll.tables.columns.closes'))
                ->dateTime('M d, Y h:i A')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('created_at')
                ->label(__('filament-poll::filament-poll.tables.columns.created_at'))
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
                ->label(__('filament-poll::filament-poll.tables.filters.active')),

            Filter::make('closes_at')
                ->schema([
                    DatePicker::make('closes_from')
                        ->label(__('filament-poll::filament-poll.tables.filters.closes_from')),
                    DatePicker::make('closes_until')
                        ->label(__('filament-poll::filament-poll.tables.filters.closes_until')),
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
