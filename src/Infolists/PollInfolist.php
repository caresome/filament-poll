<?php

namespace Caresome\FilamentPoll\Infolists;

use Filament\Infolists;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;

class PollInfolist
{
    public static function schema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Section::make(__('filament-poll::infolists.sections.details'))
                        ->columnSpanFull()
                        ->compact()
                        ->schema([
                            Infolists\Components\TextEntry::make('title')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->weight(FontWeight::Bold)
                                ->size('lg'),
                            Infolists\Components\TextEntry::make('description')
                                ->hiddenLabel()
                                ->markdown()
                                ->columnSpanFull(),
                            Infolists\Components\TextEntry::make('total_votes')
                                ->label(__('filament-poll::infolists.fields.total_votes'))
                                ->badge()
                                ->color('success'),
                            Infolists\Components\TextEntry::make('is_active')
                                ->label(__('filament-poll::infolists.fields.status'))
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? __('filament-poll::infolists.states.active') : __('filament-poll::infolists.states.inactive'))
                                ->color(fn ($state) => $state ? 'success' : 'danger'),
                            Infolists\Components\TextEntry::make('multiple_choice')
                                ->label(__('filament-poll::infolists.fields.type'))
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? __('filament-poll::infolists.states.multiple_choice') : __('filament-poll::infolists.states.single_choice'))
                                ->color('info'),
                            Infolists\Components\TextEntry::make('closes_at')
                                ->columnSpanFull()
                                ->label(__('filament-poll::infolists.fields.closes_at'))
                                ->dateTime('M d, Y h:i A')
                                ->placeholder(__('filament-poll::infolists.states.never')),
                        ])
                        ->columns(3),

                    Section::make(__('filament-poll::infolists.sections.settings'))
                        ->compact()
                        ->columnSpanFull()
                        ->collapsible()
                        ->schema([
                            Infolists\Components\TextEntry::make('show_results_before_voting')
                                ->label(__('filament-poll::infolists.fields.show_results_before_voting'))
                                ->formatStateUsing(fn ($state) => $state ? __('filament-poll::infolists.states.yes') : __('filament-poll::infolists.states.no'))
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'gray'),
                            Infolists\Components\TextEntry::make('allow_guest_voting')
                                ->label(__('filament-poll::infolists.fields.allow_guest_voting'))
                                ->formatStateUsing(fn ($state) => $state ? __('filament-poll::infolists.states.yes') : __('filament-poll::infolists.states.no'))
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'gray'),
                            Infolists\Components\TextEntry::make('created_at')
                                ->label(__('filament-poll::infolists.fields.created'))
                                ->dateTime(),
                            Infolists\Components\TextEntry::make('updated_at')
                                ->label(__('filament-poll::infolists.fields.last_updated'))
                                ->dateTime('M d, Y h:i A')
                                ->since(),
                        ])
                        ->columns(2),
                ]),

            Section::make(__('filament-poll::infolists.sections.results'))
                ->compact()
                ->schema([
                    Infolists\Components\ViewEntry::make('results')
                        ->view('filament-poll::components.poll-results')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
