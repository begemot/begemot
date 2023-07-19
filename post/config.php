<?php
return [
    'import' => [
        'application.modules.post.models.Posts',
    ],
    'modules' => [
        'post'
    ],
    'components' => [
        'urlManager' => [
            'rules'=>[
                //модуль post
                'allPosts' => 'post/site/allPosts',
//                'posts' => 'post/site/tagIndex',
                'posts/<title:[\w\-\.]+>_<id:\d+>' => 'post/site/tagIndex',
                'postsView/<title:[\w\-_.]+>_<id:\d+>' => 'post/site/view',
            ]
        ]
    ]
];
