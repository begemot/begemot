


var app = angular.module('schema', []);


app.controller('update', ['$scope', '$http', 'schemaData', function ($scope, $http, schemaData) {


    console.log(schemaData.data);
    $scope.allData = schemaData.data;
}]);