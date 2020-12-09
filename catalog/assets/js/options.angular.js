var app = angular.module('optionsImport', []);

app.controller('index', ['$scope', '$http', '$location', '$sce', '$timeout', function ($scope, $http, $location, $sce, $timeout) {

    $scope.catItems = []
    $scope.catCategories = []

    $scope.catItemSearchStr = ''
    $scope.catCategorySearchStr = ''

    $scope.selectedItem = null
    $scope.selectedCategory = null

    $scope.options = null
    $scope.panelView = false
    $scope.catPanelView = false

    $scope.inputText = 'Поиск позиций...'

    $scope.baseItemId = itemId

    $scope.loadOptions = function (id) {
        $http(
            {
                url: '/catalog/CatalogService/catalogItemOptions/',
                params: {
                    id: id
                }
            }
        ).then(function (response) {
            $scope.options = response.data

        })
    }

    $scope.loadOptionsFromCategory = function (id) {
        $http(
            {
                url: '/catalog/CatalogService/catalogCategoryItems/',
                params: {
                    id: id
                }
            }
        ).then(function (response) {
            $scope.options = response.data

        })
    }



    $scope.loadItems = function () {
        $http(
            {
                url: '/catalog/CatalogService/catalogItems/',
                params: {
                    nameFilter: $scope.catItemSearchStr
                }
            }
        ).then(function (response) {
            $scope.catItems = response.data

        })
    }

    $scope.loadCategories = function () {
        $http(
            {
                url: '/catalog/CatalogService/catergories/',
                params: {
                    nameFilter: $scope.catCategorySearchStr
                }
            }
        ).then(function (response) {
            $scope.catCategories  =[]
            $scope.addTree(response.data);
        })
    }


    $scope.loadItems()
    $scope.loadCategories()

    $scope.selectItem = function (item) {
        $scope.selectedItem = item
        $scope.selectedCategory = null
        $scope.catItemSearchStr = item.name
        $scope.loadOptions(item.id)
        console.log(item.name);
    }

    $scope.selectCategory = function (item) {
        $scope.selectedCategory = item
        $scope.selectedItem = null
        $scope.catCategorySearchStr = item.name
        $scope.loadOptionsFromCategory(item.id)
        // console.log(item.name);
    }

    $scope.hideItemsPanel = function () {

        $timeout(function () {

            $scope.panelView = false

        }, 300);
    }

    $scope.hideCatsPanel = function () {

        $timeout(function () {

            $scope.catPanelView = false
        }, 300);
    }

    $scope.makeImport = function () {
        $http(
            {
                url: '/catalog/CatItemOptions/makeImport/',
                method: 'POST',
                params: {
                    id: $scope.baseItemId,

                },
                data: {

                    options: $scope.options
                }
            }
        ).then(function (response) {


        })
    }

    $scope.removeOptions = function () {
        $http(
            {
                url: '/catalog/CatItemOptions/removeOptions/',
                method: 'POST',
                params: {
                    id: $scope.baseItemId,

                },
                data: {

                    options: $scope.options
                }
            }
        ).then(function (response) {


        })
    }

    $scope.addTree = function (tree){
        console.log(tree)
        for (key in tree){
            tree[key].name = '=='.repeat(tree[key].level) +  tree[key].name
            $scope.catCategories.push(tree[key]);

            if ('childs' in tree[key]){
                $scope.addTree(tree[key].childs)
            }
        }
    }

}]);

// app.filter('repeatSearch', [function () {
//     return function (object, param) {
//         var array = [];
//         // var param = '';
//         var reg = new RegExp("" + param + "", 'ui')
//         angular.forEach(object, function (item) {
//
//
//             if (reg.test(item.name))
//                 array.push(item);
//         });
//         return array;
//     };
// }]);