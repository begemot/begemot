angular.module('uiCatalog').directive('catItemSelect', function ($http) {
	return {
		restrict: 'E',
		transclude: false,
		scope: {
			selectedItems: '=',
			attachButtonVisible: '=',
			msg: '=',
			onSelectChange: '&',
			selectedItemsView: '=',
			showCats: '=?',
		},
		templateUrl:
			'/protected/modules/catalog/assets/js/ui//html/catItemSelect.template.html',
		link: function (scope) {
			scope.categoriesFilterCallBack = function (data) {
				scope.filterCategories = data
				scope.debouncedLoadData()
			}
			scope.filterText = 'argo'
			scope.filterCategories = []

			// scope.$watch(
			// 	'filterCategories',
			// 	function (newVal, oldVal) {
			// 		if (newVal !== oldVal) {
			// 			console.log(newVal)
			// 			// scope.categories.forEach(function(category) {
			// 			//     category.selected = newVal.includes(category.id);
			// 			// });
			// 		}
			// 	},
			// 	true
			// )

			if (typeof scope.showCats === 'undefined') {
				scope.showCats = false // Значение по умолчанию
			}

			scope.modalCatFilter = false

			scope.showCatSelectModal = function () {
				scope.modalCatFilter = true
			}

			scope.selectListView = scope.selectedItemsView
			scope.selectItem = function (item) {
				var index = scope.catItems.indexOf(item)
				if (index !== -1) {
					scope.catItems.splice(index, 1)
					scope.selectedItems.push(item)
				}
				scope.onSelectChange({ items: scope.selectedItems })
			}

			scope.deselectItem = function (item) {
				console.log(item)
				var index = scope.selectedItems.indexOf(item)

				if (index !== -1) {
					scope.selectedItems.splice(index, 1)
					scope.catItems.push(item)
					// Refresh filter after deselecting an item
					scope.onSelectChange({ items: scope.selectedItems })
				}
			}

			scope.submitData = function () {
				scope.msg = 'Отправляем данные...'
				var data = {
					selectedItems: scope.selectedItems,
				}

				$http
					.post('/catalog/catalogAndSchema/attachSchema', data)
					.then(function (response) {
						scope.msg = 'Отправлено и обработано!'
						console.log('Data submitted successfully:', response)
					})
					.catch(function (error) {
						scope.msg = 'Ошибка!'
						console.error('Error submitting data:', error)
					})
			}

			scope.debouncedLoadData = _.debounce(loadCatItems, 1000) // 300 мс задержка

			function loadCatItems() {
				$http
					.post('/catalog/api/itemListJson', {
						name: scope.filterText,
						catFilterIds: scope.filterCategories,
					})
					.then(function (response) {
						scope.catItems = response.data.filter(function (item) {
							return !scope.selectedItems.some(function (selectedItem) {
								return selectedItem.id === item.id
							})
						})
					})
					.catch(function (error) {
						console.error('Error loading catalog data:', error)
					})
			}
			scope.customComparator = function(item) {
				return -parseInt(item.id);
			};
			scope.debouncedLoadData()
		},
	}
})
