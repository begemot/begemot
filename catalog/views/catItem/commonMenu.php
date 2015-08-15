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
                'label' => 'Список разделов',
            ),
            array(
                'url' => '/catalog/catCategory/create',
                'label' => 'Создать раздел',
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
    array('label' => 'РАЗДЕЛЫ'),
);

return array_merge($menuPart1, CatCategory::model()->categoriesMenu());

?>
