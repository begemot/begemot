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
        array('label'=>'Парсер', 'url'=>'/catalog/catItem/update/id/'.$model->id.'/tab/parser', 'active'=>$tab=='parser', 'visible' => isset(Yii::app()->modules['parsers']) ),
        array('label'=>'Перемещение позиции', 'url'=>'/catalog/catItem/update/id/'.$model->id.'/tab/position', 'active'=>$tab=='position'),
        array('label'=>'Изображения', 'url'=>'/catalog/catItem/update/id/'.$model->id.'/tab/photo', 'active'=>$tab=='photo'),
        array('label'=>'Видео', 'url'=>'/catalog/catItem/update/id/'.$model->id.'/tab/video', 'active'=>$tab=='video'),
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
        $categories = CatItemsToCat::model()->with(array('item', 'cat' => array('select' => 'id, pid')))->findAll(array('condition'=>'itemId='.$model->id));

        if (is_array($categories) && count($categories) > 0) {
            foreach ($categories as $cat) {
                if ($cat->is_through_display_child == 0) {
                    Yii::app()->clientScript->registerCss('link', 
                        "
                        .td-link {
                            margin-left:15px;
                            padding: 4px; 
                            background-color: #ccc;
                            cursor: pointer;
                            border-radius: 5px;
                        }
                        .td-link.active {
                            background-color: orange;
                            border: 1px solid orange;
                        }
                        " 
                    );
                    $active = ($cat->through_display == 0) ? '' : 'active';
                    $linkTitle = (empty($active)) ? 'Неактивно' : 'Активно';
                    echo "<div class='category-item'>";
                        $this->widget('bootstrap.widgets.TbButton', array(
                            'buttonType'=>'ajaxButton',
                            'icon'=>'icon-remove',
                            'label'=>'',
                            'type'=>'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                            'size'=>'mini', // null, 'large', 'small' or 'mini'
                            'url'=>'/catalog/catItem/deleteItemToCat/catId/'.$cat->catId.'/itemId/'.$model->id,
                            'ajaxOptions'=>array('success'=>'function (){location.reload()}'),
                        ));
                    
                        echo ($cat->catId != $cat->item->catId) 
                            ? ' <a href="?setMainCat='.$cat->catId.'">'.CatCategory::model()->getCatName( $cat->catId).'</a>' 
                            : ' '.CatCategory::model()->getCatName( $cat->catId).'  [<strong>основной раздел</strong>]'; 
                    
                        if ($cat->cat->pid != -1)
                            echo "<a title='Сквозное отображение - $linkTitle' class='$active td-link' data-value='".$cat->catId."'><span class='icon icon-white icon-retweet'></span></a>";
                    echo "</div>";
                    echo "<br>";
                }
            }
            echo '<br/>';
       }

        $itemToCat = new CatItemsToCat();
        $testForm = new CForm('catalog.models.forms.catToItemForm',$itemToCat);

        $testForm['itemId']->value = $model->id;
        echo "<div id='data'></div>";
        echo '<div class="container-fluid">'.$testForm->render().'</div>';
    }?>
<script>
    $('.td-link').click(function () {
        var $this = $(this);
        var cat_id = $this.attr('data-value');
        if (!$this.hasClass('active')) {
            $.post('/catalog/catItemsToCat/changeThroughDisplayValue/cat_id/' + cat_id + '/item_id/' + <?=$model->id?> + '/value/' + 1, function (){location.reload()})
        } else {
            $.post('/catalog/catItemsToCat/changeThroughDisplayValue/cat_id/' + cat_id + '/item_id/' + <?=$model->id?> + '/value/' + 0, function (){location.reload()})
        }
    });
</script>    
<?php }  ?>

