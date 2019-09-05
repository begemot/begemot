var app = angular.module('app', []);

app.controller('tagsProcess', ['$scope', '$http', function ($scope, $http) {

    $scope.count = undefined
    $scope.executed = undefined
    $scope.lastStatus = undefined

    $scope.executeOneTask = function(){

        $http.get('/seo/webParser/tagProcess').then(function (response) {
            console.log (response.data);
            request = response.data

            if($scope.count==undefined){

                $http.get('/seo/webParser/UnprocessedTagTaskCount').then(function (response) {
                    console.log(response.data)
                    $scope.count = parseInt(response.data)
                    $scope.executed =0
                })
            }

            if (request.status =='ok'){
                $scope.executed++
                $scope.executeOneTask();

            }

        });
    }

    // $scope.$watch ('lastStatus', $scope.executeOneTask())
    $scope.executeOneTask()


}])