<?php

return [
    'title' => 'AI Social',
    'button' => 'AI Social',
    'list' => [
        'title' => 'AI Social',
        'instructions' => 'Here you can configure the social controllers',
        'new' => 'New social controller',
    ],
    'item' => [
        'title' => 'Social controller',
        'name' => [
            'display' => 'Name',
            'instructions' => 'The name of the social network you want to generate content for',
            'placeholder' => 'Instagram',
        ],
        'provider' => [
            'display' => 'Provider',
            'instructions' => 'You can enable providers by modifying your .env',
            'placeholder' => 'No provider',
        ],
        'model' => [
            'display' => 'Model',
            'instructions' => 'The name of the model for the provider you selected',
            'placeholder' => 'gpt-4o-mini',
        ],
        'instructions' => [
            'display' => 'Instructions',
            'instructions' => 'Here you can specify custom instructions for your own site',
            'placeholder' => 'Write a post for this page...',
        ],
    ],

    'generate' => [
        'title' => 'Generate post',
    ],
];
