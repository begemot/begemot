<?php
/* @var $this ParsersCategoryConnectionController */
/* @var $model ParsersCategoryConnection */

$this->breadcrumbs=array(
	'Parsers Category Connections'=>array('index'),
	'Создание',
);

require(dirname(__FILE__).'/../menu.php');
?>

<h1>Create ParsersCategoryConnection</h1>

<?php $this->widget("bootstrap.widgets.TbMenu", array(
    "type"=>"tabs", // "", "tabs", "pills" (or "list")
    "stacked"=>false, // whether this is a stacked menu
    "items"=>array(
        array("label"=>"Изменились", "url"=>"/parsers/default/do/filename/".$filename . "/tab/changed", "active"=>$tab=="changed"),
        array("label"=>"Новые изображения", "url"=>"/parsers/default/do/file/".$filename . "/tab/changedImages", "active"=>$tab=="changedImages"),
        array("label"=>"Игнорируемые изображения", "url"=>"/parsers/default/do/file/".$filename . "/tab/ignoredImages", "active"=>$tab=="ignoredImages"),
        array("label"=>"Новые", "url"=>"/parsers/default/do/file/".$filename . "/tab/new", "active"=>$tab=="new"),
        array("label"=>"Новые с возможностью связать по ID", "url"=>"/parsers/default/do/file/".$filename . "/tab/newWithId", "active"=>$tab=="newWithId"),
        array("label"=>"Все связи", "url"=>"/parsers/default/do/file/".$filename . "/tab/allSynched", "active"=>$tab=="allSynched" ),
        array("label"=>"Связи с категориями", "url"=>"/parsers/default/do/file/".$filename . "/tab/categorySync", "active"=>$tab=="categorySync" ),
        array('label' => 'Cоздать новую связанную категорию для ' . $filename , 'url'=>array('/parsers/parsersCategoryConnection/createFor/filename/' . $filename), 'active' => $tab=="createFor")


    ),
)); ?>



<?php $this->renderPartial('_form', array('model'=>$model, 'groups' => $groups)); ?>


<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'parsers-category-connection-grid',
	'dataProvider'=>$data->search(),
	'filter'=>$data,
	'type'=>'striped bordered condensed',
	'columns'=>array(
		'id',
		'connect_name',
		'category_id',
		array(      
            'header'=>'Название категории',
            'value'=>'$data->category->name'
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
