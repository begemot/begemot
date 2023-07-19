<?php
/* @var $this DefaultController */


$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
?>

<h1>meta-теги</h1>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Добавить страницу',
    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'small', // null, 'large', 'small' or 'mini'
    'url'=>'newPage'
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'test-grid',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$data,
    'template'=>"{items}\n{pager}",
    'columns'=>array(
        array('name'=>'id', 'header'=>'#'),
        array(
            'name'=>'url',
            'header'=>'url',
            'value'=>"\$data['url']",
            ),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update}{delete}',

             'deleteButtonUrl'=>'"/seo/title/delete/id/".$data["id"]',
             'updateButtonUrl'=>'"/seo/title/update/id/".$data["id"]',
        ),
    ),
)); 



?>