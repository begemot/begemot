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
<script>

    app.service('values', function ($http) {
        this.galId = '<?= $id ?>';
        this.id = <?= $elementId ?>;
    });

</script>

<div ng-app="pictureBox" ng-controller="gallery">
    <div ng-repeat="g in galList">
        <div class="navbar pictureBoxNavBar">
            <div class="navbar-inner">
                <a class="brand" href="#">Галлерея: {{values.galId}},{{values.id}}, подгаллерея {{g}}</a>
                <ul class="nav">
                    <li><a href="#FileUpload" role="button" data-toggle="modal" ng-click="madeSubGalleryActive(g)">Загрузить файлы</a></li>

                </ul>

            </div>
        </div>
        <div>
            <tiles gallery-id="<?= $id ?>" id="<?= $elementId ?>" active-gallery="{{g}}"></tiles>


        </div>
    </div>


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
                    <h4>Оригинальное изображение{{activeGallery}}123</h4>
                    <crop
                            gallery-id="<?= $id ?>" id="<?= $elementId ?>"
                            image-src="{{getAllImagesModal().image.original}}"
                            image-id="{{getAllImagesModal().image.id}}"
                            blob-send-hook="testHook"
                            images="images"
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
                        <span class="badge badge-pill badge-success" ng-click="testHook()"
                              id="previewSaveBtn">Сохранить</span>
                        <img id='realPreview' class='realPreview' src="{{getPreview()}}" alt="">
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
                            <textarea ng-model="getTitleModal().title" class="form-control" id="title-text"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="alt-text" class="col-form-label">Alt:</label>
                            <textarea ng-model="getTitleModal().alt" class="form-control" id="alt-text"></textarea>
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
                        <li ng-repeat="gal in galList" ng-click="setGallery('default')"><a
                                    ng-class={'gallListActive':'default'==activeSubGallery} href="">default</a></li>
                        <li ng-repeat="gal in galList" ng-click="setGallery(gal)"><a
                                    ng-class={'gallListActive':gal==activeSubGallery} href="">{{gal}}</a></li>

                    </ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div><!-- /.модальное окно-Содержание -->
        </div><!-- /.модальное окно-диалог -->
    </div><!-- /.модальное окно -->


</div>