<?php if ($tab=='position'){ ?>
<script>

        $(document).on("change", "#loadValues", function(){
            $.getJSON('/catalog/catItem/getItemsFromCategory/catId/' + $(this).val() + "/curCatId/" + $("#curPos").val(), function(data){
               $("#category").html(data.html);

               jQuery('#itemId').autocomplete({'minLength':'0','source':data.ids});
               $("#itemId").attr("autocomplete", "on");
               $("#currentPos").html(data.currentPos);
               $(".hidden").show();
            })
        })

    </script>
    <h2>Перемещение позиции</h2>
  
    <form method='post'>
        <input type="hidden" name='currentItem' value='<?=$model->id?>' id='curPos'>
    </form>

<?php if (!$model->isNewRecord): ?>
         <div class="success" style='color: green'><?php echo $message?></div>
       <?php $categories = CatItemsToCat::model()->with('item')->findAll(array('condition'=>'itemId='.$model->id)); ?>
       <?php if (is_array($categories) && count($categories)>0) :?>
        <form method='post'>
            <label for="">Выберите раздел:</label>
            <select name='categoryId' id='loadValues' >
               <option value="">Выберите раздел</option>
               <?php foreach ($categories as $cat): ?>
                    <option value='<?php echo $cat->catId?>'><?php echo CatCategory::model()->getCatName( $cat->catId)?></option>
                    <?php $items[$cat->catId] = CatItem::model()->findAll(array('condition'=>'catId='.$cat->catId)); ?>
              <?php endforeach ?>
            </select>
            <div class='hidden' style='display:none;'>
                <input type='submit' name='pasteOnFirstPosition' class='btn btn-info' value='Переместить на первую позицию'/>
                <input type='submit' name='pasteOnLastPosition' class='btn btn-info' value='Переместить на последнию позицию'/>

                Сейчас находится на <span id='currentPos'>2</span><br/>
                <label for="">Переместить перед:</label>
                <select name='item' id='category'>
<!---->
                </select>
                или введите ID
              <?php //
               $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                   'name'=>'itemId',
                   'source'=>array('ac1','ac2','ac3'),
                   // additional javascript options for the autocomplete plugin
                   'options'=>array(
                       'minLength'=>'0',
                   ),
               )); ?>
                <br/>
                <input type="submit" class='btn '/>
                <input type="hidden" name='changePosition'>
                <input type="hidden" name='currentItem' value='<?php echo $model->id?>' id='curPos'>
            </div>

        </form>
      <?php else: ?>
            <!-- Для начала добавльте карточку хоть в одну категорию -->
      <?php endif ?>


  <?php endif ?>

  <?php foreach ($categories as $key => $value): ?>
      
  <?php endforeach ?>
<?php }   ?>

