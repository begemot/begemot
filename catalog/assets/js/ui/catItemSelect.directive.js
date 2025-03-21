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
			menuMode: '=?',
			selectedListTitle: '@?',
			hideId: '@?',
		},
		templateUrl:
			'/protected/modules/catalog/assets/js/ui//html/catItemSelect.template.html',
		link: function (scope) {
			scope.catItems = []
			console.log(scope.hideId)

			if (!angular.isDefined(scope.selectedListTitle)) {
				console.log('выставляем заголовок по умолчанию.')
				scope.selectedListTitle = 'Выбрано'
			}

			scope.categoriesFilterCallBack = function (data) {
				scope.filterCategories = data
				scope.debouncedLoadData()
			}
			scope.filterText = ''
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

			if (typeof scope.menuMode === 'undefined') {
				scope.menuMode = false // Значение по умолчанию
			}
			console.log(scope.menuMode)
			scope.selectedSingleItemId = undefined

			scope.modalCatFilter = false

			scope.showCatSelectModal = function () {
				scope.modalCatFilter = true
			}

			scope.selectListView = scope.selectedItemsView
			scope.selectItem = function (item) {
				var index = scope.catItems.indexOf(item)
				console.log(scope.menuMode)
				if (index !== -1) {
					if (scope.menuMode == false) {
						scope.catItems.splice(index, 1)
					} else {
						scope.selectedSingleItemId = item.id
						scope.selectedItems = []
					}

					scope.selectedItems.push(item)
					console.log(scope.selectedItems)
				}
				scope.onSelectChange({ items: scope.selectedItems })
			}

			scope.deselectItem = function (item) {
				scope.selectedSingleItemId = undefined
				var index = scope.selectedItems.indexOf(item)

				if (index !== -1) {
					if (scope.menuMode == false) {
						scope.selectedItems.splice(index, 1)
						scope.catItems.push(item)
					} else {
						scope.selectedItems = []
					}

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

						if (scope.hideId != undefined) {
							scope.catItems = scope.catItems.filter(item => {
								return item.id != scope.hideId
							})
						}
					})
					.catch(function (error) {
						console.error('Error loading catalog data:', error)
					})
			}
			scope.customComparator = function (item) {
				return -parseInt(item.id)
			}

			scope.selectAll = () => {
				console.log(scope.catItems)
				angular.forEach(scope.catItems, item => {
					scope.selectItem(item)
				})
			}

			//scope.debouncedLoadData()
		},
	}
})
