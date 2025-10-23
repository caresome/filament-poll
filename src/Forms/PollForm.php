<?php

namespace Caresome\FilamentPoll\Forms;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class PollForm
{
    public static function schema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Section::make('Details')
                        ->compact()
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Textarea::make('description')
                                ->rows(3)
                                ->columnSpanFull(),

                            DateTimePicker::make('closes_at')
                                ->label('Closing Date'),
                        ]),
                    Section::make('Settings')
                        ->compact()
                        ->columnSpanFull()
                        ->collapsible()
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(config('poll.defaults.is_active', true)),

                            Toggle::make('multiple_choice')
                                ->label('Allow Multiple Choices')
                                ->default(config('poll.defaults.multiple_choice', false)),

                            Toggle::make('allow_guest_voting')
                                ->label('Allow Guest Voting')
                                ->default(config('poll.defaults.allow_guest_voting', false)),

                            Toggle::make('show_results_before_voting')
                                ->label('Show Results Before Voting')
                                ->default(config('poll.defaults.show_results_before_voting', false)),
                        ])
                        ->columns(2),
                ]),

            Section::make('Options')
                ->compact()
                ->schema([
                    Repeater::make('options')
                        ->required()
                        ->relationship()
                        ->simple(
                            TextInput::make('text')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull()
                        )
                        ->orderColumn('order')
                        ->reorderable()
                        ->collapsible()
                        ->minItems(2)
                        ->defaultItems(2)
                        ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                        ->addActionLabel('Add Option')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
