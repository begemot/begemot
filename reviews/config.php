<?php
return [
    'import'=>[
        'application.modules.reviews.models.*'
    ],
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