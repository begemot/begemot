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
    app.service('categoriesData', function () {
        this.categories = <?php echo json_encode($tmp)?>
    })

    function onDrop(event) {
        const data = event.dataTransfer.getData("text/plain");
        event.target.textContent = data;
        event.preventDefault();
        console.log(data)
    }

</script>

<div ng-app="catManage" ng-controller="ui">
    <p>
        <button href="#myModal" class="btn  btn-success" type="button" data-toggle="modal">Добавить раздел</button>
    </p>
    <div class="line" ng-repeat="(catId,cat) in cats" ng-class="{'disabled':cat.disabled}">
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

    <!-- Modal -->
    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Modal header</h3>
        </div>
        <div class="modal-body">

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <button class="btn btn-primary">Save changes</button>
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
                    <input type="radio" name="optionsRadios" id="radiobox1" value="0" ng-checked="selectedOptionForCatrgoryDelete==0" ng-model="selectedOptionForCatrgoryDelete">
                    Открепить от раздела, но не удалять
                </label>
                <label class="radio">
                    <input type="radio" name="optionsRadios" id="radiobox2" value="1" ng-checked="selectedOptionForCatrgoryDelete==1" ng-model="selectedOptionForCatrgoryDelete">
                    <span style="color:red">Удалить все позиции каталога</span>
                </label>
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <button class="btn btn-primary" data-dismiss="modal" ng-click="deleteCategory()">Удалить</button>
        </div>
    </div>
</div>



