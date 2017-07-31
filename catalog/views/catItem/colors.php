<?php

Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/assets/colorPicker/js/bootstrap-colorpicker.js');
Yii::app()->clientScript->registerCssFile('/protected/modules/catalog/assets/colorPicker/css/bootstrap-colorpicker.css');

Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/views/catItem/colors.js');

$colors = CatColorToCatItem::model()->findAllByAttributes(['catItemId'=>$_REQUEST['id']]);

?>
<h2>Цвета этого товара</h2>
<table id="topTable" class="table table-striped">
    <thead>
    <td>Название цвета</td>
    <td>Цвет</td>
    <td>Изображение</td>
    <td>Прикрепить/убрать</td>
    <td>Удалить</td>
    </thead>
    <?php foreach ($colors as $color): ?>
        <tr data-color-id="<?= $color->colorId ?>" data-cat-item-id="<?= $_REQUEST['id'] ?>">
            <td><?= $color->color->name ?></td>
            <td class="colorTd" width="100"
                style="cursor: pointer;background-color: <?= $color->color->colorCode ?>"></td>
            <td><?php

                $picturesConfig = [];

                $this->widget(
                    'application.modules.pictureBox.components.PictureBox', array(
                        'id' => 'colorItem',
                        'elementId' => $color->color->id,
                        'config' => $picturesConfig,
                        'theme' => 'oneSmall'
                    )
                );

                ?></td>
            <td><input class="colorCheckbox" type="checkbox"
                       value="" checked></td>
            <td><a class="colorDeleteBtn btn btn-danger">Удалить</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$colors = CatColor::model()->findAll();
?>
<h2>Остальные цвета</h2>
<table id="bottomTable" class="table table-striped">
    <thead>
    <td>Название цвета</td>
    <td>Цвет</td>
    <td>Изображение</td>
    <td>Прикрепить/убрать</td>
    <td>Удалить</td>
    </thead>
    <?php foreach ($colors as $color): ?>
        <?php if ($color->isLinkedWithColor($_REQUEST['id'])) continue; ?>
        <tr data-color-id="<?= $color->id ?>" data-cat-item-id="<?= $_REQUEST['id'] ?>">
            <td><?= $color->name ?></td>
            <td class="colorTd" width="100"
                style="cursor: pointer;background-color: <?= $color->colorCode ?>"></td>
            <td><?php

                $picturesConfig = [];

                $this->widget(
                    'application.modules.pictureBox.components.PictureBox', array(
                        'id' => 'colorItem',
                        'elementId' => $color->id,
                        'config' => $picturesConfig,
                        'theme' => 'oneSmall'
                    )
                );

                ?></td>
            <td><input class="colorCheckbox" type="checkbox"
                       value=""></td>
            <td><a class="colorDeleteBtn btn btn-danger">Удалить</a></td>
        </tr>
    <?php endforeach; ?>
</table>

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
