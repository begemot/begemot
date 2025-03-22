<?php
$pbPath = Yii::getPathOfAlias('pictureBox');

Yii::app()->clientScript->registerScriptFile( '/bower_components/jquery/dist/jquery.min.js', 0);
Yii::app()->clientScript->registerScriptFile( '/bower_components/angular/angular.min.js', 0);
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

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/pictureBoxBs5.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/pictureBox.gallery-controller.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/galleryControll.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/pictureBox/assets/js-angular/crop.js');

?>
<script>

    app.service('values', function ($http) {
        this.galId = '<?= $id ?>';
        this.id = <?= $elementId ?>;
    });

</script>

<<div ng-app="pictureBox" ng-controller="gallery">
    <div ng-repeat="g in galList">
        <nav class="navbar navbar-expand-lg navbar-light bg-light pictureBoxNavBar">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Галлерея: {{values.galId}},{{values.id}}, подгаллерея {{subGalConfigs[g].title}}</a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#FileUpload" role="button" data-bs-toggle="modal" data-bs-target="#FileUploadModal" ng-click="madeSubGalleryActive(g)">Загрузить файлы</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div>
            <tiles gallery-id="<?= $id ?>" id="<?= $elementId ?>" active-gallery="{{g}}"></tiles>
        </div>
    </div>

    <upload gallery-id="<?= $id ?>" id="<?= $elementId ?>"></upload>

    <!-- Modal for Resize -->
    <div class="modal fade" id="resizeModal" tabindex="-1" aria-labelledby="resizeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resizeModalLabel">Обрезка миниатюр оригинального изображения</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Оригинальное изображение{{activeGallery}}123</h4>
                    <crop gallery-id="<?= $id ?>" id="<?= $elementId ?>"
                          image-src="{{getAllImagesModal().image.original}}"
                          image-id="{{getAllImagesModal().image.id}}"
                          blob-send-hook="testHook"
                          images="images"
                          sub-gallery="activeGallery"
                          all-data-collection="dataCollection">
                    </crop>
                    {{galleryControl.activeFilter.name}}
                    <span ng-class="{'badge bg-success':name!=getActiveFilterName(),'badge bg-warning':name==getActiveFilterName()}"
                          ng-repeat="(name,filter) in getConfig().imageFilters "
                          class="badge rounded-pill me-1"
                          ng-click="madeFilterActive(name)">{{name}}</span>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="preview"></span>
                        <span class="badge bg-success rounded-pill" ng-click="testHook()" id="previewSaveBtn">Сохранить</span>
                        <img id='realPreview' class='realPreview' src="{{getPreview()}}" alt="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Title and Alt -->
    <div class="modal fade" id="titleAltModal" tabindex="-1" aria-labelledby="titleAltModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleAltModalLabel">Редактирование alt и title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="title-text" class="form-label">Title:</label>
                            <textarea ng-model="getTitleModal().title" class="form-control" id="title-text"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="alt-text" class="form-label">Alt:</label>
                            <textarea ng-model="getTitleModal().alt" class="form-control" id="alt-text"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Sub Gallery List -->
    <div class="modal fade" id="subGalleryList" tabindex="-1" aria-labelledby="subGalleryListLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subGalleryListLabel">Выбор активной галлереи</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Список подгаллерей:
                    <ul class="nav flex-column">
                        <li class="nav-item" ng-repeat="gal in galList" ng-click="setGallery('default')">
                            <a class="nav-link" ng-class="{'active': 'default' == activeSubGallery}" href="">default</a>
                        </li>
                        <li class="nav-item" ng-repeat="gal in galList" ng-click="setGallery(gal)">
                            <a class="nav-link" ng-class="{'active': gal == activeSubGallery}" href="">{{gal}}</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>