<?php
$pbPath = Yii::getPathOfAlias('pictureBox');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/tiles.css');


$dropZoneAssetDir = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('begemot.extensions.dropzone.assets'));

Yii::app()->clientScript->registerCssFile($dropZoneAssetDir . '/dropzone.css');
Yii::app()->clientScript->registerScriptFile($dropZoneAssetDir . '/dropzone.js', 1);

Yii::app()->clientScript->registerScriptFile('https://code.jquery.com/ui/1.12.0/jquery-ui.js');

$script = '



          var $config = \'' . serialize($_SESSION['pictureBox'][$id . '_' . $elementId]) . '\';
';

Yii::app()->clientScript->registerScript('pictureBox-js-' . $config['divId'], $script, 0);

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js/jquery.pictureBox.js', 0);

$thisPictureBoxScript = '
                var pbOptions = {

                    id : "' . $id . '",
                    elementId : ' . $elementId . ',
                    theme : "tiles",
                    divId:"' . $config['divId'] . '"
                }
                var pictureBox_' . $config['divId'] . ' = new PictureBox(pbOptions);
                 pictureBox_' . $config['divId'] . '.refreshPictureBox();



    ';
Yii::app()->clientScript->registerScript('pictureBox-js-' . $config['divId'], $thisPictureBoxScript, 2);


Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js/jquery.imgareaselect/scripts/jquery.imgareaselect.js', 0);
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/js/jquery.imgareaselect/css/imgareaselect-default.css');

?>


<div id="<?php echo $config['divId'] ?>" style="width:100%;">

</div>
<div id="dropzone_<?php echo $config['divId'] ?>" class="mydropzone"
     style="text-align: center;color:green;font-size:30px;"><span>Нажми, или перетащи сюда файлы!</span></div>

<div class="modal hide fade" id="titleModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Редактирование alt и title изображения</h3>
    </div>
    <div class="modal-body">
        <form>
            <div class="form-group">
                <label for="altInput">Alt</label>
                <textarea type="text" class="form-control" id="altInput" placeholder="Alt" style="width: 400px;height: 50px;"></textarea>
            </div>
            <div class="form-group">
                <label for="titleInput">Title</label>
                <textarea type="text" class="form-control" id="titleInput" placeholder="Title" style="width: 400px;height: 50px;"></textarea>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <a onclick="$('#titleModal').modal('hide')" class="btn">Отмена</a>
        <a id="altTitleSaveBtn" class="btn btn-primary">Сохранить</a>
    </div>
</div>


<div class="modal hide fade" id="imageModal" style="margin:0;left:0;width:100%;top:20px;height:80%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Изменение пропорций</h3>
    </div>
    <div class="modal-body" id="imageAreaParent" style="max-height:95%;">
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        
    </div>
    <div class="modal-footer">
        <a  id="expandBtn" onclick="console.log('увеличить фон')" class="btn">Увеличить фон</a>
        <a  onclick="$('#imageModal').modal('hide')" class="btn">Отмена</a>
        <a  id="resizeImgSaveBtn" class="btn btn-primary">Сохранить</a>
    </div>
</div>

