<h1>Управление галлерей: <?php echo $model->name; ?></h1>
<?php
//$subMenu = array(
//        array('label' => $model->name),
//    array('label'=>'Просмотр','url'=>array('/gallery/default/view/id/'.$model->id.'.html')),
//    array('label'=>'Загрузить','url'=>array('admin')),
//);
$commonMenu = require dirname(__FILE__) . '/commonMenu.php';
$this->menu = array_merge($commonMenu);
$configFile = Yii::getPathOfAlias('webroot') . '/protected/config/galleryConfig.php';
if (file_exists($configFile)) {
    $picturesConfig = require($configFile);
} else {
    Yii::app()->user->setFlash('warning', '<strong>Внимание!</strong> Отсуствует кофигурационный файл config/galleryConfig.php');
    $picturesConfig = array();
};

$this->widget(
    'application.modules.pictureBox.components.PictureBox', array(
        'id' => 'gallery',
        'elementId' => $model->id,
        'config' => $picturesConfig,
        'theme' => 'tiles'
    )
);

$this->renderPartial('messageWidget');
?>
