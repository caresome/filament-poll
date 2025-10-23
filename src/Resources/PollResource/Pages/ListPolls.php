<?php

namespace Caresome\FilamentPoll\Resources\PollResource\Pages;

use Caresome\FilamentPoll\Resources\PollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPolls extends ListRecords
{
    protected static string $resource = PollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
