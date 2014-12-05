<?php
/* @var $this CatItemController */
/* @var $model CatItem */

$this->breadcrumbs = array(
    'Cat Items' => array('index'),
    'Manage',
);

$this->menu = require dirname(__FILE__) . '/../catItem/commonMenu.php';
?>

<h1>Раздел "<?php echo $category->name; ?>"</h1>


<?php
Yii::import('begemot.extensions.grid.EImageColumn');

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'test-grid',
    'dataProvider' => $model->search($id),//$model->search($id),
    'filter' => $model,
    //'filter'=>CatItemsToCat::model(),
    'type' => 'striped bordered condensed',
    'columns' => array(
        'itemId',
        array(
            'class' => 'EImageColumn',
            'htmlOptions' => array('width' => 120),
            // see below.
            'imagePathExpression' => '$data->item->getItemMainPicture()',
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
            'header' => 'Парсится',
            'type' => 'raw',
            'value' => '$data->item->combinedWithParser()',
        ),
        array('name' => 'item_name', 'value' => '$data->item->name'),
        array(
            'header' => 'Переключатель публикации',
            'type'=>'raw',
            'value'=>'$data->item->isPublished()',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'viewButtonUrl' => 'Yii::app()->controller->createUrl("/catalog/site/itemView",array("item"=>$data->itemId, "name_t"=>$data->item->name_t, "catId" => $data->item->catId))',
            'updateButtonUrl' => 'Yii::app()->controller->createUrl("catItem/update",array("id"=>$data->itemId))',
            // 'updateButtonUrl'=>'"catItem/update/id/".$data->itemId',
        ),
        array(
            'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
            'header' => 'порядок',
        ),
    ),
));



?>



<script>
    $(function(){
        $(document).on("click", ".togglePublished", function(){
            var button = $(this);
            $.get('/catalog/catItem/togglePublished/id/' + $(this).attr('data-id'), function(data){
                button.before("<span class='toDelete'>Сохранено<br/></span>");
                setTimeout(function() { button.parent().find(".toDelete").remove() }, 500);
                
            })
        })
    })
</script>