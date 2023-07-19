<?php
return [
    'modules' => [
        'priceList',
    ],
    'components' => [
        'urlManager' => [
            'rules' => [
                'priceList' => 'priceList/site/index',
            ]
        ]
    ]
];