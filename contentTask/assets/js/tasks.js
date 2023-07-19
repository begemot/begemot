var app = angular.module('app', ['ngResource', 'bw.paging']);

app.config(['$httpProvider', function ($httpProvider) {
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}]);

app.config(['$locationProvider', function ($locationProvider) {
    $locationProvider.html5Mode(true).hashPrefix('!');
}]);

app.factory('ContentTask', [
    '$resource', function ($resource) {

        return $resource('/api/contentTask/contentTask/:id', {}, {});
    }
]);

app.controller('mainCtrl', ['$scope', '$http', '$location', 'ContentTask', function ($scope, $http, $location, ContentTask) {
    $scope.tasks = [];
     ContentTask.get().$promise.then(function (data) {
        $scope.tasks = data.data.contentTask
        console.log($scope.tasks)
    })

//создание
// create an instance as well
//    var task = new ContentTask({name: '0123'});
//
//
//    var savePromise = task.$save();


    //удаление
    //ContentTask.delete({'id': 3});


}])

app.controller('added', ['$scope', '$http', '$location', 'ContentTask', function ($scope, $http, $location, ContentTask) {
    $scope.resultItems = {};
    $scope.taskId = $location.search().id;

    $scope.activePage = 1
    $scope.recordsCount = 0

    $scope.searchId = '';
    $scope.searchTitle = '';

    $scope.makeSearch = function () {
        $scope.resultItems = [];
        $http.get('/contentTask/contentTask/ajaxAddedSearch?page=' + $scope.activePage + '&id=' + $scope.searchId + '&title=' + $scope.searchTitle + '&taskId=' + $scope.taskId).then(function (response) {
            $scope.resultItems = response.data.data;
            $scope.recordsCount = response.data.count;

        });
    }

    $scope.setPage = function (page) {
        $scope.activePage = page
        $scope.makeSearch()
    }

    $scope.removeFromTask= function(index){
        id = $scope.resultItems[index]['id']
       console.log($scope.resultItems[index]);
        $http.get('/contentTask/contentTask/ajaxRemoveFromTask?' +'id=' + id + '&taskId=' + $scope.taskId).then(function (response) {
             $scope.resultItems.splice(index, 1)

        });
    }

    $scope.makeSearch();

}])


app.controller('search', ['$scope', '$http', '$location', 'ContentTask', function ($scope, $http, $location, ContentTask) {
    $scope.resultItems = {};
    $scope.taskId = $location.search().id;

    $scope.activePage = 1
    $scope.recordsCount = 0

    $scope.searchId = '';
    $scope.searchTitle = '';


    $scope.makeSearch = function () {
        $scope.resultItems = [];
        $http.get('/contentTask/contentTask/ajaxSearch?page=' + $scope.activePage + '&id=' + $scope.searchId + '&title=' + $scope.searchTitle + '&taskId=' + $scope.taskId).then(function (response) {
            $scope.resultItems = response.data.data;
            $scope.recordsCount = response.data.count;
            console.log($scope.resultItems)
        });
    }

    $scope.setPage = function (page) {
        $scope.activePage = page
        $scope.makeSearch()
    }

    $scope.addToTask = function (index) {
        id = $scope.resultItems[index]['id']

        $http.get('/contentTask/contentTask/ajaxAddTOTask?' +'id=' + id + '&taskId=' + $scope.taskId).then(function (response) {
            $scope.resultItems[index]['added'] = 1;
        });
    }

    $scope.makeSearch();


}])

app.controller('update', ['$scope', '$http', '$location', 'ContentTask', function ($scope, $http, $location, ContentTask) {

    $scope.taskId = taskId;


    $scope.typesChoiceVisible = false;
    $scope.actonsVisible = false;
    $scope.fieldsVivsible = false;


    $scope.types = null
    $scope.selectedType = null;
    $scope.typeActs = null;
    $scope.typeFields = null;

    ContentTask.get({id: $scope.taskId}, function (response) {
        $scope.contentTask = response.data.contentTask
        $scope.taskTitle = $scope.contentTask.name
        $scope.taskText = $scope.contentTask.text
        console.log($scope.contentTask.codeDate);
        $scope.selectedType = $scope.contentTask.type
        $scope.getActions();
        $scope.getFields();

    });


    $scope.taskTitle = ''//contentTask.name
    $scope.taskText = ''

    $http.get('/contentTask/contentTask/typesList').then(function (response) {
        $scope.types = response.data;
        console.log($scope.types)
        $scope.typesChoiceVisible = true;
        //$scope.selectType($scope.types[0]);
    });


    $scope.getActions = function () {
        $http.get('/contentTask/contentTask/typeActs', {
            params: {
                type: $scope.selectedType,
                id: $scope.taskId
            }
        }).then(function (response) {
            $scope.typeActs = response.data
            $scope.actonsVisible = true;
        });
    }

    $scope.getFields = function () {
        $http.get('/contentTask/contentTask/typeFields', {
            params: {
                type: $scope.selectedType,
                id: $scope.taskId
            }
        }).then(function (response) {
            $scope.typeFields = response.data
            $scope.fieldsVivsible = true;
        });
    }

    $scope.sevaNameAndText = function () {

        var task = new ContentTask({name: '0123'});
        var savePromise = task.$save();


    }

    $scope.updateType = function () {
        console.log($scope.selectedType)

        $scope.getActions();
        $scope.getFields();

    }

    $scope.sendCreateData = function () {
        //console.log($scope.typeFields);
        //console.log($scope.typeActs);
        var selectedTypes = [];
        for (var i = 0, len = $scope.typeActs.length; i < len; i++) {
            if ($scope.typeActs[i].selected !== undefined && $scope.typeActs[i].selected !== false) {
                console.log($scope.typeActs[i].selected);
                selectedTypes.push($scope.typeActs[i]);
            }
        }
        //типы подготовили отправляем
        //console.log(selectedTypes);

        var selectedFields = [];
        for (key in $scope.typeFields) {
            var obj = $scope.typeFields[key]
            if (obj.selected !== undefined && obj.selected !== false) {
                selectedFields.push(obj)
            }
        }
        //console.log(selectedFields)

        var dataForSend =
        {
            id: $scope.taskId,
            name: $scope.taskTitle,
            text: $scope.taskText,
            type: $scope.selectedType,
            actionsList: selectedTypes,
            fieldsList: selectedFields
        };

        $http.post('/contentTask/contentTask/createAndSerrialise', dataForSend)
        //.then(successCallback, errorCallback);

    }

    $scope.generateNewAccessCode = function (){

        $http.get('/contentTask/contentTask/ajaxGenerateCode', {
            params: {
                id: $scope.taskId
            }
        }).then(function (response) {
            $scope.contentTask.accessCode = response.data;
        });

    }


}])


app.directive('ckEditor', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ngModel) {
                var ck = CKEDITOR.replace(elm[0]);
                if (!ngModel) return;
                ck.on('instanceReady', function () {
                    ck.setData(ngModel.$viewValue);
                });
                function updateModel() {
                    scope.$apply(function () {
                        ngModel.$setViewValue(ck.getData());
                    });
                }
                ck.on('change', updateModel);
                ck.on('key', updateModel);
                ck.on('dataReady', updateModel);

                ngModel.$render = function (value) {
                    ck.setData(ngModel.$viewValue);
                };
            }
        };
    });