<?php

$menuPart1 = array(
    array('label' => 'Управление ценами'),
//    array('label'=>'Все цены', 'url'=>array('/priceList/prices/admin')),
    array('label'=>'Создать позицию с ценой', 'url'=>array('/priceList/prices/create')),
    array('label' => 'Управление разделами'),
    array('label'=>'Список разделов цен', 'url'=>array('/priceList/priceCats/admin')),
    array('label'=>'Создать раздел цен', 'url'=>array('/priceList/priceCats/create')),
);


return array_merge($menuPart1);