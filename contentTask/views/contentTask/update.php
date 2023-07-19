<?php

require_once (__DIR__.'/common.php');

?>

<script>

    taskId = <?=$_REQUEST['id']?>;
    app.value('taskId', taskId);
</script>
    <h1>Управление заданием</h1>
<ul class="nav nav-tabs">
    <li class="active">
        <a href="/contentTask/contentTask/update?id=<?=$_REQUEST['id']?>">Основные</a>
    </li>
    <li>
        <a href="/contentTask/contentTask/search?id=<?=$_REQUEST['id']?>">Поиск</a>
    </li>
    <li>
        <a href="/contentTask/contentTask/added?id=<?= $_REQUEST['id'] ?>">Добавленные</a>
    </li>
</ul>

<div ng-app="app">
    <div ng-controller="update">
        <h3>Название</h3>
        <input type="text" placeholder="Название задания" ng-model="taskTitle">
        <h3>Код доступа</h3>
        {{contentTask.accessCode}} был создан в {{contentTask.codeDate*1000 | date:'HH:mm dd.MM.yyyy':'+0430'}}
        <button class="btn btn-small" ng-click="generateNewAccessCode()">Создать новый код </button>
        <br>
        <a href="/contentTask/taskPerform/taskList?accessCode={{contentTask.accessCode}}" target="_blank">Интерфейс</a>, где можно выполнить задание.
        <h3>Описание задания</h3>
        <textarea rows="3" ng-model="taskText" placeholder="Описание задания"  data-ck-editor></textarea>

        <h3>Выберите тип задания</h3>
        <select ng-show="typesChoiceVisible" ng-model="selectedType" ng-change="updateType()">
                <option ng-repeat="type in types" value="{{type.id}}" ng-selected="selectedType == type.id">{{type.title}}</option>
        </select>

        <div id="options" ng-show="actonsVisible">
            <h2>Действия</h2>
            <label class="checkbox inline" ng-repeat="act in typeActs track by $index">
                <input ng-click="act.selected=!act.selected"  ng-checked="act.selected" type="checkbox" id="inlineCheckbox1" value="option1" > {{act.id}}
            </label>
        </div>
        <div ng-show="fieldsVivsible">
            <h2>Данные</h2>


                <div ng-repeat="field in typeFields track by $index" >
                    <label class="checkbox inline">
                        <input ng-click="field.selected=!field.selected" type="checkbox" id="inlineCheckbox1" value="option1" ng-checked="field.selected"> {{field.name}}
                    </label>
                </div>
        </div>
        <br>
        <button class="btn btn-large btn-primary" type="button" ng-click="sendCreateData()">Отправить</button>

    </div>
</div>