<?php if ($tab=='options'){ require ('options.php');}?>

 <?php if (!$model->isNewRecord): ?>
    <?php if (isset(Yii::app()->modules['parsers'])): ?>
        <?php if ($tab=='parser'){ ?>
            <h1>Парсеры</h1>
            <?php if (isset($synched)): ?>
                <div class="append redColored">Сохраненно</div>
                <table>
                    <thead>
                        <tr>
                            <td>Артикул</td>
                            <td>Название</td>
                            <td>Старая цена</td>
                            <td>Новая цена</td>
                            <td>Наличие</td>
                            <td>Обновить</td>
                            <td>Удалить связь</td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr class='<?php echo $synched->id?>'>
                            <td><?php echo $synched->fromId?></td>
                            <td class='name'><?php echo $synched->item->name?></td>
                            <td><?php echo $synched->item->price?></td>
                            <td><input type='text' value='<?php echo $synched->linking->price?>' name='item[<?php echo $synched->item->id ?>][price]' class='price input-small'></td>
                            <td><input type='text' value='<?php echo $synched->linking->quantity?>' name='item[<?php echo $synched->item->id ?>][quantity]' class='quantity input-small'></td>
                            <td><button type='button' class='updatePrice btn btn-primary' data-id='<?php echo $synched->item->id ?>'>Обновить цену и наличие</button></td>
                            <td><button type='button' class='deleteLinking btn btn-danger' data-id='<?php echo $synched->id ?>'>Удалить связь</button></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <td>Название файла</td>
                            <td>Спарсить новые данные</td>
                            <td>Применить</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($fileListOfDirectory as $item): ?>
                        <tr>
                            <td><?php echo $item['name']?></td>
                            <td><input type='button' class='parseNew' data-file='<?php echo $item['className']?>' value='Спарсить новые данные'></td>
                            <td><input type='button' class='getParsedForCatItem btn btn-small btn-info' data-file='<?php echo $item['name']?>' data-id='<?php echo $model->id?>' value='Работать с текущими данными'></td>
                            
                        </tr>
                            
                    <?php endforeach ?>
                    </tbody>
                </table>

                <div class="append"></div>



                
            <?php endif ?>

            <script>
                $(document).on("click", ".parseNew", function(){
                    var button = $(this);

                    $.get('/parsers/default/parseNew/className/' + $(this).attr("data-file") + '/', function(data){
                        if (data == 1) {
                            button.val("Спарсенно");
                        };
                        
                    })

                })

                $(document).on("click", ".getParsedForCatItem", function(){
                    var button = $(this);
                    var params = {'CatItem': {'name': $(this).attr("name"), 'price': $(this).attr("price"), 'text': $(this).attr("text")}, 'returnId': true};

                    $(".append").html("<span class='spinner'></span>");
                    $.get('/parsers/default/getParsedForCatItem/itemId/' + $(this).attr("data-id") + '/file/' + $(this).attr("data-file") + '/', function(data){
                        $(".append").html(data);
                        
                    })

                })

                $(document).on("submit", ".ajaxSubmit", function(e){
                    e.preventDefault();
                    var form = $(this);
                    var hideAfter = $(this).attr("data-hideafter");
                    var removeAfter = $(this).attr("data-removeAfter");
                    var status = true;
                    $(this).find(".required").removeClass("form_error");

                    $(this).find(".required").each(function(){
                        if ($(this).val() == "") {status = false}
                            $(this).addClass("form_error");
                    })
                    if (!status) return false;

                    $.post(form.attr("action"), form.serialize(), function(data){
                        location.reload();    
                    })
                })

                $(document).on("click", ".deleteLinking", function(e){

                    var link = $(this);
                    var id = $(this).attr("data-id");


                    $.get('/parsers/default/deleteLinking/id/' + id, function(data){
                        if (data == '1') {
                            location.reload();
                            
                        } else{
                            alert("error");
                        }
                        
                    })

                })

                $(document).on("click", ".updatePrice", function(e){

                    var button = $(this);
                    var params = {'id': $(this).attr("data-id"), 'price': $(this).parents("TR").find(".price").val(), 'quantity': $(this).parents("TR").find(".quantity").val()};


                    $.post('/parsers/default/updateCard', params, function(data){
                        if (data == '1') {
                            $('.append').fadeIn('1000');
                            setTimeout(function(){
                                $('.append').fadeOut('1000');
                            }, 1000)
                            
                            button.attr('disabled', true);
                        };
                        
                    })

                })

            </script>
            
        <?php }?>  
    <?php endif ?>
<?php endif; ?>



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

<?php if ($tab=='video'){ ?>

<div id='video'>

Алгоритм работы таков. После добавления изображения, надо указывать в парамметрах "video_url" урл для видео и нажимать на сохранить зоголовок каждый раз. Парамметр видео урл находится на месте "alt", то есть если сохранил заголовок, то там и должно быть урл видео.
<br/>
Сама ссылка должна содержать только сам код ролика(Например: Ghnz9pLsAc)
<?php 
        
    $picturesConfig = array();
    $configFile = Yii::getPathOfAlias('webroot').'/protected/config/catalog/categoryItemPictureSettings.php';
    if (file_exists($configFile)){

        $picturesConfig = require($configFile);

        $this->widget(
            'application.modules.pictureBox.components.PictureBox', array(
            'id' => 'catalogItemVideo',
            'elementId' => $model->id,
            'config' => $picturesConfig,
          )
        );
    } else{
        Yii::app()->user->setFlash('error','Отсутствует конфигурационный файл:'.$configFile);
    }
?>    

</div>

<script>
  $(function(){
    var html = $("#video").find("FORM").html();

    if(html != undefined){
      html = html.replace('alt:', 'video_url:');
      $("#video").find("FORM").html(html);
    }
  })
</script>
<?php } ?>