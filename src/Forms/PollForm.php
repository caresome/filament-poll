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
                    Section::make(__('filament-poll::filament-poll.forms.sections.details'))
                        ->compact()
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('title')
                                ->label(__('filament-poll::filament-poll.forms.fields.title'))
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Textarea::make('description')
                                ->label(__('filament-poll::filament-poll.forms.fields.description'))
                                ->rows(3)
                                ->columnSpanFull(),

                            DateTimePicker::make('closes_at')
                                ->label(__('filament-poll::filament-poll.forms.fields.closes_at')),
                        ]),
                    Section::make(__('filament-poll::filament-poll.forms.sections.settings'))
                        ->compact()
                        ->columnSpanFull()
                        ->collapsible()
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('filament-poll::filament-poll.forms.fields.active'))
                                ->default(config('filament-poll.defaults.is_active', true)),

                            Toggle::make('multiple_choice')
                                ->label(__('filament-poll::filament-poll.forms.fields.allow_multiple_choices'))
                                ->default(config('filament-poll.defaults.multiple_choice', false)),

                            Toggle::make('allow_guest_voting')
                                ->label(__('filament-poll::filament-poll.forms.fields.allow_guest_voting'))
                                ->default(config('filament-poll.defaults.allow_guest_voting', false)),

                            Toggle::make('show_results_before_voting')
                                ->label(__('filament-poll::filament-poll.forms.fields.show_results_before_voting'))
                                ->default(config('filament-poll.defaults.show_results_before_voting', false)),
                        ])
                        ->columns(2),
                ]),

            Section::make(__('filament-poll::filament-poll.forms.sections.options'))
                ->compact()
                ->schema([
                    Repeater::make('options')
                        ->required()
                        ->relationship()
                        ->simple(
                            TextInput::make('text')
                                ->label(__('filament-poll::filament-poll.forms.fields.text'))
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
                        ->addActionLabel(__('filament-poll::filament-poll.forms.actions.add_option'))
                        ->columnSpanFull(),
                ]),
        ];
    }
}
