<?php
$breadcrumbsArray = array(
    'Каталог' => array('admin'),
);


//print_r($model->getCategory($pid));
$crumbsElements = $model->getBreadCrumbs($pid);

foreach ($crumbsElements as $element){
      if ($element['pid']!=-1)
          $breadcrumbsArray[$element['name']]=array('admin','pid'=>$element['pid']); 
      else
          $breadcrumbsArray[$element['name']]=array('admin');   

}

$this->breadcrumbs = $breadcrumbsArray;

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('cat-category-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1>Разделы каталога: <?php echo $model->getCatname($pid); ?></h1>

<?php 
if ($pid!=-1)
echo CHtml::link('Вернуться в раздел "'.$model->getCatName($pid).'"','/catalog/ '.Yii::app()->createUrl($this->id.'/admin',array('pid'=>$model->getPid($pid))));
?>



<?php

Yii::import('begemot.extensions.grid.EImageColumn');

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'cat-category-grid',
    'dataProvider' => $model->search($pid),
   // 'filter' => $model,
    'type'=>'striped bordered condensed',    
    'columns' => array(
        array(
            'class' => 'EImageColumn',
            'htmlOptions'=>array('width'=>120),
            // see below.
            'imagePathExpression' => '$data->getCatMainPicture()',
            // Text used when cell is empty.
            // Optional.
            'emptyText' => '—',
            // HTML options for image tag. Optional.
            'imageOptions' => array(
                'alt' => 'no',
                'width' => 120,
                'height' => 120,
            ),
        ),
  
        array(

            "name"=>"name",
            'type' => 'raw',
            "header"=>"Категории",
            "value"=>'CHtml::link($data->name."(".$data->getCatChildsCount($data->id).")",Yii::app()->createUrl("catalog/'.$this->id.'/admin",array("pid"=>$data->id)))',
          ),
     
        /*
          'dateCreate',
          'dateUpdate',
          'status',
          'tName',
         */
        array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'updateButtonUrl'=>'"/catalog/catCategory/update/id/".$data->id',
        ),
        array(
            'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
            "header"=>"порядок",
        ),
    ),
));
?>
