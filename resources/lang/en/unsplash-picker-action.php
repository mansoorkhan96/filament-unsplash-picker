<?php

return [
    'label' => 'Pick from Unsplash',
    'description' => 'You may select 1 photo.|You may select :numberOfSelectableImages photos.',
    'form' => [
        'fields' => [
            'search' => [
                'placeholder' => 'Search photos...',
            ],
            'square_mode' => [
                'label' => 'Square Mode',
            ],
        ],
    ],
    'actions' => [
        'next_page' => [
            'label' => 'Next',
        ],
        'previous_page' => [
            'label' => 'Previous',
        ],
    ],
    'no_search_results' => "Sorry, your search didn't return any results.<br>Please try a different search.",
];
