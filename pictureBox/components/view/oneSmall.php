<?php

$pbPath = Yii::getPathOfAlias('pictureBox');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/oneSmall.css');


$dropZoneAssetDir = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('begemot.extensions.dropzone.assets'));

//Yii::app()->clientScript->registerCssFile($dropZoneAssetDir . '/dropzone.css');
Yii::app()->clientScript->registerScriptFile($dropZoneAssetDir . '/dropzone.js', 1);

Yii::app()->clientScript->registerScriptFile('https://code.jquery.com/ui/1.12.0/jquery-ui.js');

$script = '

          var $config = \'' . serialize($_SESSION['pictureBox'][$id . '_' . $elementId]) . '\';
';

Yii::app()->clientScript->registerScript('pictureBox-js-' . $config['divId'], $script, 0);

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js/oneSmall.pictureBox.js', 0);

$thisPictureBoxScript = '
                var pbOptions = {

                    id : "' . $id . '",
                    elementId : ' . $elementId . ',
                    theme : "oneSmall",
                    divId:"' . $config['divId'] . '"
                }
                var pictureBox_' . $config['divId'] . ' = new PictureBox(pbOptions);
                 pictureBox_' . $config['divId'] . '.refreshPictureBox();



    ';
Yii::app()->clientScript->registerScript('pictureBox-js-' . $config['divId'], $thisPictureBoxScript, 2);


?>
<div class="oneSmallContainer">
<div id="<?php echo $config['divId'] ?>" class="imageContainer">


</div>
<div id="dropzone_<?php echo $config['divId'] ?>" class="oneSmallDropzone icon-folder-open hideIt"
     style="text-align: center;color:green;font-size:30px;"></div><div class="icon-folder-open startBtn"></div>
</div>