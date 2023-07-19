<?php
/* @var $this DiscountController */
/* @var $model Discount */

$this->menu = require dirname(__FILE__) . '/../catItem/commonMenu.php';

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

if (is_null($tab)) $tab = 'data';
?>

    <h2>Редактирование скидки <?php echo $model->title; ?></h2>

<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type' => 'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked' => false, // whether this is a stacked menu
    'items' => array(
        array('label' => 'Данные', 'url' => '/catalog/discount/update/id/' . $model->id, 'active' => $tab == 'data'),
        array('label' => 'Разделы', 'url' => '/catalog/discount/update/id/' . $model->id . '/tab/cat', 'active' => $tab == 'cat'),
        array('label' => 'Позиции', 'url' => '/catalog/discount/update/id/' . $model->id . '/tab/positions', 'active' => $tab == 'positions'),
    ),
)); ?>


<?php if ($tab == 'data'): ?>

    <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
<?php endif; ?>


<?php if ($tab == 'cat'): ?>

    <h3>Список разделов на которые действует cкидка</h3>
    <?php

    if (!$model->isNewRecord) {
        //$categories = CatItemsToCat::model()->with(array('item', 'cat' => array('select' => 'id, pid')))->findAll(array('condition'=>'itemId='.$model->id));
        $categories = DiscountRelation::model()->findAll(array('condition' => 'discountId=' . $model->id . ' and type=' . DiscountTypeEnum::TO_CATEGORY));

        if (is_array($categories) && count($categories) > 0) {

            foreach ($categories as $cat) {



                echo "<div class='category-item'>";
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'ajaxButton',
                    'icon' => 'icon-remove',
                    'label' => '',
                    'type' => 'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                    'size' => 'mini', // null, 'large', 'small' or 'mini'
                    'url' => '/catalog/discount/deleteDiscountToCat/catId/' . $cat->targetId . '/discountId/' . $model->id,
                    'ajaxOptions' => array('success' => 'function (){location.reload()}'),
                ));

                echo ' ' . CatCategory::model()->getCatName($cat->targetId) . '';

//                if ($cat->cat->pid != -1)
//                    echo "<a title='Сквозное отображение - $linkTitle' class='$active td-link' data-value='" . $cat->catId . "'><span class='icon icon-white icon-retweet'></span></a>";
                echo "</div>";
                echo "<br>";

            }
            echo '<br/>';
        }

        $discountRelation = new DiscountRelation();
        $discountToCatForm = new CForm('catalog.models.forms.discountToCatForm', $discountRelation);

        $discountToCatForm['discountId']->value = $model->id;
        $discountToCatForm['type']->value = DiscountTypeEnum::TO_CATEGORY;

        echo "<div id='data'></div>";
        echo '<div class="container-fluid">' . $discountToCatForm->render() . '</div>';
    }?>
<?php endif; ?>

<?php if ($tab == 'positions'): ?>

    <h2>Привязать скидку к карточкам</h2>

    <form method='post'>
        <select id='custom-headers' multiple='multiple' name='items[]' class='searchable'>
            <?php
            if (!$model->isNewRecord):
                $alreadyGot = DiscountRelation::model()->findAll(array('select'=>'targetId', 'condition' => 'discountId='.$model->id . " AND type=2"));

                $arrayOfItems = array();
                foreach ($alreadyGot as $item) {
                    array_push($arrayOfItems, $item->targetId);
                }
                $arrayOfItems = array_filter($arrayOfItems);
                $items = CatItem::model()->findAll(array('order'=>'id ASC'));

                if (is_array($items) && count($items)>0):

                    foreach ($items as $item): ?>

                        <?php $checked = in_array($item->id, $arrayOfItems) ? "selected" : "" ?>

                        <option <?php echo $checked?> value="<?php echo $item->id?>"><?php echo $item->name?>(<?php echo number_format($item->price, 0, ',', ' ');?> руб.)<span class='editItem'></span></option>


                    <?php endforeach;
                endif;
            endif;
            ?>
        </select>
        <script>
            $('.searchable').multiSelect({
                selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Поиск по карточкам...'>",
                selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Связаны с этой скидкой...'>",
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

    

     <input type="submit" name='saveItemsToDiscount' class='btn btn-primary' value='сохранить'/>
    </form>


<?php endif; ?>