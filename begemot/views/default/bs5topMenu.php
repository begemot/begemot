<?php


$begemotMenuArray = array(
    //array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),

    array('label' => 'Система', 'url' => array(''),
        'items' => array(
            array('label' => 'Пользователи', 'url' => array('/user/admin'), 'visible' => Yii::app()->hasModule('user')),
            array('label' => 'Разрешения', 'url' => array('/srbac'), 'visible' => Yii::app()->hasModule('srbac')),
            array('label' => 'Ипорт ролей', 'url' => array('/RolesImport'), 'visible' => Yii::app()->hasModule('RolesImport')),
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

//$configPath = Yii::getPathOfAlias('webroot.protected.config');
//$localMenuFile = $configPath . '/adminLocalMenu.php';
//if (file_exists($localMenuFile)) {
//    $localMenu = require($localMenuFile);
//    array_unshift($begemotMaenuArray, $localMenu);
//}

function checkVisiblesOfAllSubMenu($menuArray)
{
    $booleanResult = false;

    foreach ($menuArray as $menuItem) {
        if ($menuItem['visible'] == true) {
            $booleanResult = true;
            break;
        }
    }
    return $booleanResult;
}

?>
<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Begemot</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">

                <?php foreach ($begemotMenuArray as $menuItem): ?>

                    <?php

                    if (isset($menuItem['visible']) && $menuItem['visible']==false) {
                        continue;
                    }

                    $url = '#';
                    if (isset($menuItem['url'])) {
                        $url = $this->createUrl($menuItem['url'][0], []);
                    }
                    ?>

                    <?php if (!isset($menuItem['items'])): ?>

                        <li class="nav-item ">
                            <a class="nav-link" aria-current="page" href="<?= $url ?>"><?= $menuItem['label'] ?></a>
                        </li>
                    <?php else: ?>
                        <?php
                        if (!checkVisiblesOfAllSubMenu($menuItem['items'])) continue;
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <?= $menuItem['label'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach ($menuItem['items'] as $menuSubItem): ?>
                                    <?php

                                    if (!$menuSubItem['visible']) continue;
                                    $suburl = '#';
                                    if (isset($menuSubItem['url'])) {
                                        $suburl = $this->createUrl($menuSubItem['url'][0], []);
                                    }
                                    ?>
                                    <li><a class="dropdown-item" href="<?= $suburl ?>"><?= $menuSubItem['label'] ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>


                    <?php endif; ?>

                <?php endforeach; ?>

            </ul>
            <!--            <form class="d-flex" role="search">-->
            <!--                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">-->
            <!--                <button class="btn btn-outline-success" type="submit">Search</button>-->
            <!--            </form>-->
        </div>
    </div>
</nav>
