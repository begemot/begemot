<?php
//$this->breadcrumbs=array(
//	'Galleries'=>array('index'),
//	$model->name,
//);
//$subMenu = array(
//        array('label' => $model->name),
//    array('label'=>'Просмотр','url'=>array('/gallery/default/view/id/'.$model->id.'.html')),
//    array('label'=>'Загрузить','url'=>array('/gallery/default/manageGallery/id/'.$model->id.'.html')),
//);
$commonMenu = require dirname(__FILE__).'/commonMenu.php';
$this->menu = array_merge($commonMenu);
?>

<h1>Просмотр галлереи: <?php echo $model->name; ?></h1>
<?php

$this->widget(
    'begemot.extensions.bootstrap.widgets.TbButton',
    array(
        'label' => 'Добавить фото',
        'type' => 'success', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'size' => 'small', // null, 'large', 'small' or 'mini'
        //'url' => '/catalog/default/renderImages/action/start',
        'icon'=>'plus',
        'url'=>array('/gallery/default/manageGallery/id/'.$model->id.'.html')

    )
);

$filesArray = array();

$dataFile = Yii::getPathOfAlias('webroot').'/files/pictureBox/gallery/'.$model->id.'/data.php';

if (file_exists($dataFile)){
    
    $images = require ($dataFile);
    
    foreach ($images['images'] as $id=>$item){
        $tmpArray = $item;
        $tmpArray['id']='1';
        $filesArray[]= $tmpArray;
    }
    
    if (count($filesArray)==0)
       Yii::app()->user->setFlash('error', '<strong>Ошибка!</strong> Изображения отсутствуют. Воспользуйтесь пунктом меню "загрузить"');
 
    $listDataProvider = new CArrayDataProvider($filesArray);

    $this->widget('begemot.extensions.bootstrap.widgets.TbThumbnails', array(
        'dataProvider'=>$listDataProvider,
        'template'=>"{items}\n{pager}",
        'itemView'=>'_thumb',
    ));

    
} else{
    Yii::app()->user->setFlash('error', '<strong>Ошибка!</strong> Изображения отсутствуют. Воспользуйтесь пунктом меню "загрузить"');

}

$this->renderPartial('messageWidget');
?>
