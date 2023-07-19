<?php
return [
    'modules' => [
        'gallery',
    ],
    'components' => [

        'urlManager' => [

            'rules' => [
                //модуль gallery
                '/photo' => array('gallery/siteGallery/index'),
                '/photo/<title:[\w-]+>_<id:[\w-]+>' => array('gallery/siteGallery/viewGallery'),
            ]
        ]
    ]
];

