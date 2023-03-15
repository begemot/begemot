<?php
$this->menu = CatalogModule::getMenu();
/** @var CatItem $model */
/** @var CActiveDataProvider $dataProvider */

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'test-grid',
    'dataProvider' => $dataProvider,
    'filter' => $model,


    'type' => 'striped bordered condensed',
    'columns' => array(
        'id', 'name',
        'apgId'=>['value'=>'$data->schemaGet("apgId")'],
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
         //   'viewButtonUrl' => 'Yii::app()->urlManager->createUrl(\'catalog/site/itemView\',array(\'title\'=>\'tmp_name\',\'catId\'=>$data->catId,\'itemName\'=>$data->name_t,\'item\'=>$data->id,))',
            'viewButtonOptions' => array('target' => '_blank'),
            'template'=>'{update}',
            'updateButtonUrl' => 'Yii::app()->controller->createUrl("catItem/update",array("id"=>$data->id))',

        ),
    ),
));
