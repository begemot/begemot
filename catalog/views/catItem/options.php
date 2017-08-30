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
Yii::app()->clientScript->registerScriptFile('https://code.jquery.com/ui/1.12.0/jquery-ui.js');

$options = CatItem::model()->findByPk($_REQUEST['id'])->options();

?>

<h4>Опции</h4>


<form method='post'>
    <select id='custom-headers' multiple='multiple' name='options[]' class='searchable'>
        <?php
        if (!$model->isNewRecord):
            $alreadyGot = CatItemsToItems::model()->findAll(array('select' => 'toItemId,isBase', 'condition' => 'itemId=' . $model->id));

            $arrayOfItems = [];
            $arrayOfIsBase = [];

            foreach ($alreadyGot as $item) {
                array_push($arrayOfItems, $item->toItemId);
                $arrayOfIsBase[$item->toItemId] = $item->isBase;
            }
            $arrayOfItems = array_filter($arrayOfItems);
            $items = CatItem::model()->with('options')->findAll(array('order' => ' `options`.`order` ASC'));

            $alreadyGotCatItems = [];

            if (is_array($items) && count($items) > 0):

                foreach ($items as $item): ?>

                    <?php
                    $checked = in_array($item->id, $arrayOfItems) ? "selected" : "";
                    if ($checked) $alreadyGotCatItems[] = $item;

                    ?>

                    <option <?php echo $checked ?> value="<?php echo $item->id ?>"><?php echo $item->name ?>
                        (<?php echo number_format($item->price, 0, ',', ' '); ?> руб.)
                    </option>


                <?php endforeach;
            endif;
        endif;


        ?>
    </select> <br/>
<!--    <a class="btn choseOptions btn-success" data-status="0">Выбрать опции</a>-->


    <table class="table optionsTable" data-item-id="<?= $_REQUEST['id'] ?>">
        <thead>
        <th>Изображение</th>
        <th>Название</th>
        <th>Базовая</th>
        <th>Цена</th>
        <th>Не ставится без</th>
        <th>Конфликт</th>
        <th></th>

        </thead>
        <?php foreach ($options as $item): ?>

            <?php
            $tableLineViewData = [
                'originalImage' => $item->getItemMainPicture('original'),
                'adminImage' => $item->getItemMainPicture('admin'),
                'name' => $item->name,
                'itemId' => $item->id,
                'isBase' => $arrayOfIsBase[$item->id],
                'options' => $item->options,
                'price'=>$item->price,
//                'conflict' => $item->conflict,
            ];

            $this->renderPartial('tableLine', $tableLineViewData);
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
    <form method='post'>
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
    </form>

    <br/>

<!--    <a class="btn choseOptions btn-success" data-status="0">Выбрать опции</a>-->
</form>


<div class="modal hide fade" id="optionSelectmodal" data-option-for-relation-id="" data-target="">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Выбираем зависимые опции</h3>
        <table class="table" style="margin:0">
            <thead>
            <tr>
                <th colspan="2">Для базовой опции:</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>

            </tbody>

        </table>
    </div>
    <div class="modal-body">
        <p>Опции загружаются...</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="option-slect-close-btn">Закрыть</a>
    </div>
</div>


<br/><br/>
<br/>
<br/>
<br/>
<br/>
<br/>
