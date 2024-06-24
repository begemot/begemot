<?php
$this->menu = require dirname(__FILE__) . '/../catItem/commonMenu.php';


Yii::app()->clientScript->registerCssFile('/protected/modules/catalog/assets/css/catManage.css');
Yii::app()->clientScript->registerCssFile('/bower_components/font-awesome/css/all.css');

Yii::app()->clientScript->registerScriptFile('/bower_components/lodash/dist/lodash.min.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/assets/js/catManage.angular.js');
?>

<h2>Управление разделами каталога</h2>
<p>Пробный текст</p>

<?php
$model = CatCategory::model();
//$tmp = $model->getcategoriesTree();
$model->loadCategories();
$tmp = $model->categories;

?>
<script>
app.service('categoriesData', function() {
    this.categories = <?php echo json_encode($tmp) ?>
})

function onDrop(event) {
    const data = event.dataTransfer.getData("text/plain");
    event.target.textContent = data;
    event.preventDefault();
    console.log(data)
}
</script>
<style>
.modal-body {
    max-height: 600px;
    /* Увеличение высоты модального окна */
    overflow-y: auto;
    /* Скролл при необходимости */
}

.modal-lg {
    width: 80%;
    /* Увеличение ширины модального окна */
}
</style>
<div ng-app="catManage" ng-controller="ui">
    <p>
        <button href="#myModal" class="btn  btn-success" type="button" data-toggle="modal">Добавить раздел</button>
        <input type="text" ng-model="catsFilter">
    </p>
    <div class="line" ng-repeat="(catId,cat) in cats | filter:catsFilter" ng-class="{'disabled':cat.disabled}">
        <div class="leftBlock" style="width: {{70+25*cat.level}}px;" a-drop-target catId="{{cat.id}}"
            id="item{{cat.id}}" el-type="left">
            <span class="updateDeleteButtonBlock">
                <i class="fa fa-trash" data-toggle="modal" href="#catDeleteModal"
                    ng-click="markForDeleteCategory(catId)"></i>
                <a href="/catalog/catCategory/update/id/{{cat.id}}" target="_blank"><i class="fa fa-pen"></i></a>
            </span>


        </div>
        <div a-draggable a-drop-target catId="{{cat.id}}" id="item{{cat.id}}" el-type="middle">{{cat.id}} {{cat.order}}
            {{cat.name}}
        </div>
        <div class="rightBlock" a-drop-target catId="{{cat.id}}" id="item{{cat.id}}" el-type="right"></div>
    </div>

    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Создание раздела</h3>
        </div>
        <div class="modal-body">
            <form id="section-form" method="post" ng-submit="submitForm()">
                <p class="note">Поля, отмеченные звездочкой (<span class="required">*</span>), являются обязательными.
                </p>

                <div class="form-group">
                    <label for="parent_section">Родительский раздел</label>
                    <select class="form-control" id="parent_section" name="Section[parent_id]" ng-model="parentSection">
                        <option value="">-- Выбрать раздел --</option>
                        <option ng-repeat="category in cats" value="{{category.id}}">{{category.name}}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">Название*</label>
                    <input type="text" class="form-control" id="name" name="Section[name]" ng-model="section.name"
                        size="60" maxlength="70" required>
                </div>

                <div class="form-group">
                    <label for="seo_title">SEO заголовок*</label>
                    <input type="text" class="form-control" id="seo_title" name="Section[seo_title]"
                        ng-model="section.seo_title" size="60" maxlength="255" required>
                </div>

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>

            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>

            </div>
        </div>




    </div>


    <!-- Modal -->
    <div id="catDeleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Удаление каталога</h3>


        </div>
        <div class="modal-body">


            <h5>Удаляемый каталог:</h5>
            <ul>
                <li ng-repeat="(key, value) in markedForDeleteCategory ">
                    {{ key }}: {{ value }}
                </li>
            </ul>
            <p>Что делать с позициями в разделе:</p>
            <div class="form">
                <label class="radio">
                    <input type="radio" name="optionsRadios" id="radiobox1" value="0"
                        ng-checked="selectedOptionForCatrgoryDelete==0" ng-model="selectedOptionForCatrgoryDelete">
                    Открепить от раздела, но не удалять
                </label>
                <label class="radio">
                    <input type="radio" name="optionsRadios" id="radiobox2" value="1"
                        ng-checked="selectedOptionForCatrgoryDelete==1" ng-model="selectedOptionForCatrgoryDelete">
                    <span style="color:red">Удалить все позиции каталога</span>
                </label>
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <button class="btn btn-primary" data-dismiss="modal" ng-click="deleteCategory()">Удалить</button>
        </div>
    </div>