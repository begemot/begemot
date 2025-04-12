<style>
    .is-changed {
        background-color: #fff3cd;
        /* легкий желтый фон */
        border-color: rgb(238, 163, 25);
        transition: background-color 0.3s ease;
        /* плавный переход */
    }
</style>
<?php

$this->menu = require dirname(__FILE__) . '/../default/commonMenu.php';

Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/schema/assets/js/schema.angular.js', 1);


$mongoId = new MongoDB\BSON\ObjectId($id);


// Yii::import('schema.components.*');
Yii::import('begemot.extensions.vault.FileVault');
// $link = new CSchemaLink($model->linkType, $model->linkId);
$collection = Yii::app()->mongoDb->getCollection('schemaData');
$model = $collection->findOne(['_id' => $mongoId]);

?>

<script>
    // Глобальные переменные для передачи данных в AngularJS
    window.schemaRawData = <?= json_encode($model) ?>;
    window.schemaLinkType = <?= json_encode($model->linkType) ?>;
    window.schemaGroupId = <?= json_encode($model->groupId) ?>;
</script>

<div class="container mt-4">
    <h1 class="mb-4">Редактирование SchemaLinks #<?php echo $model->_id; ?></h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php
            // $this->renderPartial('_form', array('model' => $model)); 
            ?>
        </div>
    </div>

    <div ng-app="schema" ng-controller="update" class="mt-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Данные схемы: {{ allData[0].name }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>
                                Название
                                <input type="text" class="form-control form-control-sm mt-1" placeholder="Фильтр..."
                                    ng-model="searchName">
                            </th>
                            <th>
                                Значение
                                <input type="text" class="form-control form-control-sm mt-1" placeholder="Фильтр..."
                                    ng-model="searchValue">
                            </th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody> | filter: { name: searchName, value: searchValue }
                        <tr ng-repeat="(key,value) in allData['fields']">
                            <td>{{ key }}</td>
                            <td>
                                <input type="text" class="form-control" ng-model="value.value"
                                    ng-class="{'is-changed': isChanged(value.value, key)}">
                            </td>
                            <td>
                                <button class="btn btn-sm" ng-class="buttonState[key].class || 'btn-primary'"
                                    ng-click="updateField(value, key)">
                                    {{ buttonState[field.id].text || "Сохранить" }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>


</div>