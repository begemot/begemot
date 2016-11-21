<?php
/* @var $this CompanyEmployeeController */
/* @var $model CompanyEmployee */

Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(
        Yii::getPathOfAlias('company.assets.js').'/company.js',
        false,
        -1,
        YII_DEBUG
    ),
    CClientScript::POS_END
);

$this->menu=$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<h1>Update CompanyEmployee <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>

<h2>Прикрепить к отделению</h2>
<form>
<?php
$departsOwn = $model->departs;

$deparOwnArray =[];
foreach ($departsOwn as $departOwn){
    $deparOwnArray[$departOwn->id]=1;
}

$departs = CompanyDepart::model()->findAll();
foreach ($departs as $depart){
    echo $depart->name;

    $checked = '';
    if (isset($deparOwnArray[$depart->id])){
        $checked='checked';
    }
    echo " <input ".$checked." class='empToDepartInput' empId='".($model->id)."' depId='".($depart->id)."' type='checkbox' value=''/><br/>";
}
?>
</form>
<h2>Изображения</h2>
<?php

$picturesConfig = array();
$configFile = Yii::getPathOfAlias('webroot') . '/protected/config/ComanyEmployeePBConfig.php';
if (file_exists($configFile)) {
    $picturesConfig = require($configFile);
} else {
    $configFile = Yii::getPathOfAlias('company') . '/ComanyEmployeePBConfig.php';
    $picturesConfig = require($configFile);
}





$this->widget(
    'application.modules.pictureBox.components.PictureBox', array(
        'id' => 'companyEmployee',
        'elementId' => $model->id,
        'config' => $picturesConfig,
        'theme' => 'tiles'
    )
);
?>
