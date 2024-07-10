<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>
<?php
$pbPath = Yii::getPathOfAlias('pictureBox');

Yii::app()->clientScript->registerScriptFile('/bower_components/angular/angular.min.js');
//Yii::app()->clientScript->registerScriptFile('/bower_components/jquery/src/jquery.js');

Yii::app()->clientScript->registerScriptFile('https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/tiles.css');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/js-angular.css');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/crop.css');

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/angular-drag-and-drop-lists.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/fileUpload/ng-file-upload.min.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/fileUpload/ng-file-upload-shim.min.js');

Yii::app()->clientScript->registerCssFile('/bower_components/cropperjs/dist/cropper.css');
Yii::app()->clientScript->registerScriptFile('/bower_components/cropperjs/dist/cropper.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/begemot/ui/searchDropDown/js/service.ui.bgmt.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/begemot/ui/searchDropDown/js/modal.ui.bgmt.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/pictureBox.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/pictureBox.gallery-controller.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/pictureBox.massResize-controller.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/galleryControll.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/crop.js');


?>
<?php
$id = 'catalogCategory';
$elementId = 3803;
?>
<script>
    $(document).ready(() => {
        console.log('123123');
    });
    app.service('values', function ($http) {
        this.galId = '<?= $id ?>';
        this.id = <?= $elementId ?>;
        this.activeGallery = 'default';
    });

</script>


<div ng-app="pictureBox" ng-controller="massResize">

    <modal-and-button
            modal-title="Настройки для пакетной обработки"
            btn-label="Настройки процесса"
    >
        <label for="exampleInputEmail1" class="form-label">ID галлереи</label>
        <input  ng-model="settingsForm.galleryId"   class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">

        <label for="exampleInputEmail1" class="form-label">Тег изображения для пакетной обработки</label>
        <input ng-model="settingsForm.tag" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        <label  class="form-label">Размеры обрезки</label>
        <div class="input-group mb-3">
            <span class="input-group-text">Ш</span>
            <input  ng-model="settingsForm.width" type="text" class="form-control" placeholder="ширина">
            <span class="input-group-text">В</span>
            <input  ng-model="settingsForm.height" type="text" class="form-control" placeholder="высота">
            
        </div>
        <label for="exampleFormControlTextarea1" class="form-label">Список IDs экземпляров геллереи для обработки(через пробел)</label>
        <textarea ng-model="settingsForm.ids"  class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" ng-click="formChange()">Обновить</button>
    </modal-and-button>


            <div class="modal-content">
                <button type="button" class="btn btn-primary" ng-click="nextImage()">Следующее</button>
                <button type="button" class="btn btn-primary" ng-click="testHook()">Сохранить</button>
                <div class="modal-body" style="max-height: 1000px;">

                    <crop
                        gallery-id="{{settingsForm.galleryId}}" id="{{id}}"
                        image-src="{{activeImageSrc}}"
                        image-id="{{activeImageIndex}}"
                        blob-send-hook="testHook"
                        images=""
                        sub-gallery="activeGallery"

                        all-data-collection="dataCollection"
                    ></crop>
                    {{galleryControl.activeFilter.name}}
                    <span
                        ng-class="{'badge-success':name!=getActiveFilterName(),'badge-warning':name==getActiveFilterName()}"
                        ng-repeat="(name,filter) in getConfig().imageFilters "
                        class="badge badge-pill"
                        ng-click="madeFilterActive(name)">{{name}}</span>
                    <div style="display: flex;justify-content: space-between;">
                        <span class="preview" style=""></span>
<!--                        <span class="badge badge-pill badge-success" ng-click="testHook()"-->
<!--                              id="previewSaveBtn">Сохранить</span>-->
                        <img style="height: fit-content" id='realPreview' class='realPreview' src="{{activeImageSrcTag}}" alt="">
                    </div>

                    <!--                    <span class="badge badge-pill badge-warning">Warning</span>-->

                </div>

            </div><!-- /.модальное окно-Содержание -->



</div>