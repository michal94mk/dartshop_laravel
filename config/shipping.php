<?php

return [
    'methods' => [
        'courier' => [
            'name' => 'Kurier',
            'description' => 'Dostawa kurierem w ciągu 1-2 dni roboczych',
            'cost' => 15.99,
            'delivery_time' => '1-2 dni robocze',
            'icon' => 'truck'
        ],
        'inpost' => [
            'name' => 'InPost Paczkomaty',
            'description' => 'Dostawa do paczkomatu InPost',
            'cost' => 9.99,
            'delivery_time' => '1-3 dni robocze',
            'icon' => 'archive'
        ],
        'pickup' => [
            'name' => 'Odbiór osobisty',
            'description' => 'Odbiór w naszym sklepie stacjonarnym',
            'cost' => 0.00,
            'delivery_time' => 'Natychmiast',
            'icon' => 'store'
        ],
        'express' => [
            'name' => 'Kurier ekspresowy',
            'description' => 'Dostawa kurierem w ciągu 24 godzin',
            'cost' => 25.99,
            'delivery_time' => '24 godziny',
            'icon' => 'lightning-bolt'
        ]
    ],
    
    'free_shipping_threshold' => 200.00, // Darmowa dostawa powyżej tej kwoty
    
    'default_method' => 'courier'
]; 