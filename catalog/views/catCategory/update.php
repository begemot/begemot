<?php

$this->breadcrumbs=array(
	'Cat Categories'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';
?>

<h1>Update CatCategory <?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'items'=>array(
        array('label'=>'Данные', 'url'=>'/catalog/catCategory/update/id/'.$model->id, 'active'=>$tab=='data'),
        array('label'=>'Изображения', 'url'=>'/catalog/catCategory/update/id/'.$model->id.'/tab/photo', 'active'=>$tab=='photo'),
        array('label'=>'Видео', 'url'=>'/catalog/catCategory/update/id/'.$model->id.'/tab/video', 'active'=>$tab=='video'),
    ),
)); ?>

<?php
    if ($tab=='data'){
        echo $this->renderPartial('_form', array('model'=>$model));
    	$this->renderPartial('messageWidget');
    }
?>


<?php if ($tab=='photo'){ ?>

<?php 
        
    $picturesConfig = array();
    $configFile = Yii::getPathOfAlias('webroot').'/protected/config/catalog/categoryPictureSettings.php';
    if (file_exists($configFile)){

        $picturesConfig = require($configFile);

//        $this->widget(
//            'application.modules.pictureBox.components.PictureBox', array(
//            'id' => 'catalogCategory',
//            'elementId' => $model->id,
//            'config' => $picturesConfig,
//                )
//        );

        $this->widget(
            'application.modules.pictureBox.components.ColorsPictureBox', array(
                'id' => 'catalogCategory',
                'elementId' =>  $model->id,
                'config' => $picturesConfig,
                'theme' => 'tiles'
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
    $configFile = Yii::getPathOfAlias('webroot').'/protected/config/catalog/categoryPictureSettings.php';
    if (file_exists($configFile)){

        $picturesConfig = require($configFile);

        $this->widget(
            'application.modules.pictureBox.components.PictureBox', array(
            'id' => 'catalogCategoryVideo',
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