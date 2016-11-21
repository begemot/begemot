<?php
/* @var $this PriceCatsController */
/* @var $model PriceCats */

$this->breadcrumbs = array(
    'Price Cats' => array('index'),
    'Manage',
);

$this->menu = require(dirname(__FILE__) . '/../commonMenu.php');


?>

<h1>Управление категориями цен</h1>


<?php

$dataProvider = $model->search();
$dataProvider->setPagination(false);

$dataProvider->criteria->order = '`level`,`order`';

$priceCats = PriceCats::model()->findAllByAttributes(['pid' => -1], ['order' => '`order`']);
$resultArray = [];

foreach ($priceCats as $priceCat) {
    $resultArray[] = $priceCat;

    $subCats = PriceCats::model()->findAllByAttributes(['pid' => $priceCat->id], ['order' => '`order`']);
    foreach ($subCats as $subCat) {
        $resultArray[] = $subCat;
    }
}

// or using: $rawData=User::model()->findAll();
$dataProvider = new CArrayDataProvider($resultArray, array(
    'id' => 'PriceCats',
//    'sort'=>array(
//        'attributes'=>array(
//            'id', 'username', 'email',
//        ),
//    ),
    'pagination' => false,
));

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'price-cats-grid',
    'dataProvider' => $dataProvider,

    'columns' => array(

        array(
            'class' => 'priceList.CDataNestedColumn',
            'name' => 'name',
            'headerHtmlOptions' => array('style' => 'width: 310px'),
            "value"=>'CHtml::link($data->name,Yii::app()->createUrl("priceList/prices/adminForCat/catId/".$data->id))',
            'type'=>'raw'
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{update}{delete}'
        ),
        array(
            'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
            'header' => 'порядок',
        ),

    ),
)); ?>
