<?php
/* @var $this CatItemController */
/* @var $model CatItem */

$this->breadcrumbs = array(
    'Cat Items' => array('index'),
    'Manage',
);



$this->menu = require dirname(__FILE__).'/commonMenu.php';

 $massSelectJsScript = '/protected/modules/catalog/assets/js/massSelect.js';
Yii::app()->clientScript->registerScriptFile($massSelectJsScript);
?>

<h1>Все позиции каталога</h1>


<?php
Yii::import('begemot.extensions.grid.EImageColumn');


$js = '
    function setCheckboxCall(){

      $(".gridCheckbox").click();


    }
 ';
Yii::app()->clientScript->registerScript('checkBoxScript',$js,1);

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'test-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'selectableRows'=>0,


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
            'header' => '<input  onClick="setCheckboxCall();"  class="gridCheckboxCheckAll" type="checkbox" />',
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

$this->widget(
    'bootstrap.widgets.TbButton',
    array(
//        'type'=>'danger',
        'label' => 'Разделы',
        'htmlOptions'=>[
            'class'=>'sectionsBtn',
            'style'=>'margin-left:10px;'
        ]
    )
);

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
//        'type'=>'danger',
//        'label' => 'Разделы',
        'htmlOptions'=>[
            'id'=>'sectionsModal'
        ]
    )
);

?>

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Массовые операции с позициями и разделами</h4>
            </div>
            <div class="modal-body">

                <h4>Выбрано <span id="selectedCount">__</span> позиций</h4>
                <p>Теперь выберите нужные разделы, куда вы хотите переместить или скопировать выбранные позиции.</p>

                <ul>
                <?php
                    $menu = CatCategory::model()->categoriesMenu();
                    foreach ($menu as $itemId => $menuItem){
                      echo '<li>';
                      echo '<input class="sectionCheckBox" type="checkbox" data-id="'.$itemId.'"/>';
                      echo $menuItem['label'];


                      echo '</li>';
                    }
//                    print_r( CatCategory::model()->categoriesMenu());
                ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-mass-cat-connect">Добавить в раздел</button>
                <button type="button" class="btn btn-primary btn-mass-copy">Скопировать</button>
                <button type="button" class="btn btn-primary btn-mass-move">Переместить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>


<?php

$this->endWidget();
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

