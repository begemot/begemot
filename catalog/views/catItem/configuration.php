<h1>Конфигуратор</h1>

<?php

Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/assets/js/cfg.js', 1);
Yii::app()->clientScript->registerCssFile('/protected/modules/catalog/assets/css/cfg.css');

Yii::import('pictureBox.components.PBox');
$pBoxMainPicture = new PBox('cfgMainPicture', $_REQUEST['id']);
//print_r($pBoxMainPicture->pictures);
//return;
if (count($pBoxMainPicture->pictures) > 0) {
    $pictureMain = array_shift($pBoxMainPicture->pictures);
    $pictureMain = $pictureMain['original'];
} else {
    $pictureMain = '';
}

123
echo '
<div id="mainContainer">
    <div id="mainPicture">
        <img class="baseImage" src="' . $pictureMain . '" />
    </div>
     <div id="optionPicture">

    </div>
</div>';


$this->widget(
    'application.modules.pictureBox.components.PictureBox', array(
        'id' => 'cfgMainPicture',
        'elementId' => $_REQUEST['id'],
        'config' => [],
        'theme' => 'oneSmall'
    )
);
$pBoxColor = new PBox('cfgMainPicture', $_REQUEST['id']);
echo '<a class="pictureBase" href="javascript:;" data-image="' . ($pBoxColor->getFirstImage('original')) . '">поменять</a>';

$this->widget(
    'application.modules.pictureBox.components.PictureBox', array(
        'id' => 'cfgColor',
        'elementId' => $_REQUEST['id'],
        'config' => [],
        'theme' => 'oneSmall'
    )
);
$pBoxColor = new PBox('cfgColor', $_REQUEST['id']);

echo '<a class="pictureBase" href="javascript:;" data-image="' . ($pBoxColor->getFirstImage('original')) . '">поменять</a>';


$this->widget(
    'application.modules.pictureBox.components.PictureBox', array(
        'id' => 'cfgOption',
        'elementId' => $_REQUEST['id'],
        'config' => [],
        'theme' => 'oneSmall'
    )
);

$pBoxColor = new PBox('cfgOption', $_REQUEST['id']);

echo '<a class="pictureOption" href="javascript:;" data-image="' . ($pBoxColor->getFirstImage('original')) . '">поменять</a>';


$colors = CatColorToCatItem::model()->findAllByAttributes(['catItemId' => $_REQUEST['id']]);
$options = CatItem::model()->findByPk($_REQUEST['id'])->options();