var app = angular.module('massOperation', ['bw.paging']);

app.controller('index', ['$scope', '$http', '$location', '$sce', function ($scope, $http, $location, $sce) {


    $scope.catItems = [];
    $scope.page = 1;
    $scope.count = 1;

    $scope.idAsc = 0;
    //вернуть весь список без учета страниц
    $scope.noLimit = false;

    $scope.catCategories = [];
    $scope.selectedCat = ''

    $scope.targetCatCategories = [];
    $scope.targetSelectedCat = ''

    $scope.markedItems = {};

    $scope.catMsg = ''
    $scope.catMsgShow = false
    $scope.loadCategories = function () {
        $http(
            {
                url: '/catalog/massOperation/loadCategories/',

            }).then(function (response) {

            $scope.catCategories = response.data
            $scope.selectedCat = $scope.catCategories[0];

            $scope.targetCatCategories = response.data
            $scope.targetSelectedCat = $scope.catCategories[0];
        });
    }

    $scope.loadCatItems = function () {
        $http(
            {
                url: '/catalog/massOperation/loadCatItems/',
                params: {
                    page: $scope.page,
                    idAsc: $scope.idAsc,
                    catId: $scope.selectedCat.id,
                    noLimit: $scope.noLimit
                }
            }
        ).then(function (response) {
            $scope.catItems = response.data.data;
            $scope.count = response.data.count;
        })
    }
    $scope.setPage = function (page) {
        $scope.page = page
        $scope.loadCatItems()
    }

    $scope.loadCategories();

    $scope.$watch('idAsc', function () {
        $scope.loadCatItems()
    });
    $scope.$watch('selectedCat', function () {
        $scope.loadCatItems()
    });
    $scope.isMarked = function (id) {
        return id in $scope.markedItems;
    }

    $scope.checkClick = function (id) {
        if (id in $scope.markedItems) {
            delete $scope.markedItems[id];
        } else {
            $scope.markedItems[id] = '';
        }
        console.log($scope.markedItems)
    }

    $scope.countOfCheckedItems = function () {
        var i = 0;
        for (id in $scope.markedItems) {
            i++;
        }
        return i;
    }


    $scope.searchCatStr = ''
    $scope.targetSearchCatStr = ''
    $scope.loadCatItems()

    $scope.checkAll = function () {

    }

    $scope.moveToCategory = function () {
        $scope.catMsgShow = false
        console.log('в функцию попал')
        if ($scope.countOfCheckedItems() == 0) {
            $scope.showCatMsg('Не выбрали позиции!')
        } else if (($scope.selectedCat == '') || ($scope.selectedCat.name=="Без раздела")) {
            $scope.showCatMsg('Не выбрали исходный раздел!')
        } else{
            $http(
                {
                    url: '/catalog/massOperation/moveToCategory/',
                    params: {
                        markedItems: $scope.markedItems,
                        sourceCat: $scope.selectedCat,
                        targetCat: $scope.targetSelectedCat
                    }
                }
            ).then(function (response) {
                $scope.loadCatItems()
            })
        }

    }

    $scope.showCatMsg = function (msg) {
        $scope.catMsg = msg
        $scope.catMsgShow = true
    }

    $scope.saveField = function(fieldName,value,id){
        $http(
            {
                url: '/catalog/massOperation/saveField',
                params: {
                    fieldName: fieldName,
                    value:value,
                    itemId:id
                }
            }
        )
    }

}]);

app.filter('categories', [function () {
    return function (object, param) {
        var array = [];
        // var param = '';
        var reg = new RegExp("" + param + "", 'ui')
        angular.forEach(object, function (cat) {


            if (reg.test(cat.name))
                array.push(cat);
        });
        return array;
    };
}]);