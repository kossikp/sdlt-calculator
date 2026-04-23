<?php

return [
    'standard_bands' => [
        ['up_to' => 125000, 'rate' => 0.00],
        ['up_to' => 250000, 'rate' => 0.02],
        ['up_to' => 925000, 'rate' => 0.05],
        ['up_to' => 1500000, 'rate' => 0.10],
        ['up_to' => null, 'rate' => 0.12],
    ],

    'first_time_buyer' => [
        'cap' => 500000,
        'bands' => [
            ['up_to' => 300000, 'rate' => 0.00],
            ['up_to' => 500000, 'rate' => 0.05],
        ],
    ],

    'additional_property_surcharge' => 0.05,
];
