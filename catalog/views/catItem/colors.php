<?php

Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/assets/colorPicker/js/bootstrap-colorpicker.js');
Yii::app()->clientScript->registerCssFile('/protected/modules/catalog/assets/colorPicker/css/bootstrap-colorpicker.css');

Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/views/catItem/colors.js');

$colors = CatColorToCatItem::model()->findAllByAttributes(['catItemId'=>$_REQUEST['id']]);

?>

<h2>Цвета этого товара</h2>
<style>
    .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-hue,
    .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-color,
    .colorpicker-color div {
        height: 30px;
    }
</style>
<form class="form-search" action="/catalog/catItem/createColor">
    <input name="colorName" type="text" placeholder="Название цвета" class="input-medium search-query">

    <div id="cp2" style="width:150px;display: inline-block" class="input-group colorpicker-component">
        <input name="colorCode" style="width:100px" type="text" value="#00AABB" class="form-control"/>
        <span class="input-group-addon"><i></i></span>
    </div>
    <input name="catItemId" value="<?= $_REQUEST['id'] ?>" type="hidden"/>
    <button type="submit" class="btn btn-success"><i class="icon-plus"></i> Добавить цвет</button>
</form>


<div class="modal fade" id="colorPickerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div id="cp11" data-active-color-id="" class="input-group colorpicker-component">
                    <input type="text" value="" class="form-control"/>
                    <span class="input-group-addon"><i style="width:200px"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<table id="topTable" class="table table-striped">
    <thead>
    <td>id</td>
    <td>Название цвета</td>
    <td>Цвет</td>
    <td></td>
    </thead>
    <?php foreach ($colors as $color): ?>
        <tr data-color-id="<?= $color->colorId ?>" data-cat-item-id="<?= $_REQUEST['id'] ?>">
            <td><?= $color->color->id ?></td>
            <td><?= $color->color->name ?></td>
            <td class="colorTd" width="100"
                style="cursor: pointer;background-color: <?= $color->color->colorCode ?>"></td>


            <td><a class="colorDeleteBtn btn btn-danger">Удалить</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Изображения по цветам</h2>

<?php
Yii::import('pictureBox.components.PBoxFiles');
//$pbox = new PBoxSQLite('sqlLiteTest', 1);

$picturesConfig = array(
    'divId' => 'pictureBox',
    'nativeFilters' => array(
        'main' => true,
        'admin' =>true,
    ),
    'catalog' => true,
    'filtersTitles' => array(
        'main' => 'Основная',
        'catalog' => 'каталог',
        'admin' =>'Системный',
    ),
    'imageFilters' => array(
        'admin' => array(
            0 => array(
                'filter' => 'CropResizeUpdate',
                'param' => array(
                    'width' => 298,
                    'height' => 198,
                ),
            ),
        ),
        'main' => array(
            0 => array(
                'filter' => 'CropResize',
                'param' => array(
                    'width' => 320,
                    'height' => 219,
                ),
            ),
        ),
        'catalog' => array(
            0 => array(
                'filter' => 'CropResize',
                'param' => array(
                    'width' => 163,
                    'height' => 120,
                ),
            ),
        ),
    ),
//    'original' => array(
//        1 => array(
//            'filter' => 'WaterMark',
//            'param' => array(
//                'watermark' => '/images/watermark.png',
//            ),
//        ),
//    ),
);




$this->widget(
    'application.modules.pictureBox.components.ColorsPictureBox', array(
        'id' => 'catColors',
        'elementId' => $_REQUEST['id'],
        'config' => $picturesConfig,
        'theme' => 'tiles'
    )
);
