// var catalogUiModule = angular.module('uiCatalog', [])
angular.module('uiCatalog').directive('catItemList', function ($http) {
	return {
		restrict: 'E',
		scope: {
			selectedItems: '=',
			onDeselect: '&',
			showCats:'=?'
		},
		templateUrl:
			'/protected/modules/catalog/assets/js/ui/html/catalogItemList.html',
		link: function (scope) {

			if (typeof scope.showCats === 'undefined') {
                scope.showCats = false // Значение по умолчанию
            }
			console.log(scope.showCats )

			scope.deselectItem = function (item) {
				scope.onDeselect({ item: item })
			}
		},
	}
})
