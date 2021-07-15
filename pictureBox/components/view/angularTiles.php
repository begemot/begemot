<?php
$pbPath = Yii::getPathOfAlias('pictureBox');
Yii::app()->clientScript->registerScriptFile('https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/tiles.css');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/js-angular.css');
Yii::app()->clientScript->registerCssFile('/protected/modules/pictureBox/assets/css/crop.css');

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/angular-drag-and-drop-lists.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/fileUpload/ng-file-upload.min.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/fileUpload/ng-file-upload-shim.min.js');

Yii::app()->clientScript->registerCssFile('/bower_components/cropperjs/dist/cropper.css');
Yii::app()->clientScript->registerScriptFile('/bower_components/cropperjs/dist/cropper.js');


Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/pictureBox.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/crop.js');

?>
<input type="text" data-provide="typeahead" data-items="4" data-source='{

}'>
<div ng-app="pictureBox" ng-controller="gallery">
    <div class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="#">Галлерея</a>
            <ul class="nav">
                <li><a  href="#FileUpload" role="button"  data-toggle="modal">Загрузить файлы</a></li>
                <li><a  href="#subGalleryList" role="button"  data-toggle="modal">Подгаллереи</a></li>
            </ul>

        </div>
    </div>

    <tiles gallery-id="<?= $id ?>" id="<?= $elementId ?>" sub-gallery="{{activeSubGallery}}"></tiles>
    <upload gallery-id="<?= $id ?>" id="<?= $elementId ?>"></upload>


    <div class="modal fade bd-example-modal-lg" id="resizeModal"
         style="width: 850px;margin-left:-425px; height: 800px;margin-top: -400px;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Обрезка миниатюр оригинального изображения</h4>
                </div>
                <div class="modal-body" style="max-height: 1000px;">
                    <h4>Оригинальное изображение</h4>
                    <crop
                            gallery-id="<?= $id ?>" id="<?= $elementId ?>"
                        image-src="{{allImagesModal.image.original}}"
                        image-id="{{allImagesModal.image.id}}"
                        blob-send-hook="testHook"
                        images="images" active-filter="activeFilter"
                            sub-gallery="activeSubGallery"
                            images-reload="getData"
                    ></crop>

                    <span
                            ng-class="{'badge-success':name!=activeFilter.name,'badge-warning':name==activeFilter.name}"
                            ng-repeat="(name,filter) in config.imageFilters "
                            class="badge badge-pill"
                            ng-click="madeFilterActive(name)">{{name}}</span>
                    <div style="display: flex;justify-content: space-between;">
                        <span class="preview" style=""></span>
                        <span class="badge badge-pill badge-success" ng-click="testHook()"  id="previewSaveBtn">Сохранить</span>
                        <img id='realPreview' class='realPreview' src="{{currentPreviewSrc}}" alt="">
                    </div>

                    <!--                    <span class="badge badge-pill badge-warning">Warning</span>-->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div><!-- /.модальное окно-Содержание -->
        </div><!-- /.модальное окно-диалог -->
    </div><!-- /.модальное окно -->

    <div class="modal fade" id="titleAltModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Редактирование alt и title</h4>
                </div>
                <div class="modal-body">


                    <form>
                        <div class="form-group">
                            <label for="title-text" class="col-form-label">Title:</label>
                            <textarea ng-model="titleModal.title" class="form-control" id="title-text"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="alt-text" class="col-form-label">Alt:</label>
                            <textarea ng-model="titleModal.alt" class="form-control" id="alt-text"></textarea>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div><!-- /.модальное окно-Содержание -->
        </div><!-- /.модальное окно-диалог -->
    </div><!-- /.модальное окно -->

    <div class="modal fade" id="subGalleryList">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Выбор активной галлереи</h4>
                </div>
                <div class="modal-body">
                    Список подгаллерей:
                    <ul class="nav">
                        <li  ng-repeat="gal in galList" ng-click="setGallery('default')"><a  ng-class={'gallListActive':'default'==activeSubGallery} href=""  >default</a></li>
                        <li  ng-repeat="gal in galList" ng-click="setGallery(gal)"><a  ng-class={'gallListActive':gal==activeSubGallery}  href=""  >{{gal}}</a></li>

                    </ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div><!-- /.модальное окно-Содержание -->
        </div><!-- /.модальное окно-диалог -->
    </div><!-- /.модальное окно -->


</div>