<?php

namespace Caresome\FilamentPoll\Resources\PollResource\Pages;

use Caresome\FilamentPoll\Resources\PollResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoll extends EditRecord
{
    protected static string $resource = PollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
