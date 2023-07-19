<?php
/* @var $this PromoController */
/* @var $model Promo */

$this->menu = require dirname(__FILE__) . '/../catItem/commonMenu.php';

if (is_null($tab)) $tab = 'data';
?>

    <h2>Редактирование акции <?php echo $model->title; ?></h2>

<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type' => 'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked' => false, // whether this is a stacked menu
    'items' => array(
        array('label' => 'Данные', 'url' => '/catalog/promo/update/id/' . $model->id, 'active' => $tab == 'data'),
        array('label' => 'Разделы', 'url' => '/catalog/promo/update/id/' . $model->id . '/tab/cat', 'active' => $tab == 'cat'),
        array('label' => 'Позиции', 'url' => '/catalog/promo/update/id/' . $model->id . '/tab/positions', 'active' => $tab == 'positions'),
    ),
)); ?>


<?php if ($tab == 'data'): ?>

    <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
<?php endif; ?>


<?php if ($tab == 'cat'): ?>

    <h3>Список разделов на которые действует акция</h3>
    <?php

    if (!$model->isNewRecord) {
        //$categories = CatItemsToCat::model()->with(array('item', 'cat' => array('select' => 'id, pid')))->findAll(array('condition'=>'itemId='.$model->id));
        $categories = PromoRelation::model()->findAll(array('condition' => 'promoId=' . $model->id . ' and type=' . PromoTypeEnum::TO_CATEGORY));

        if (is_array($categories) && count($categories) > 0) {

            foreach ($categories as $cat) {



                echo "<div class='category-item'>";
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'ajaxButton',
                    'icon' => 'icon-remove',
                    'label' => '',
                    'type' => 'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                    'size' => 'mini', // null, 'large', 'small' or 'mini'
                    'url' => '/catalog/promo/deletePromoToCat/catId/' . $cat->targetId . '/promoId/' . $model->id,
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

        $promoRelation = new PromoRelation();
        $promoToCatForm = new CForm('catalog.models.forms.promoToCatForm', $promoRelation);

        $promoToCatForm['promoId']->value = $model->id;
        $promoToCatForm['type']->value = PromoTypeEnum::TO_CATEGORY;

        echo "<div id='data'></div>";
        echo '<div class="container-fluid">' . $promoToCatForm->render() . '</div>';
    }?>
<?php endif; ?>

<?php if ($tab == 'positions'): ?>

    <?php
    $items = PromoRelation::model()->with(array('promo'))->findAll(array('condition' => 'promoId=' . $model->id . ' and type=' . PromoTypeEnum::TO_POSITION));

    if (is_array($items) && count($items) > 0) {

        foreach ($items as $item) {

            echo "<div class='category-item'>";
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'ajaxButton',
                'icon' => 'icon-remove',
                'label' => '',
                'type' => 'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                'size' => 'mini', // null, 'large', 'small' or 'mini'
                'url' => '/catalog/promo/deletePromoToPosition/itemId/' . $item->targetId . '/promoId/' . $model->id,
                'ajaxOptions' => array('success' => 'function (){location.reload()}'),
            ));

            $catItem = null;
            $catItem = CatItem::model()->findByPk($item->targetId );
            if (!is_null($catItem)){
                echo ' ' . $catItem->name . '';
            } else{
                $item->delete();
                echo 'Нет позиции с id='.$item->targetId.'. Связь удалена.';
            }



//                if ($cat->cat->pid != -1)
//                    echo "<a title='Сквозное отображение - $linkTitle' class='$active td-link' data-value='" . $cat->catId . "'><span class='icon icon-white icon-retweet'></span></a>";
            echo "</div>";
            echo "<br>";

        }
        echo '<br/>';
    }

    $promoRelation = new PromoRelation();
    $promoToItemForm = new CForm('catalog.models.forms.promoToItemForm', $promoRelation);

    $promoToItemForm['promoId']->value = $model->id;
    $promoToItemForm['type']->value = PromoTypeEnum::TO_POSITION;

    echo "<div id='data'></div>";
    echo '<div class="container-fluid">' . $promoToItemForm->render() . '</div>';

    ?>


<?php endif; ?>