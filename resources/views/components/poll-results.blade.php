@php
    $poll = $getRecord();
@endphp

<x-filament-poll::poll-results-display :poll="$poll" :show-vote-count="true" />
