<h1>Конфигуратор</h1>

<?php
Yii::app()->clientScript->registerScriptFile('/protected/modules/begemot/assets/js/bobber.js', 1);
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

?>

<div id="mainContainer" style="height:1000px">
    <div id="mainPicture">
      123
    </div>
     <div id="optionPicture">
        1<br/>
        2<br/>
        3<br/>
        4<br/>
        5<br/>
        6<br/>
        7<br/>
        8<br/>
        9<br/>
        10<br/>
        11<br/>
        12<br/>
        13<br/>
        14<br/>
        15<br/>
        16<br/>
        17<br/>
        18<br/>
        19<br/>
        20<br/>
        21<br/>
        22<br/>
        23<br/>
        24<br/>
        25<br/>
        26<br/>
        27<br/>
        28<br/>
        29<br/>
        30<br/>
        31<br/>
        32<br/>
        33<br/>
        34<br/>
        35<br/>
         36<br/>
         37<br/>
         38<br/>
         39<br/>
         40<br/>
         41<br/>
         42<br/>
         43<br/>
         44<br/>
         45<br/>
         46<br/>
         47<br/>
         48<br/>
         49<br/>
         50<br/>
         51<br/>
         52<br/>
         53<br/>
         54<br/>
    </div>

</div>

<?php
return;
echo '<div id="rightPanel">';
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
echo '</div>';