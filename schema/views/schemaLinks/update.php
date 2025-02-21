<?php
$this->menu = require dirname(__FILE__) . '/../default/commonMenu.php';

Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/schema/assets/js/schema.angular.js', 1);


Yii::import('schema.components.*');
Yii::import('begemot.extensions.vault.FileVault');
$link = new CSchemaLink($model->linkType, $model->linkId);
?>

<script>
    // Глобальные переменные для передачи данных в AngularJS
    window.schemaRawData = <?= json_encode($link->getData(true)) ?>;
    window.schemaLinkType = <?= json_encode($model->linkType) ?>;
    window.schemaGroupId = <?= json_encode($model->linkId) ?>;
</script>

<div class="container mt-4">
    <h1 class="mb-4">Редактирование SchemaLinks #<?php echo $model->id; ?></h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php $this->renderPartial('_form', array('model' => $model)); ?>
        </div>
    </div>

    <div ng-app="schema" ng-controller="update" class="mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Данные схемы: {{ allData[0].name }}</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered" ng-if="allData.length > 0 && allData[0].data.length > 0">
                <thead class="table-light">
                    <tr>
                        <th>
                            Название 
                            <input type="text" class="form-control form-control-sm mt-1"
                                   placeholder="Фильтр..." ng-model="searchName">
                        </th>
                        <th>
                            Значение 
                            <input type="text" class="form-control form-control-sm mt-1"
                                   placeholder="Фильтр..." ng-model="searchValue">
                        </th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="field in allData[0].data | filter: { name: searchName, value: searchValue }">
                        <td>{{ field.name }}</td>
                        <td>
                            <input type="text" class="form-control" ng-model="field.value">
                        </td>
                        <td>
                            <button class="btn btn-sm"
                                    ng-class="buttonState[field.id].class || 'btn-primary'"
                                    ng-click="updateField(allData[0], field)">
                                {{ buttonState[field.id].text || "Сохранить" }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="text-muted" ng-if="allData.length === 0 || allData[0].data.length === 0">
                Нет данных для отображения.
            </p>
        </div>
    </div>
</div>


</div>