<?php



$this->menu = require(dirname(__FILE__).'/../commonMenu.php');

?>

<h1><?php echo 'Manage' . ' '  ?></h1>


<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
	'id' => 'video-gallery-video-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'id',
		'title',
		array(
				'name'=>'gallery_id',
				'value'=>'GxHtml::valueEx($data->gallery)',
				'filter'=>GxHtml::listDataEx(VideoGallery::model()->findAllAttributes(null, true)),
				),
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>