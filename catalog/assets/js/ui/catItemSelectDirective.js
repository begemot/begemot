var catalogSelectorModule = angular.module('uiCatalog', []);
catalogSelectorModule.directive('catItemSelect', function($http) {
    return {
        restrict: 'E',
        scope: {
           
         
            selectedItems: '=',
            attachButtonVisible: '=',
            msg: '=',
            onSelectChange: '&'
        },
        templateUrl: '/protected/modules/catalog/assets/js/ui//html/catalogItemSelect.html' ,
        link: function(scope) {
            scope.selectItem = function(item) {
                var index = scope.catItems.indexOf(item);
                if (index !== -1) {
                    scope.catItems.splice(index, 1);
                    scope.selectedItems.push(item);
                }
                scope.onSelectChange({items: scope.selectedItems});
            };

            scope.deselectItem = function(item) {
                var index = scope.selectedItems.indexOf(item);
                if (index !== -1) {
                    scope.selectedItems.splice(index, 1);
                    scope.catItems.push(item);
                    // Refresh filter after deselecting an item
                    scope.onSelectChange({items: scope.selectedItems});
                }
            };

            scope.submitData = function() {
                scope.msg = 'Отправляем данные...';
                var data = {
                    selectedItems: scope.selectedItems
                };

                $http.post('/catalog/catalogAndSchema/attachSchema', data)
                    .then(function(response) {
                        scope.msg = 'Отправлено и обработано!';
                        console.log('Data submitted successfully:', response);
                    })
                    .catch(function(error) {
                        scope.msg = 'Ошибка!';
                        console.error('Error submitting data:', error);
                    });
            };



            scope.loadCatItems = function (filterText) {
                $http.get('/catalog/api/itemListJson', { params: { name: filterText } })
                    .then(function (response) {
                        scope.catItems = response.data.filter(function (item) {
                            return !scope.selectedItems.some(function (selectedItem) {
                                return selectedItem.id === item.id;
                            });
                        });
                    })
                    .catch(function (error) {
                        console.error('Error loading catalog data:', error);
                    });
            };
            scope.loadCatItems('');
        }
    };
});
