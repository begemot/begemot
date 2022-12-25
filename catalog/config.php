<?php
return [
    'import'=>[
        'application.modules.catalog.models.CatItem',
    ],
    'modules' => [
        'catalog' => [
            'tidyConfig' => array(
                'Three' => array(

                    'imageTag' => 'admin'
                ),
                'One' => array(

                    'imageTag' => '4text'
                )
            ),
            'itemLayout' => '//layouts/catalogItemViewLayout',
            'baseLayout' => '//layouts/main'
        ]
    ],
    'components' => [
        'urlManager'=>[
            'rules' => [
                //модуль catalog
                '/catalog' => '/catalog/site/',
                'catalog/<title:[\w-]+>_<catId:\d+>' => 'catalog/site/RCategoryView',
                'catalog/<catName:[\w-]+>_<catId:\d+>' => 'catalog/site/RCategoryView',
                'catalog/<title:[\w-]+>_<catId:\d+>/<itemName:[\w\-\"\'\.\"]+>_<item:\d+>' => 'catalog/site/itemView',
            ]
        ]
    ]
];