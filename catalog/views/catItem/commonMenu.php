<?php

$menuPart1 = array(
    array('label' => 'КАТАЛОГ'),
    array('label' => 'Все позиции', 'url' => array('/catalog/catItem/index')),
    array(
        'label' => 'Создать позицию',
        'url' => array('/catalog/catItem/create'),
    ),
    array(
        'label' => 'Скопировать позицию',
        'url' => array('/catalog/catCategory/makeCopy'),
    ),
    array(
        'label' => 'Управление разделами',
        'items' => array(
            array(
                'url' => '/catalog/catCategory/admin',
                'label' => 'Управление разделами',
            ),
            array(
                'url' => '/catalog/catCategory/massItemsMoveBetweenCategories',
                'label' => 'Массовое перемещение',
            ),
            array(
                'url' => '/catalog/catCategory/create',
                'label' => 'Создать раздел',
            ),


        ),
    ),
    array(
        'label' => 'Опции',
        'url' => array('/catalog/catItemOptions/manage'),
    ),
    array(
        'label' => 'Изображения(json)',
        'url' => array('/catalog/catItem/massImages'),
    ),
    array(
        'label' => 'Видео',
        'url' => array('/catalog/catItem/video'),
    ),
    array(


        'url' => '/catalog/catOrder/admin',
        'label' => 'Заказы',



    ),
    array(

        'label' => 'Доставка',
        'items' => array(
            array(
                'url' => '/catalog/catShipment/admin',
                'label' => 'Управление',
            ),
            array(
                'url' => '/catalog/catShipment/create',
                'label' => 'Создать',
            ),
        ),



    ),
    array(
        'label' => 'Дополнительные поля',
        'items' => array(
            array(
                'label' => 'Список полей',
                'url' => array('/catalog/catItemsRow/admin'),
            ),
            array(
                'label' => 'Новое поле',
                'url' => array('/catalog/catItemsRow/create'),
            ),
        ),
    ),

    array(
        'label' => 'Акции',
        'items' => array(
            array(
                'label' => 'Список акций',
                'url' => array('/catalog/promo/admin'),
            ),
            array(
                'label' => 'Создать акцию',
                'url' => array('/catalog/promo/create'),
            ),
        ),
    ),
    array(
        'label' => 'Скидки',
        'items' => array(
            array(
                'label' => 'Список скидок',
                'url' => array('/catalog/discount/admin'),
            ),
            array(
                'label' => 'Создать скидку',
                'url' => array('/catalog/discount/create'),
            ),
        ),
    ),
    array(
        'label' => 'Пересборка',
        'url' => array('/catalog/default/renderImages/action'),
    ),
    array(
        'label' => 'Схемы данных',
        'url' => array('/catalog/catalogAndSchema'),
    ),

    array('label' => 'РАЗДЕЛЫ'),
);

return array_merge($menuPart1, CatCategory::model()->categoriesMenu());