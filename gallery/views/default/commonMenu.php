<?php

$menuPart1 =

    array(
        array('label' => 'Управление'),
        array('label' => 'Список галлерей', 'url' => array('/gallery/default/admin')),
        array('label' => 'Создать галлерею', 'url' => array('/gallery/default/create')),


    );


$allGallery = Gallery::model()->findAll();
$menuPart2 = array(
     array('label' => 'Навигация')
);
foreach ($allGallery as $galleryModel) {
    $menuPart2[] = array(
        'label' => $galleryModel->name,
        'url' => array
            ('/gallery/default/manageGallery',
            'id'=>$galleryModel->id));

}

return array_merge($menuPart1, $menuPart2);
?>
