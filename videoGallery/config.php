<?php
return [
    'modules' => [
        'videoGallery',
    ],
    'components' => [
        'urlManager' => [
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => [
                'api/video-relations' => 'videoGallery/videoRelation/index',
                'api/video-relations/create' => 'videoGallery/videoRelation/create',
                'api/video-relations/<id:\d+>/delete' => 'videoGallery/videoRelation/delete',


          
            ],
        ],
    ],
];
