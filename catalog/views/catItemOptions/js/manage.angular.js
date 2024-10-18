var app = angular.module('myApp', ['uiCatalog', 'commonUi']);

app.controller('myCtrl', function ($scope, $http) {
    $scope.selectedItems = [];
   
    // Функция, которая будет вызываться при изменении selectedItems
    $scope.onSelectItem = function (item) {
        console.log('Первый элемент selectedItems:', item);
        $scope.selectedItem = item
        $scope.getOptionsList()
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

    $scope.optionsListOfSelected = []
    $scope.getOptionsList = function () {
        $http.get('/catalog/api/getOptionsList', {
            params: { itemId: $scope.selectedItem.id }
        }).then(function (response) {
            // Успешный ответ от сервера
            $scope.optionsListOfSelected = response.data
            $scope.inputData = response.data
        }, function (error) {
            // Обработка ошибок
            console.error('Ошибка при получении списка опций:', error);
        });
    };

    $scope.removeOption = function (option) {
        $http.post('/catalog/api/deleteOption', {
            params: { itemId: $scope.selectedItem.id, option: option }
        }).then(()=>{
            $scope.getOptionsList()
        })
    }

    $scope.sendAttributes = function(itemId, attributes) {

        $http.post('/catalog/api/CatItemAttributesSet', {
            data: JSON.stringify({
                itemId: itemId,
                attributes: attributes,
                baseItem:$scope.selectedItem
            })
        }, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Явно указываем, что это AJAX-запрос
            }
        })
        .then(function(response) {
            // Обработка успешного ответа
            console.log('Response:', response.data);
        }, function(error) {
            // Обработка ошибок
            console.error('Error:', error);
        });
    };

    // Метод для изменения атрибута isBase
    $scope.isBaseChange = function(option) {
        console.log(option);

        // Извлечение itemId и атрибутов из объекта option
        const itemId = option.itemId;
        const attributes = {
            isBase: option.isBase // или любой другой атрибут, который вы хотите изменить
        };

        // Вызов метода отправки атрибутов
        $scope.sendAttributes(itemId, attributes);
    };

    // Пример входных данных
    $scope.inputData = [
        {
            "name": "Предпусковой подогреватель двигателя",
            "price": 231000,
            "url": "https://avtoros.com/upload/iblock/bfc/bfcc138a91477900cf0350547a159804.jpg",
            "group": "Шасси"
        },
        {
            "name": "Передняя гидравлическая лебедка",
            "price": 357500,
            "url": "https://avtoros.com/upload/iblock/81d/81d467a4fce9c375363c8413d1bad2ec.jpg",
            "group": "Шасси"
        },
        {
            "name": "Задняя электрическая лебедка",
            "price": 275000,
            "url": "https://avtoros.com/upload/iblock/e4a/e4a3596c3181fad014256d597cc8caa8.jpg",
            "group": "Шасси"
        }
    ]
    // Выходные данные будут храниться здесь
    $scope.outputData = [];
});
