<?php

return [
    'title' => 'Social AI',
    'button' => 'Social AI',
    'list' => [
        'title' => 'Social AI',
        'instructions' => 'Qui puoi configurare i controller social',
        'new' => 'Nuovo controller social',
    ],
    'item' => [
        'title' => 'Controller social',
        'name' => [
            'display' => 'Nome',
            'instructions' => 'Il nome del social network per cui vuoi generare contenuti',
            'placeholder' => 'Instagram',
        ],
        'provider' => [
            'display' => 'Provider',
            'instructions' => 'Puoi abilitare i provider modificando il tuo .env',
            'placeholder' => 'Nessun provider',
        ],
        'model' => [
            'display' => 'Modello',
            'instructions' => 'Il nome del modello per il provider che hai selezionato',
            'placeholder' => 'gpt-4o-mini',
        ],
        'instructions' => [
            'display' => 'Istruzioni',
            'instructions' => 'Qui puoi specificare le istruzioni personalizzate per il tuo sito',
            'placeholder' => 'Scrivi un post per questa pagina...',
        ],
    ],

    'generate' => [
        'title' => 'Genera post',
    ],
];
