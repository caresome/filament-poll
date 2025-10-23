<?php

return [

    'navigation' => [
        'group' => 'Content',
        'label' => 'Polls',
    ],

    'forms' => [
        'sections' => [
            'details' => 'Details',
            'settings' => 'Settings',
            'options' => 'Options',
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'text' => 'Text',
            'closes_at' => 'Closes At',
            'active' => 'Active',
            'allow_multiple_choices' => 'Allow Multiple Choices',
            'allow_guest_voting' => 'Allow Guest Voting',
            'show_results_before_voting' => 'Show Results Before Voting',
        ],
        'actions' => [
            'add_option' => 'Add Option',
        ],
    ],

    'tables' => [
        'columns' => [
            'title' => 'Title',
            'total_votes' => 'Total Votes',
            'active' => 'Active',
            'multiple' => 'Multiple',
            'closes' => 'Closes',
            'created_at' => 'Created At',
        ],
        'filters' => [
            'active' => 'Active',
            'closes_from' => 'Closes From',
            'closes_until' => 'Closes Until',
        ],
    ],

    'infolists' => [
        'sections' => [
            'details' => 'Details',
            'settings' => 'Settings',
            'results' => 'Results',
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'total_votes' => 'Total Votes',
            'status' => 'Status',
            'type' => 'Type',
            'closes_at' => 'Closes At',
            'show_results_before_voting' => 'Show Results Before Voting',
            'allow_guest_voting' => 'Allow Guest Voting',
            'created' => 'Created',
            'last_updated' => 'Last Updated',
        ],
        'states' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'yes' => 'Yes',
            'no' => 'No',
            'multiple_choice' => 'Multiple Choice',
            'single_choice' => 'Single Choice',
            'never' => 'Never',
        ],
    ],

    'messages' => [
        'errors' => [
            'login_required' => 'You must be logged in to vote on this poll.',
            'poll_closed' => 'This poll is closed.',
            'already_voted' => 'You have already voted in this poll.',
            'select_at_least_one' => 'Please select at least one option.',
            'select_only_one' => 'You can only select one option.',
        ],
        'info' => [
            'multiple_selection' => 'You can select multiple options',
        ],
    ],

    'badges' => [
        'closed' => 'Closed',
        'closes' => 'Closes',
        'login_required' => 'Login required to vote',
        'guest_voting' => 'Guest Voting',
        'multiple_choice' => 'Multiple Choice',
        'results_visible' => 'Results Visible',
        'ended' => 'Ended',
        'ends' => 'Ends',
        'your_vote' => 'Your vote',
    ],

    'actions' => [
        'back_to_voting' => 'Back to voting',
        'view_results' => 'View Results',
        'submit_vote' => 'Submit Vote',
    ],

    'vote_count' => '{0} :count votes|{1} :count vote|[2,*] :count votes',
    'total_text' => 'total',

];
