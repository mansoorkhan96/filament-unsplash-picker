<?php

return [
    'label' => 'Kies een afbeelding van Unsplash',
    'description' => 'Je kunt 1 foto selecteren.|Je kunt :numberOfSelectableImages foto\'s selecteren.',
    'form' => [
        'fields' => [
            'search' => [
                'placeholder' => 'Afbeelding zoeken',
            ],
            'square_mode' => [
                'label' => 'Vierkante modus',
            ],
        ],
    ],
    'actions' => [
        'next_page' => [
            'label' => 'Volgende',
        ],
        'previous_page' => [
            'label' => 'Vorige',
        ],
    ],
    'no_search_results' => 'Sorry, je zoekopdracht heeft geen resultaten opgeleverd.<br>Probeer een andere zoekopdracht.',
];
