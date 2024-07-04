uiModule.directive('categorySelect', function($http) {
    return {
        restrict: 'E',
        scope: {
            selectedCategories: '=',
            businessLogicEnabled: '=' // Входной параметр для включения бизнес логики
        },
        templateUrl: '/protected/modules/catalog/assets/js/ui/html/categorySelect.template.html',
        link: function(scope) {
            scope.categories = [];
            const restrictedCategories = ['sold', 'archive', 'catalog', 'stock'];

            // Функция для обновления списка выбранных категорий
            scope.updateSelectedCategories = function(selectedCategory) {
                if (scope.businessLogicEnabled && restrictedCategories.includes(selectedCategory.name)) {
                    // Если выбрана одна из ограниченных категорий, снимаем выбор с других ограниченных категорий
                    scope.categories.forEach(function(category) {
                        if (category.name !== selectedCategory.name && restrictedCategories.includes(category.name)) {
                            category.selected = false;
                        }
                    });
                }
                scope.selectedCategories = scope.categories.filter(function(category) {
                    return category.selected;
                }).map(function(category) {
                    return category.id;
                });
            };

            // Запрос к API для получения списка категорий
            $http.get('/catalog/api/getCatList').then(function(response) {
                var data = response.data;
                scope.categories = Object.keys(data).map(function(key) {
                    return {
                        id: data[key].id,
                        name: data[key].name,
                        order: data[key].order,
                        level: data[key].level,
                        selected: false
                    };
                });

                // Сортировка категорий по полю order
                scope.categories.sort(function(a, b) {
                    return a.order - b.order;
                });
            });
        }
    };
});