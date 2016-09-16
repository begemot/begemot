<?php
$assets = Yii::app()->clientScript;
Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/css/multi-select.css')
);
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/js/jquery.multi-select.js'), CClientScript::POS_HEAD
);
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/js/jquery.quicksearch.js'), CClientScript::POS_HEAD
);
Yii::app()->clientScript->registerScriptFile('/protected/modules/begemot/components/fancybox/jquery.fancybox.js');
Yii::app()->clientScript->registerCssFile('/protected/modules/begemot/components/fancybox/jquery.fancybox.css');

Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/assets/js/options.js');

?>

    <h4>Опции</h4>


    <form method='post'>
        <select id='custom-headers' multiple='multiple' name='options[]' class='searchable'>
            <?php
            if (!$model->isNewRecord):
                $alreadyGot = CatItemsToItems::model()->findAll(array('select' => 'toItemId,isBase', 'condition' => 'itemId=' . $model->id));

                $arrayOfItems = [];
                $arrayOfIsBase  = [];

                foreach ($alreadyGot as $item) {
                    array_push($arrayOfItems, $item->toItemId);
                    $arrayOfIsBase[$item->toItemId] = $item->isBase;
                }
                $arrayOfItems = array_filter($arrayOfItems);
                $items = CatItem::model()->findAll(array('order' => 'id ASC'));

                $alreadyGotCatItems = [];

                if (is_array($items) && count($items) > 0):

                    foreach ($items as $item): ?>

                        <?php
                            $checked = in_array($item->id, $arrayOfItems) ? "selected" : "";
                            if ($checked) $alreadyGotCatItems[]=$item;

                        ?>

                        <option <?php echo $checked ?> value="<?php echo $item->id ?>"><?php echo $item->name ?>
                            (<?php echo number_format($item->price, 0, ',', ' '); ?> руб.)
                        </option>


                    <?php endforeach;
                endif;
            endif;
            ?>
        </select>  <br/>
        <a class="btn choseOptions btn-success" data-status="0">Выбрать опции</a>


        <table class="table optionsTable" data-item-id="<?=$_REQUEST['id']?>">
            <thead>
            <th>Изображение</th>
            <th>Название</th>
            <th>Базовая</th>
            <th>Не ставится без</th>
            <th></th>

            </thead>
            <?php foreach ($alreadyGotCatItems as $item): ?>

                <?php
                    $tableLineViewData = [
                        'originalImage'=>$item->getItemMainPicture('original'),
                        'adminImage'=>$item->getItemMainPicture('admin'),
                        'name' => $item->name,
                        'itemId'=>$item->id,
                        'isBase'=>$arrayOfIsBase[$item->id]
                    ];

                    $this->renderPartial('tableLine',$tableLineViewData);
                ?>

            <?php endforeach; ?>
        </table>

        <?php
        $related = CatItemsToItems::model()->with('item')->findAll(array('select' => 'itemId', 'condition' => 'toItemId=' . $model->id));
        if ((!$model->isNewRecord) && (count($related) > 0)):
            ?>
            <h4>Сопутствует</h4>
            <p>Список других товаров, у которых текущий товар указан как сопутствующий товар или в виде опции.</p>
        <?php
        foreach ($related as $item): ?>
            <div id="<?php echo $item->item->id; ?>" style="float: left; width: 100%;">
                <a href="/catalog/catItem/update/id/<?php echo $item->item->id; ?>"><?php echo $item->item->name; ?> </a><a
                    onClick="removeOption('<?php echo $item->item->id; ?>', '<?php echo $model->id; ?>');"
                    href="#">Убрать</a>
            </div>
        <?php
        endforeach;
        ?>
            <div style="float: left; width: 100%;">
                <br/>
            </div>
            <script>
                function removeOption(id, subid) {
                    $.ajax({
                        url: '/catalog/catItem/options/id/' + id + '/subid/' + subid,
                        success: function () {
                            $('#' + id).remove();
                        }
                    });
                }
            </script>
            <?php
        endif;
        ?>


        <h4>Добавить как опцию в карочки</h4>

        <select id='custom-headers2' multiple='multiple' name='items[]' class='searchable2'>
            <?php
            if (!$model->isNewRecord):
                $alreadyGot = CatItemsToItems::model()->findAll(array('select' => 'itemId', 'condition' => 'toItemId=' . $model->id));

                $arrayOfItems = array();
                foreach ($alreadyGot as $item) {
                    array_push($arrayOfItems, $item->itemId);
                }
                $arrayOfItems = array_filter($arrayOfItems);
                $items = CatItem::model()->findAll(array('order' => 'id ASC'));

                if (is_array($items) && count($items) > 0):

                    foreach ($items as $item): ?>

                        <?php $checked = in_array($item->id, $arrayOfItems) ? "selected" : "" ?>

                        <option <?php echo $checked ?> value="<?php echo $item->id ?>"><?php echo $item->name ?>
                            (<?php echo number_format($item->price, 0, ',', ' '); ?> руб.)<span class='editItem'></span>
                        </option>


                    <?php endforeach;
                endif;
            endif;
            ?>
        </select>

        <br/>

        <input type="submit" name='saveItemsToItems' class='btn btn-primary' value='сохранить'/>
    </form>

<?php
$related = CatItemsToItems::model()->with('toItem')->findAll(array('select' => 'itemId', 'condition' => 'itemId=' . $model->id));
if ((!$model->isNewRecord) & $related):
    ?>
    <h4>Уже в опция у данных карточек</h4>
    <?php

    $arrayOfItems = array_filter($arrayOfItems);
    foreach ($related as $item): ?>
        <div id="<?php echo $item->toItem->id; ?>" style="float: left; width: 100%;">
            <a href="/catalog/catItem/update/id/<?php echo $item->toItem->id; ?>"><?php echo $item->toItem->name; ?> </a><a
                onClick="removeOption('<?php echo $model->id; ?>', '<?php echo $item->toItem->id; ?>');"
                href="#">Убрать</a>
        </div>
        <?php
    endforeach;
    ?>
    <div style="float: left; width: 100%;">
        <br/>
    </div>

    <?php
endif;
?>

<div class="modal hide fade" id="optionSelectmodal" data-option-for-relation-id="">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Выбираем зависимые опции</h3>
    </div>
    <div class="modal-body">
        <p>Опции загружаются...</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="option-slect-close-btn">Закрыть</a>
        <a href="#" class="btn btn-primary" id="option-slect-end-btn">Выбрать</a>
    </div>
</div>