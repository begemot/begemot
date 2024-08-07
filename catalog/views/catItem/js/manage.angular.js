var app = angular.module('myApp', ['uiCatalog', 'commonUi']);

app.controller('myCtrl', function ($scope, $http) {
    $scope.selectedItems = [];
    $scope.celectedItem = undefined
    // Функция, которая будет вызываться при изменении selectedItems
    $scope.onSelectItem = function (item) {
        console.log('Первый элемент selectedItems:', item);
        $scope.selectedItem = item
        // Здесь можно добавить дополнительную логику для обработки элемента item
    };

    // Функция, которая будет вызываться, когда selectedItems становится пустым
    $scope.deselectItem = function () {
        console.log('selectedItems пустой');
        // Здесь можно добавить дополнительную логику, которая должна выполняться, когда selectedItems становится пустым
    };

    // Используем $watchCollection для отслеживания изменений в массиве selectedItems
    $scope.$watchCollection('selectedItems', function (newVal, oldVal) {
        if (newVal !== oldVal) {
            if (newVal.length > 0) {
                $scope.onSelectItem(newVal[0]);
            } else {
                $scope.deselectItem();
            }
        }
    });





    // Пример входных данных
    $scope.inputData = [
        {

          "url": "https://avtoros.com/upload/iblock/bfc/bfcc138a91477900cf0350547a159804.jpg",
        
        },

    ]
    // Выходные данные будут храниться здесь
    $scope.outputData = [];
});
