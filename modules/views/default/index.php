<?php
Yii::app()->clientScript->registerScriptFile('/protected/modules/modules/assets/js/modulesService.angular.js', 2);
?>
<script>

    var app = angular.module('modulesManager', ['modules'])

    app.controller('modulesManagerCtrl', ['$scope', 'modulesService','$http', function ($scope, modulesService,$http) {
        $scope.modulesList = null
        $scope.modulesData = null
        $scope.loading = true
        modulesService.loadData().then(() => {

            $scope.modulesList = modulesService.getModulesList()
            $scope.modulesData = modulesService.getModulesData()

            $scope.loading = false
        });

        $scope.activateModule = (module)=>{
            console.log('активировать '+module)
            $http({
                method:'GET',
                url:'/modules/default/activateModule',
                params:{
                    module:module
                }
            }).then(()=>{
                $scope.modulesData[module].active = true;
            })
        }

        $scope.deactivateModule = (module)=>{
            console.log('деактивировать '+module)
            $http({
                method:'GET',
                url:'/modules/default/deactivateModule',
                params:{
                    module:module
                }
            }).then(()=>{
                $scope.modulesData[module].active = false;
            })
        }

    }])

</script>

<div ng-app="modulesManager" ng-controller="modulesManagerCtrl">
    <div style="display: flex;margin-top: 20px;">
        <div class=" p-3 bg-white" style="width: 280px;">

            <span class="fs-5 fw-semibold">Modules</span>


        </div>

        <div style="width: 80%;margin-top: 20px;">

            <h4>Список модулей</h4>

            <div ng-show="loading" class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <table ng-show="!loading" class="table table-striped">
                <thead>
                <tr>

                    <th class="col-2">Название</th>
                    <th class="col-2">Включен</th>
                    <th class="col-2"></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="module in modulesList">

                    <td>{{module}}</td>

                    <td>
                        <span ng-show="modulesData[module].active" class="badge text-bg-success rounde">да</span>
                        <span ng-show="!modulesData[module].active" class="badge text-bg-danger rounde">нет</span>

                    </td>
                    <td>
                        <span ng-show="!modulesData[module].default">
                            <button ng-if="!modulesData[module].active" type="button" class="btn btn-success btn-sm"
                                    ng-click="activateModule(module)">Активировать
                            </button>
                            <button ng-if="modulesData[module].active" type="button" class="btn btn-danger btn-sm"
                                    ng-click="deactivateModule(module)">Деактивировать
                            </button>
                        </span>
                        <span class="badge text-bg-secondary rounded" ng-show="modulesData[module].default">базовый модуль</span>
                    </td>
                </tr>


                </tbody>
            </table>
        </div>
    </div>

</div>