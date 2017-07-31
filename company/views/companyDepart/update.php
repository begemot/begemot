<?php
/* @var $this CompanyDepartController */
/* @var $model CompanyDepart */

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

<h1>Update CompanyDepart <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>

<h2>Прикрепить сотрудника к отделению</h2>
<form>
    <?php
    $empsOwn = $model->emps;

    $empOwnArray =[];
    foreach ($empsOwn as $empOwn){
        $empOwnArray[$empOwn->id]=1;
    }

    $emps = CompanyEmployee::model()->findAll();
    foreach ($emps as $emp){
        echo $emp->name;

        $checked = '';
        if (isset($empOwnArray[$emp->id])){
            $checked='checked';
        }
        echo " <input ".$checked." class='empToDepartInput' empId='".($emp->id)."' depId='".($model->id)."' type='checkbox' value=''/><br/>";
    }
    ?>
</form>

<h2>Изображения</h2>
<?php

$picturesConfig = array();
$configFile = Yii::getPathOfAlias('webroot') . '/protected/config/ComanyDepartPBConfig.php';
if (file_exists($configFile)) {
    $picturesConfig = require($configFile);
} else {
    $configFile = Yii::getPathOfAlias('company') . '/ComanyDepartPBConfig.php';
    $picturesConfig = require($configFile);
}





$this->widget(
    'application.modules.pictureBox.components.PictureBox', array(
        'id' => 'companyDepart',
        'elementId' => $model->id,
        'config' => $picturesConfig,
        'theme' => 'tiles'
    )
);
?>
