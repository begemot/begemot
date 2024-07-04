// var catalogUiModule = angular.module('uiCatalog', [])
angular.module('uiCatalog').directive('catItemCatList', function ($http) {
	return {
		restrict: 'E',
		scope: {
			itemId: '=',

		},
		templateUrl:
			'/protected/modules/catalog/assets/js/ui/html/catalogItemLCats.html',
		link: function (scope) {
			// Функция для получения данных о категориях
			scope.getCategories = function () {
				$http.get('/catalog/api/getCategoriesOfCatItem', {
					params: { itemId: scope.itemId }
				}).then(function (response) {
					scope.categories = response.data;
				}, function (error) {
					console.error('Error fetching categories:', error);
				});
			};

			// Следить за изменением itemId и загружать данные
			scope.$watch('itemId', function (newVal, oldVal) {
				if (newVal) {
					scope.getCategories();
				}
			});
		},
	}
})
