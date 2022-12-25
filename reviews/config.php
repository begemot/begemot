<?php
return [
    'modules' => [
        'reviews',
    ],
    'components' => [
        'urlManager' => [
            'rules' => [
                'reviews' => 'reviews/site/reviewIndex',
            ]
        ]
    ]
];