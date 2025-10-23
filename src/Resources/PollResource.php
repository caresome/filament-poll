<?php

namespace Caresome\FilamentPoll\Resources;

use Caresome\FilamentPoll\Forms\PollForm;
use Caresome\FilamentPoll\Infolists\PollInfolist;
use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Resources\PollResource\Pages;
use Caresome\FilamentPoll\Tables\PollTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PollResource extends Resource
{
    protected static ?string $model = Poll::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(PollForm::schema());
    }

    public static function table(Table $table): Table
    {
        return PollTable::make($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components(PollInfolist::schema());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolls::route('/'),
            'create' => Pages\CreatePoll::route('/create'),
            'view' => Pages\ViewPoll::route('/{record}'),
            'edit' => Pages\EditPoll::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return config('poll.navigation.label', 'Polls');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('poll.navigation.group', 'Content');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('poll.navigation.icon', 'heroicon-o-chart-bar');
    }

    public static function getNavigationSort(): ?int
    {
        return config('poll.navigation.sort');
    }
}
