<?php

namespace Caresome\FilamentPoll\Resources\PollResource\Pages;

use Caresome\FilamentPoll\Resources\PollResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPoll extends ViewRecord
{
    protected static string $resource = PollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
