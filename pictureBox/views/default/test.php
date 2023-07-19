<?php

$component=Yii::createComponent(array(

    'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

));

Yii::app()->setComponent('bootstrap',$component);

$configFile = Yii::getPathOfAlias('webroot') . '/protected/config/catalog/categoryItemPictureSettings.php';
$picturesConfig = require($configFile);
$this->widget(
    'application.modules.pictureBox.components.PictureBox', array(
        'id' => 'test',
        'elementId' => '1',
        'config' => $picturesConfig,
        'theme' => 'tiles'
    )
);

//$this->widget(
//    'application.modules.pictureBox.components.PictureBox', array(
//        'id' => 'test',
//        'elementId' => '1',
//        'config' => $picturesConfig,
//    )
//);