<?php
Yii::app()->clientScript->registerScriptFile('/protected/modules/modules/assets/js/modulesService.angular.js', 2);
?>

<script>
var app = angular.module('migration', ['modules'])

app.controller('ctrl', ['$scope', '$http', 'modulesService', function($scope, $http, modulesService) {

    $scope.modulesList = null
    $scope.migrationList = null

    $scope.nameOfNewMigration = null
    $scope.loading = false;

    modulesService.loadData().then(() => {

        $scope.modulesList = modulesService.getActiveModulesData()

        //$scope.selectModule($scope.modulesList[0]);
        $scope.selectAllModules()

    });

    $scope.selectModule = (module) => {
        $scope.activeModule = module
        $scope.loading = true
        $scope.unselectAllModules()
        $scope.getMigrationList(module)
    }

    $scope.getMigrationList = (module) => {
        $http({
            method: 'GET',
            url: '/migrations/default/getMigrationsList',
            params: {
                moduleName: module
            }
        }).then((response) => {

            $scope.migrationList = response.data
            $scope.loading = false
        })
    }

    $scope.getAllMigrationList = () => {
        $http({
            method: 'GET',
            url: '/migrations/default/getAllMigrations',

        }).then((response) => {

            $scope.migrationList = response.data
            $scope.loading = false
        })
    }


    $scope.createNewMigrationFile = () => {
        if ($scope.nameOfNewMigration != null)
            $http({
                method: 'GET',
                url: '/migrations/default/newMigrationFile',
                params: {
                    fileName: $scope.nameOfNewMigration,
                    module: $scope.activeModule
                }
            }).then(() => {
                $scope.nameOfNewMigration = null
                $scope.getMigrationList($scope.activeModule)
            })
    }

    $scope.upMigration = (file, module) => {

        $http({
            method: 'GET',
            url: '/migrations/default/upMigration',
            params: {
                module: module,
                fileName: file
            }
        }).then(() => {
            $scope.updateMigrationsList()
        })
    }
    $scope.downMigration = (file, module) => {
        $http({
            method: 'GET',
            url: '/migrations/default/downMigration',
            params: {
                module: module,
                fileName: file
            }
        }).then(() => {
            $scope.updateMigrationsList()
        })
    }

    $scope.updateMigrationsList = () => {
        if ($scope.allModulesSelected) {
            $scope.getAllMigrationList()
        } else {
            $scope.getMigrationList($scope.activeModule)
        }
    }


    $scope.allModulesSelected = true;
    $scope.selectAllModules = () => {
        $scope.getAllMigrationList()
        $scope.allModulesSelected = true;

    }

    $scope.unselectAllModules = () => {
        $scope.allModulesSelected = false;
    }
}])
</script>
<div ng-app="migration" ng-controller="ctrl">
    <div style="display: flex;margin-top: 20px;">
        <div class=" p-3 bg-white" style="width: 280px;">

            <span class="fs-5 fw-semibold">Модуль миграций</span>

            <ul class="list-unstyled ps-0">
                <li class="mb-1">


                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li class="">

                            <a href="" class="link-dark d-inline-flex text-decoration-none rounded"
                                ng-class="{'text-bg-primary':allModulesSelected}" ng-click="selectAllModules()">Все
                                миграции</a>
                        </li>
                        <li ng-repeat="module in modulesList" class="">

                            <a href="" class="link-dark d-inline-flex text-decoration-none rounded"
                                ng-class="{'text-bg-primary':module==activeModule && !allModulesSelected}"
                                ng-click="selectModule(module)">{{module}}</a>
                        </li>
                    </ul>

                </li>

            </ul>
        </div>
        <div style="width: 80%;margin-top: 20px;">

            <h4>Список миграций в модуле</h4>
            <form class="row row-cols-lg-auto g-3 align-items-center">
                <div class="col-12">

                    <div class="input-group">

                        <input type="text" class="form-control" placeholder="Название миграции"
                            ng-model="nameOfNewMigration">
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary" ng-click="createNewMigrationFile()">Создать миграцию
                    </button>
                </div>
            </form>
            <div ng-show="loading" class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <table ng-show="!loading" class="table table-striped">
                <thead>
                    <tr>

                        <th class="col-2">Название</th>
                        <th class="col-6">Описание</th>
                        <th class="col-2">Применено</th>
                        <th class="col-2">Модуль</th>
                        <th class="col-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="migration in migrationList">

                        <td>{{migration.className}}</td>
                        <td>{{migration.description}}</td>
                        <td>
                            <span ng-if="migration.confirmed">да</span>
                            <span ng-if="!migration.confirmed">нет</span>
                        </td>
                        <td>{{migration.moduleName}}</td>
                        <td>
                            <button ng-if="!migration.confirmed" type="button" class="btn btn-success btn-sm"
                                ng-click="upMigration(migration.className,migration.moduleName)">Применить
                            </button>
                            <button ng-if="migration.confirmed" type="button" class="btn btn-danger btn-sm"
                                ng-click="downMigration(migration.className,migration.moduleName)">Откатить
                            </button>
                        </td>
                    </tr>


                </tbody>
            </table>
        </div>
    </div>

</div>
<style>
.dropdown-toggle {
    outline: 0;
}

.btn-toggle {
    padding: .25rem .5rem;
    font-weight: 600;
    color: rgba(0, 0, 0, .65);
    background-color: transparent;
}

.btn-toggle:hover,
.btn-toggle:focus {
    color: rgba(0, 0, 0, .85);
    background-color: #d2f4ea;
}

.btn-toggle::before {
    width: 1.25em;
    line-height: 0;
    content: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%280,0,0,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
    transition: transform .35s ease;
    transform-origin: .5em 50%;
}

.btn-toggle[aria-expanded="true"] {
    color: rgba(0, 0, 0, .85);
}

.btn-toggle[aria-expanded="true"]::before {
    transform: rotate(90deg);
}

.btn-toggle-nav a {
    padding: .1875rem .5rem;
    margin-top: .125rem;
    margin-left: 1.25rem;
}

.btn-toggle-nav a:hover,
.btn-toggle-nav a:focus {
    background-color: #d2f4ea;
}

.scrollarea {
    overflow-y: auto;
}
</style>

<script>
/* global bootstrap: false */
(() => {
    'use strict'
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })
})()
</script>