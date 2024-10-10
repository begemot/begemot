var app = angular.module('myApp', ['uiCatalog', 'commonUi']);

app.controller('myCtrl', function ($scope, $http) {
    $scope.selectedItems = [];
    $scope.selectedItem = undefined;
    $scope.itemSelected = false;

    // Функция, которая будет вызываться при изменении selectedItems
    $scope.onSelectItem = function (item) {
        console.log('Первый элемент selectedItems:', item);
        $scope.selectedItem = item;
        $scope.itemSelected = true;
 
    };

    // Функция, которая будет вызываться, когда selectedItems становится пустым
    $scope.deselectItem = function () {
        console.log('selectedItems пустой');
        $scope.itemSelected = false;
        $scope.outputData = []; // Очистка данных, когда ничего не выбрано
    };

    // Отслеживаем изменения в массиве selectedItems
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
    ];

    // Выходные данные будут храниться здесь
    $scope.outputData = [];

    // Функция для получения изображений по id
    $scope.fetchImages = function () {
        var url = '/catalog/api/ExportImagesOfCatItem'; // URL для API
        var params = { id: $scope.selectedItem.id }; // Параметры запроса (id)

        $http({
            method: 'GET',
            url: url,
            params: params
        }).then(function successCallback(response) {
            console.log('Изображения получены:', response.data);
           // $scope.outputData = response.data; // Сохраняем полученные данные
            $scope.inputData = response.data;
        }, function errorCallback(error) {
            console.error('Ошибка при получении изображений:', error);
        });
    };
});
