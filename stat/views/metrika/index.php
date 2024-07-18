<?php
/* @var $this MetrikaController */
$menuPath = Yii::getPathOfAlias('stat.views');
$this->menu = require_once($menuPath . DIRECTORY_SEPARATOR . 'commonMenu.php');
?>

<div class="container" ng-app="metrikaApp" ng-controller="MetrikaController">
    <h1>Метрики</h1>
    <button class="btn btn-primary mb-3" ng-click="showCreateForm()">Добавить метрику</button>
    <div ng-show="showForm">
        <h2>{{formTitle}}</h2>
        <form ng-submit="saveMetrika()">
            <div class="mb-3">
                <label for="domain" class="form-label">Домен</label>
                <input type="text" id="domain" ng-model="metrika.domain" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="counter_id" class="form-label">ID счетчика</label>
                <input type="number" id="counter_id" ng-model="metrika.counter_id" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Сохранить</button>
            <button type="button" class="btn btn-secondary" ng-click="cancel()">Отмена</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Домен</th>
                    <th>ID счетчика</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="metrika in metrikas" ng-init="rowNumber = $index + 1">
                    <td>{{rowNumber}}</td>
                    <td>{{metrika.id}}</td>
                    <td>{{metrika.domain}}</td>
                    <td>{{metrika.counter_id}}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" ng-click="editMetrika(metrika)">Редактировать</button>
                        <button class="btn btn-danger btn-sm" ng-click="deleteMetrika(metrika.id)">Удалить</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    var app = angular.module('metrikaApp', []);

    app.controller('MetrikaController', function($scope, $http) {
        $scope.metrikas = [];
        $scope.showForm = false;
        $scope.formTitle = '';
        $scope.metrika = {};

        $scope.loadMetrikas = function() {
            $http.get('<?php echo Yii::app()->createUrl("stat/metrika/list"); ?>')
                .then(function(response) {
                    $scope.metrikas = response.data;
                }, function(error) {
                    console.error('Ошибка загрузки метрик:', error);
                });
        };

        $scope.showCreateForm = function() {
            $scope.showForm = true;
            $scope.formTitle = 'Добавить метрику';
            $scope.metrika = {};
        };

        $scope.editMetrika = function(metrika) {
            $scope.showForm = true;
            $scope.formTitle = 'Редактировать метрику';
            $scope.metrika = angular.copy(metrika);
            $scope.metrika.counter_id = parseInt($scope.metrika.counter_id, 10); // Преобразуем в число
        };

        $scope.saveMetrika = function() {
            var url = $scope.metrika.id ? '/stat/metrika/update/id/' + $scope.metrika.id : '<?php echo Yii::app()->createUrl("stat/metrika/create"); ?>';
            $http.post(url, $scope.metrika)
                .then(function(response) {
                    if (response.data.status === 'success') {
                        $scope.loadMetrikas();
                        $scope.cancel();
                    } else {
                        console.error('Ошибка сохранения метрики:', response.data.errors);
                    }
                }, function(error) {
                    console.error('Ошибка сохранения метрики:', error);
                });
        };

        $scope.deleteMetrika = function(id) {
            if (confirm('Вы уверены, что хотите удалить эту метрику?')) {
                $http.delete('/stat/metrika/delete/id/' + id)
                    .then(function(response) {
                        if (response.data.status === 'success') {
                            $scope.loadMetrikas();
                        } else {
                            console.error('Ошибка удаления метрики:', response.data.message);
                        }
                    }, function(error) {
                        console.error('Ошибка удаления метрики:', error);
                    });
            }
        };

        $scope.cancel = function() {
            $scope.showForm = false;
            $scope.metrika = {};
        };

        $scope.loadMetrikas();
    });
</script>
