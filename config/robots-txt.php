<?php

return [
    'paths' => [
        'production' => [
            '*' => [
                '/q', // Exclude search
                '/admin' // Exclude admin
            ]
        ],
    ]
];