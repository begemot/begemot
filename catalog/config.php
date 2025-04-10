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
                'catalog' => 'catalog/site/',
                
           
                'catalog/<catName:[\w-]+>_<catId:\d+>' => 'catalog/site/rCategoryView',
                'catalog/<title:[\w-]+>_<catId:\d+>/<itemName:[\w\-\"\'\.]+>_<item:\d+>/modif/<modifid:\d+>' => 'catalog/site/itemView',
                'catalog/<title:[\w-]+>_<catId:\d+>/<itemName:[\w\-\"\'\.\"]+>_<item:\d+>' => 'catalog/site/itemView',
                
            ]
        ]
    ]
];