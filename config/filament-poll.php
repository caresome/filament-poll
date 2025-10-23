<?php

return [
    'table_names' => [
        'polls' => 'polls',
        'poll_options' => 'poll_options',
        'poll_votes' => 'poll_votes',
    ],

    'defaults' => [
        'is_active' => true,
        'allow_guest_voting' => false,
        'multiple_choice' => false,
        'show_results_before_voting' => false,
    ],

    'navigation' => [
        'group' => 'Content',
        'icon' => 'heroicon-o-chart-bar',
        'sort' => null,
        'label' => 'Polls',
    ],
];
