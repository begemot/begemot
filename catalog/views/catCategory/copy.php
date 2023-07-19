<?php

$assets=Yii::app()->clientScript;
Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/css/multi-select.css')
);
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/js/jquery.multi-select.js'), CClientScript::POS_HEAD
);
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/js/jquery.quicksearch.js'), CClientScript::POS_HEAD
);
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/js/editItem.js'), CClientScript::POS_HEAD
);

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';

?>
<h2>Создать копию карточки товара</h2>


<form method='post'>
    <select id='custom-headers' multiple='multiple' name='items[]' class='searchable'>
        <?php

            $arrayOfItems = array();

            $items = CatItem::model()->findAll(array('order'=>'id ASC'));

            if (is_array($items) && count($items)>0):

                foreach ($items as $item): ?>

                    <?php $checked = in_array($item->id, $arrayOfItems) ? "selected" : "" ?>

                    <option <?php echo $checked?> value="<?php echo $item->id?>"><?php echo $item->name?>(<?php echo number_format($item->price, 0, ',', ' ');?> руб.)<span class='editItem'></span></option>


                <?php endforeach;
            endif;

        ?>
    </select>
    <script>
        $('.searchable').multiSelect({
            selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Поиск товаров...'>",
            selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Выбрано...'>",
            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(){
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(){
                this.qs1.cache();
                this.qs2.cache();
            }
        });
    </script>
    <br/>
    <h3>Категория:</h3>

    <input type="radio" checked name="mode" value="default"> Без категории

    <input type="radio" name="mode" value="catOfOriginal"> Категория оригинала
    <br/>
    <br/>
    <input type="submit" name='makeItemsCopy' class='btn btn-primary' value='Копировать'/>
</form>