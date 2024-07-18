<?php
return [
    'modules' => [
        'stat',
    ],
    'components' => array(
        'urlManager' => array(

            'rules' => array(
                'stat/metrika/delete/<id:\d+>' => 'stat/metrika/delete',
                // Другие правила...
            ),
        )
    )
];
