<?php
return array(
    //array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),

    array('label' => 'Система', 'url' => array(''),
        'items' => array(
            array('label' => 'Пользователи', 'url' => array('/user/admin'), 'visible' => Yii::app()->hasModule('user')),
            array('label' => 'Разрешения', 'url' => array('/srbac'), 'visible' => Yii::app()->hasModule('srbac')),
            array('label' => 'Ипорт ролей', 'url' => array('/RolesImport'), 'visible' => Yii::app()->hasModule('RolesImport')),
            array('label' => 'Модули', 'url' => array('/modules'), 'visible' => Yii::app()->hasModule('modules')),
        ),
    ),
    array('label' => 'Контент', 'url' => array(''),
        'items' => array(
            array('label' => 'Каталог', 'url' => array('/catalog/catItem'), 'visible' => Yii::app()->hasModule('catalog')),
            array('label' => 'Статьи', 'url' => array('/post/default/admin'), 'visible' => Yii::app()->hasModule('post')),
            array('label' => 'Редактируемые области', 'url' => array('/pages'), 'visible' => Yii::app()->hasModule('pages')),
            array('label' => 'Переменные', 'url' => array('/vars'), 'visible' => Yii::app()->hasModule('vars')),
            array('label' => 'Слайдер', 'url' => array('/slider/admin'), 'visible' => Yii::app()->hasModule('slider')),
            array('label' => 'Структура компании', 'url' => array('/company/companyDepart/admin'), 'visible' => Yii::app()->hasModule('slider')),
            array('label' => 'Цены', 'url' => array('/priceList/prices/admin'), 'visible' => Yii::app()->hasModule('priceList')),
            array('label' => 'ContentManager', 'url' => array('/contentTask/contentTask/admin'), 'visible' => Yii::app()->hasModule('contentTask')),
            array('label' => 'Schemas', 'url' => array('/schema/default/index'), 'visible' => Yii::app()->hasModule('schema')),
        ),
    ),

    array('label' => 'Другие',
        'items' => array(
            array('label' => 'Миграция', 'url' => array('/migrations'), 'visible' => Yii::app()->hasModule('migrations')),
            array('label' => 'Парсеры', 'url' => array('/parsers'), 'visible' => Yii::app()->hasModule('parsers')),
            array('label' => 'Кронтаб', 'url' => array('/jobs'), 'visible' => Yii::app()->hasModule('jobs')),
        ),
    ),

    array('label' => 'Файлы', 'url' => array('/elfinder'), 'visible' => Yii::app()->hasModule('elfinder')),
    array('label' => 'Сообщения',
        'items' => array(
            array('label' => 'CallBack', 'url' => array('/callback/callback/admin'), 'visible' => Yii::app()->hasModule('callback')),
            array('label' => 'Коменты', 'url' => array('/comments'), 'visible' => Yii::app()->hasModule('comments')),
            array('label' => 'FAQ', 'url' => array('/faq/admin'), 'visible' => Yii::app()->hasModule('faq')),
            array('label' => 'Отзывы', 'url' => array('/reviews/admin'), 'visible' => Yii::app()->hasModule('reviews')),
        ),
    ),
    array('label' => 'Галлерея',
        'items' => array(
            array('url' => array('/gallery'), 'visible' => Yii::app()->hasModule('gallery'), 'label' => 'Фото'),
            array('url' => array('/videoGallery/videoGalleryVideo/admin'), 'label' => 'Видео', 'visible' => Yii::app()->hasModule('videoGallery')),

        ),
    ),

    array('label' => 'SEO',
        'items' => array(
            ['url' => array('/seo/seoPages/admin'), 'visible' => Yii::app()->hasModule('seo'), 'label' => 'Анализ страниц'],
            ['url' => array('/seo/title/index'), 'visible' => Yii::app()->hasModule('seo'), 'label' => 'Редактор мета-тегов']
        )
    ),
    array('label' => 'Login', 'url' => array('/begemot'), 'visible' => Yii::app()->user->isGuest),
    array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/begemot/default/logout'), 'visible' => !Yii::app()->user->isGuest)
);