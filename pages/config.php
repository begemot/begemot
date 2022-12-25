<?php
return [
    'modules' => [
        'pages'
    ],
    'components'=>[
        'urlManager'=>[
            'rules'=>[
                array('class' => 'application.modules.pages.components.PageUrlRule'),
            ]
        ]
    ]
];
