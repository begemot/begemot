<?php

$menuPart1 = array(
    array('label' => 'Управление разделами'),
    array('label'=>'Список разделов цен', 'url'=>array('/priceList/priceCats/admin')),
    array('label'=>'Создать раздел цен', 'url'=>array('/priceList/priceCats/create')),
);


return array_merge($menuPart1);