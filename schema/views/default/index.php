<?php
Yii::app()->clientScript->registerScriptFile('/protected/modules/schema/assets/js/apiService.angular.js', 1);
Yii::app()->clientScript->registerScriptFile('/protected/modules/schema/assets/js/schemaFields.angular.js', 1);
$this->menu = require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/angular-drag-and-drop-lists/2.1.0/angular-drag-and-drop-lists.min.js"></script>


<div ng-app="schemaFieldApp" ng-controller="SchemaFieldController as ctrl" class="container">
    <h2>
        <i class="fas fa-table me-2"></i>Schema Fields Editor
    </h2>

    <table class="table table-hover table-draggable">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 40px;"></th>
                <th scope="col">ID</th>
                <th scope="col">Type</th>
                <th scope="col">Name</th>
                <th scope="col">Unit</th>
                <th scope="col">Order</th>
            </tr>
        </thead>
        <tbody dnd-list="ctrl.schemaFields" dnd-allowed-types="['field']">
            <tr ng-repeat="field in ctrl.schemaFields |orderBy:'order' track by $id(field)" dnd-draggable="field"
                dnd-effect-allowed="move" dnd-type="'field'" dnd-moved="ctrl.schemaFields.splice($index, 1)"
                dnd-dragstart="ctrl.onDragStart(field)" dnd-dragend="ctrl.onDragEnd()">
                <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
                <td class="text-muted">{{field._id}}</td>
                <td>
                    <select class="form-select" ng-model="field.type" ng-options="type for type in ctrl.types"
                        ng-change="ctrl.saveField(field)">
                    </select>
                </td>
                <td class="editable-cell">
                    <input type="text" ng-model="field.name" ng-blur="ctrl.saveField(field)"
                        placeholder="Field name">
                </td>
                <td>
                    <select class="form-select" ng-model="field.UoFId"
                        ng-options="unit.id as unit.abbreviation for unit in ctrl.units"
                        ng-change="ctrl.saveField(field)">
                        <option value="">----</option>
                    </select>

                </td>
                <td><span class="order-badge">{{field.order}}</span></td>
            </tr>
        </tbody>
    </table>
</div>