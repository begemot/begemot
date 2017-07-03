<?php

$menuPart1 = array(
    array('label' => 'Задания'),
    array('label' => 'Все позиции', 'url' => array('/tasks/tasks/admin')),
    array(
        'label' => 'Создать позицию',
        'url' => array('/tasks/tasks/create'),
    ),

    array(
        'label' => 'Лайки и дизлайки',
        'items' => array(
            array(
                'label' => 'Все лайки и дизлайки',
                'url' => array('/tasks/likesAndDislikes/admin'),
            ),
            array(
                'label' => 'Создать лайк или дизлайк',
                'url' => array('/tasks/likesAndDislikes/create'),
            ),
        ),
    ),

    array(
        'label' => 'Подписчики',
        'items' => array(
            array(
                'label' => 'Все подписчики',
                'url' => array('/tasks/subscribers/admin'),
            ),
            array(
                'label' => 'Создать подписчика',
                'url' => array('/tasks/subscribers/create'),
            ),
        ),
    ),

    array(
        'label' => 'Выписки',
        'items' => array(
            array(
                'label' => 'Все выписки',
                'url' => array('/tasks/invoices/admin'),
            ),
            array(
                'label' => 'Создать выписку',
                'url' => array('/tasks/invoices/create'),
            ),
        ),
    ),

    array(
        'label' => 'Пользователи которые хотят выполнить',
        'items' => array(
            array(
                'label' => 'Все пользователи для заданий',
                'url' => array('/tasks/tasksToUser/admin'),
            ),
            array(
                'label' => 'Лайки пользователей',
                'url' => array('/tasks/tasksToUserLikes/admin'),
            ),
            array(
                'label' => 'Создать пользователя для задания',
                'url' => array('/tasks/tasksToUser/create'),
            ),
        ),
    ),


);

return $menuPart1;

?>
