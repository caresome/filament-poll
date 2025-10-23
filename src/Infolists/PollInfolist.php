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
                    Section::make('Details')
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
                                ->label('Total Votes')
                                ->badge()
                                ->color('success'),
                            Infolists\Components\TextEntry::make('is_active')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                                ->color(fn ($state) => $state ? 'success' : 'danger'),
                            Infolists\Components\TextEntry::make('multiple_choice')
                                ->label('Type')
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? 'Multiple Choice' : 'Single Choice')
                                ->color('info'),
                            Infolists\Components\TextEntry::make('closes_at')
                                ->columnSpanFull()
                                ->label('Closes At')
                                ->dateTime('M d, Y h:i A')
                                ->placeholder('Never'),
                        ])
                        ->columns(3),

                    Section::make('Settings')
                        ->compact()
                        ->columnSpanFull()
                        ->collapsible()
                        ->schema([
                            Infolists\Components\TextEntry::make('show_results_before_voting')
                                ->label('Show Results Before Voting')
                                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'gray'),
                            Infolists\Components\TextEntry::make('allow_guest_voting')
                                ->label('Allow Guest Voting')
                                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'gray'),
                            Infolists\Components\TextEntry::make('created_at')
                                ->label('Created')
                                ->dateTime(),
                            Infolists\Components\TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime('M d, Y h:i A')
                                ->since(),
                        ])
                        ->columns(2),
                ]),

            Section::make('Results')
                ->compact()
                ->schema([
                    Infolists\Components\ViewEntry::make('results')
                        ->view('filament-poll::components.poll-results')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
