<?php
return [
    'modules' => [
        'company',
    ],
    'components' => [
        'urlManager' => [
            'rules' => [
                'company/<title:[\w-]+>_<departId:\d+>' => 'company/site/depart',
                'employee/<title:[\w-]+>_<empId:\d+>' => 'company/site/emp',
                'employes' => 'company/site/employes',
            ]
        ]
    ]
];
