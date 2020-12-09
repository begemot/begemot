var app = angular.module('app', ['bw.paging']);

app.controller('tagsProcess', ['$scope', '$http', function ($scope, $http) {

    $scope.count = undefined
    $scope.executed = undefined
    $scope.lastStatus = undefined

    $scope.executeOneTask = function () {


        $http.get('/seo/webParser/tagProcess').then(function (response) {
            console.log (response.data);

            request = response.data

            if ($scope.count == undefined) {

                $http.get('/seo/webParser/UnprocessedTagTaskCount').then(function (response) {
                    console.log(response.data)
                    $scope.count = parseInt(response.data)
                    $scope.executed = 0
                })
            }

            if (request.status == 'ok') {
                $scope.executed++
                $scope.executeOneTask();

            }

        });
    }

    // $scope.$watch ('lastStatus', $scope.executeOneTask())
    $scope.executeOneTask()


}])

app.controller('tagsTable', ['$scope', '$http', function ($scope, $http) {
    $scope.cols = {};
    $scope.tagsData = [];


    $scope.dataCount = 0;
    $scope.activePage = 1;

    $scope.sortCol = '';
    $scope.sortAsc = 0;

    $http.get('/seo/webParser/tagsDataCount').then(function (response) {
        $scope.dataCount = response.data

    })

    $scope.computePageTags = function(id){
        $http.get('/seo/webParser/tagProcess/id/'+id).then(function (response) {
            $scope.loadData()
        });
    }

    loadCols = function () {
        $http.get('/seo/webParser/getTagFields').then(function (response) {

            for (var i = 0; i < response.data.length; i++) {

                $scope.cols[response.data[i]] = {
                    'enabled': true,
                    'sort': 'none',
                    'name': response.data[i]
                }
            }
            $scope.loadData()
            console.log($scope.cols)
        })
    }

    $scope.loadData = function () {
        $http.get('/seo/webParser/loadTagsData/page/'+$scope.activePage+'/sort/'+$scope.sortCol+'/asc/'+$scope.sortAsc).then(function (response) {
            $scope.tagsData = response.data
        })

    }

    $scope.setPage = function(page){
        $scope.activePage = page
        $scope.loadData()
    }

    $scope.setSort = function(item){
        console.log(123);
        $scope.sortCol = item
        $scope.sortAsc = ($scope.sortAsc+1) % 2
        $scope.loadData()
    }

    $scope.checkTag = function (tag,value){
        if (tag=='strong'){
            if (!value  || value >1){
                return 'wrong'
            }
        }
        if (tag=='h1'){
            if (!value  || value >1){
                return 'wrong'
            }
        }

        return ''
    }

    $scope.goToPageUrl = function(id){

        $http.get('/seo/webParser/linkToPage/id/'+id).then(function (response) {

            window.open(
                response.data,
                '_blank'
            );
        })
    }

    loadCols()
}])