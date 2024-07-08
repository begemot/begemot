var app = angular.module('myApp', ['uiCatalog','commonUi'])

app.controller('myCtrl', function ($scope, $http) {
	$scope.selectedItems = []
    $scope.selectedCategories  = []

    $scope.$watch('selectedCategories', function(newVal, oldVal) {
        if (newVal !== oldVal) {
            console.log(newVal)
            // scope.categories.forEach(function(category) {
            //     category.selected = newVal.includes(category.id);
            // });
        }
    }, true);

    $scope.moveToStandartCat = function(cat) {
        // Проверка, что есть выбранные элементы для перемещения
        if ($scope.selectedItems.length > 0) {
            $http.post('/catalog/api/MoveItemsToStandartCat', { selectedItems: $scope.selectedItems,where:cat})
                .then(function(response) {
                    // Обработка успешного ответа
                    console.log('Items moved to stock successfully:', response.data);
                })
                .catch(function(error) {
                    // Обработка ошибки
                    console.error('Error moving items to stock:', error);
                });
        } else {
            console.log('No items selected.');
        }
    };
})
