<?php
/* @var $this CatItemController */
/* @var $model CatItem */
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

$this->breadcrumbs=array(
	'Cat Items'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu = require dirname(__FILE__).'/commonMenu.php';
?>

<h1>Редактирование позиции "<?php echo $model->name; ?>"</h1>

    <?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'items'=>array(
        array('label'=>'Данные', 'url'=>'/catalog/catItem/update/id/'.$model->id, 'active'=>$tab=='data'),
        array('label'=>'Разделы', 'url'=>'/catalog/catItem/update/id/'.$model->id.'/tab/cat', 'active'=>$tab=='cat'),
        array('label'=>'Опции', 'url'=>'/catalog/catItem/update/id/'.$model->id.'/tab/options', 'active'=>$tab=='options'),
        array('label'=>'Изображения', 'url'=>'/catalog/catItem/update/id/'.$model->id.'/tab/photo', 'active'=>$tab=='photo'),
    ),
)); ?>

<?php
    if ($tab=='data')
        echo $this->renderPartial('_form', array('model'=>$model)); 
?>
<?php if ($tab=='cat'){ ?>
<h2>Разделы</h2>
<?php
    if (!$model->isNewRecord) {  

        $categories = CatItemsToCat::model()->with('item')->findAll(array('condition'=>'itemId='.$model->id));

        if (is_array($categories) && count($categories)>0){
            foreach ($categories as $cat){

                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'=>'ajaxButton',
                    'icon'=>'icon-remove',
                    'label'=>'',
                    'type'=>'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                    'size'=>'mini', // null, 'large', 'small' or 'mini'
                    'url'=>'/catalog/catItem/deleteItemToCat/catId/'.$cat->catId.'/itemId/'.$model->id,
                    'ajaxOptions'=>array('success'=>'function (){location.reload()}'),
                ));


                if ($cat->catId!=$cat->item->catId){
                    echo ' <a href="?setMainCat='.$cat->catId.'">'.CatCategory::model()->getCatName( $cat->catId).'</a><br/>';
                } else{

                echo ' '.CatCategory::model()->getCatName( $cat->catId).'  [<strong>основной раздел</strong>]<br/>';
                }

            }
            echo '<br/>';
       }

        $itemToCat = new CatItemsToCat();
        $testForm = new CForm('catalog.models.forms.catToItemForm',$itemToCat);


        $testForm['itemId']->value = $model->id;
        echo '<div class="container-fluid">'.$testForm->render().'</div>';
    }?>
<?php }  ?>

<?php if ($tab=='options'){ ?>
<h2>Опции</h2>


<form method='post'>
    <select id='custom-headers' multiple='multiple' name='options[]' class='searchable'>
      <?php
        if (!$model->isNewRecord):
            $alreadyGot = CatItemsToItems::model()->findAll(array('select'=>'toItemId', 'condition' => 'itemId='.$model->id));

            $arrayOfItems = array();
            foreach ($alreadyGot as $item) {
                array_push($arrayOfItems, $item->toItemId);
            }
            $arrayOfItems = array_filter($arrayOfItems);
            $items = CatItem::model()->findAll(array('order'=>'id ASC'));

            if (is_array($items) && count($items)>0):

                foreach ($items as $item): ?>

                    <?php $checked = in_array($item->id, $arrayOfItems) ? "selected" : "" ?>
                    
                    <option <?=$checked?> value="<?=$item->id?>"><?php echo $item->name?>(<?php echo number_format($item->price, 0, ',', ' ');?> руб.)<span class='editItem'></span></option>


                <?php endforeach;
            endif;
        endif; 
      ?>
    </select>
    <script>
        $('.searchable').multiSelect({
      selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Поиск по опциям...'>",
      selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Уже в опциях...'>",
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

    <input type="submit" name='saveItemsToItems' class='btn btn-primary' value='сохранить'/>
</form>
<?php }  ?>



<?php if ($tab=='photo'){ ?>

<?php 
        
    $picturesConfig = array();
    $configFile = Yii::getPathOfAlias('webroot').'/protected/config/catalog/categoryItemPictureSettings.php';
    if (file_exists($configFile)){

        $picturesConfig = require($configFile);

        $this->widget(
            'application.modules.pictureBox.components.PictureBox', array(
            'id' => 'catalogItem',
            'elementId' => $model->id,
            'config' => $picturesConfig,
                )
        );
    } else{
        Yii::app()->user->setFlash('error','Отсутствует конфигурационный файл:'.$configFile);
    }
?>    
<?php } ?>





