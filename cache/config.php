<?php
return [
    'modules' => [
        'cache',
    ],
    'components' => [
        'cache' => array(
            'class' => 'CMemCache',
            'servers' => array(
                array(
                    'host' => 'memcached',
                    'port' => 11211,
                    // 'weight' => 60,
                ),
            ),
        ),
    ]
];