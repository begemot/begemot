<?php
/* @var $this CatItemController */
/* @var $model CatItem */
Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/css/styles.css')
);

$this->breadcrumbs = array(
    'Cat Items' => array('index'),
    'Manage',
);

$menu = require dirname(__FILE__) . '/commonMenu.php';

$this->menu = $menu;

$this->menu = require dirname(__FILE__).'/commonMenu.php';

 $massSelectJsScript = '/protected/modules/catalog/assets/js/massSelect.js';
Yii::app()->clientScript->registerScriptFile($massSelectJsScript);
?>

<h1>Все позиции каталога</h1>


<?php
Yii::import('begemot.extensions.grid.EImageColumn');
$js = '
 $(document).ready(function(){
    $(".gridCheckboxCheckAll").click(function(){
      $(".gridCheckbox").click();
    });
 });
';
Yii::app()->clientScript->registerScript('checkBoxScript',$js,1);

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'test-grid',
    'dataProvider' => $dataProvider->search(),
    'filter' => $dataProvider,

    'type' => 'striped bordered condensed',
    'columns' => array(
        array(
            'class' => 'EImageColumn',
            'htmlOptions' => array('width' => 120),
            // see below.
            'imagePathExpression' => '$data->getItemMainPicture("admin")',
            // Text used when cell is empty.
            // Optional.
            'emptyText' => 'нет изображения',
            // HTML options for image tag. Optional.
            'imageOptions' => array(
                'alt' => 'no',
                'width' => 120,
                'height' => 120,
            ),
        ),
        [
            'header' => '<input  class="gridCheckboxCheckAll" type="checkbox" />',
            'type' => 'raw',
            'value' => '"<input data-id=".$data->id." class=\"gridCheckbox\" type=\"checkbox\" />"',
        ],
        'id',
        'article',
        array(
            'header' => 'Парсится',
            'type' => 'raw',
            'value' => '$data->combinedWithParser()',
        ),
        'name',

        array(
            'header' => 'Переключатель публикации',
            'type' => 'raw',
            'value' => '$data->isPublished()',
        ),
        array(
            'header' => 'Top',
            'type' => 'raw',
            'value' => '$data->isTop()',
        ),

        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'viewButtonUrl' => 'Yii::app()->urlManager->createUrl(\'catalog/site/itemView\',array(\'title\'=>\'tmp_name\',\'catId\'=>$data->catId,\'itemName\'=>$data->name_t,\'item\'=>$data->id,))',
            'viewButtonOptions' => array('target' => '_blank')

        ),
    ),
));

$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'type'=>'danger',
        'label' => 'Удалить выбранное',
        'htmlOptions'=>[
            'class'=>'deleteAllBtn'
        ]
    )
);
?>

<script>
    $(function () {
        $(document).on("click", ".togglePublished", function () {
            var button = $(this);
            $.get('/catalog/catItem/togglePublished/id/' + $(this).attr('data-id'), function (data) {
                button.before("<span class='toDelete'>Сохранено<br/></span>");
                setTimeout(function () {
                    button.parent().find(".toDelete").remove()
                }, 500);

            })
        })

        $(document).on("click", ".toggleTop", function () {
            var button = $(this);
            $.get('/catalog/catItem/toggleTop/id/' + $(this).attr('data-id'), function (data) {
                button.before("<span class='toDelete'>Сохранено<br/></span>");
                setTimeout(function () {
                    button.parent().find(".toDelete").remove()
                }, 500);

            })
        })
    })
</script>

