var app = angular.module('performApp', ['ngResource', 'ngSanitize', 'bw.paging', 'ngRoute'])
    .config(['$routeProvider', ($routeProvider) => {

        $routeProvider.when('/', {

            controller: 'edit'
        }).when('/tab/:tabName', {

            controller: 'edit'
        })
    }]);


