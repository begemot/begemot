var app = angular.module('myApp', ['uiCatalog'])

app.controller('myCtrl', function ($scope, $http) {
	$scope.selectedItems = []
	$scope.itemId = undefined

	$scope.getModifsOfItem = function (itemId) {
		$http({
			method: 'GET',
			url: '/catalog/modification/list/itemId/' + itemId, // Исправленный URL в соответствии с маршрутом
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then(function (response) {
				// response.data содержит массив модификаций
				$scope.selectedItems = response.data.map(function (mod) {
					return {
						id: mod.toItemId.toString(), // Приводим к строке
						name: mod.toItemName,
						image: '', // Пустое значение, если API не возвращает
						article: '', // Пустое значение, если API не возвращает
					}
				})
				$scope.itemId = itemId // Сохраняем текущий itemId
				console.log('Модификации получены:', $scope.selectedItems) // Исправлено $scope.modifications на $scope.selectedItems
			})
			.catch(function (error) {
				console.error('Ошибка при получении модификаций:', error)
				$scope.selectedItems = [] // Очищаем список при ошибке (исправлено на selectedItems)
			})
	}

	$scope.init = data => {
		// console.log(data)
		$scope.itemId = data
		$scope.getModifsOfItem($scope.itemId)
	}

	$scope.sendSelectedItems = items => {
		$http({
			method: 'POST',
			url: '/catalog/modification/sync', // URL вашего API
			headers: {
				'Content-Type': 'application/json',
			},
			data: {
				modifications: items,
				itemId: $scope.itemId,
			},
		})
			.then(function (response) {
				console.log('Модификация успешно добавлена:', response)
			})
			.catch(function (error) {
				console.error('Ошибка при добавлении модификации:', error)

				alert('Ошибка: ' + error.error)
			})
	}

	// $scope.schemaLinks = []
	// $scope.selectedSchemaLink = null
	// $scope.schemaLinkData = null
	// $scope.attachButtonVisible = false

	$scope.updateAttachButtonVisibility = function () {
		$scope.attachButtonVisible =
			$scope.selectedItems.length > 0 && $scope.selectedSchemaLink != null
	}

	$scope.$watch('selectedItems', $scope.updateAttachButtonVisibility, true)

	// $scope.$watch('selectedSchemaLink', $scope.updateAttachButtonVisibility)

	// $scope.selectSchemaLink = function (item) {
	// 	$scope.selectedSchemaLink = item
	// 	$scope.schemaLinkData = undefined
	// 	// Load data for selected schemaLink
	// 	$http
	// 		.get('/schema/api/getSchemaData', {
	// 			params: { linkType: item.linkType, linkId: item.linkId },
	// 		})
	// 		.then(function (response) {
	// 			console.log(response.data[1].data)
	// 			$scope.schemaLinkData = response.data[1].data
	// 		})
	// 		.catch(function (error) {
	// 			console.error('Error loading schemaLink data:', error)
	// 		})
	// }

	$scope.isSchemaLinkSelected = function (item) {
		return $scope.selectedSchemaLink && $scope.selectedSchemaLink.id === item.id
	}

	$scope.submitData = function () {
		$scope.msg = 'Отправляем данные...'
		// var data = {
		// 	selectedItems: $scope.selectedItems,
		// 	selectedSchemaLink: $scope.selectedSchemaLink,
		// }

		// $http
		// 	.post('/catalog/catalogAndSchema/attachSchema', data)
		// 	.then(function (response) {
		// 		$scope.msg = 'Отправлено и обработано!'
		// 		console.log('Data submitted successfully:', response)
		// 		// Handle success response
		// 	})
		// 	.catch(function (error) {
		// 		$scope.msg = 'Ошибка!'
		// 		console.error('Error submitting data:', error)
		// 		// Handle error response
		// 	})
	}

	$scope.onSelectAndUnselect = function (items) {
		console.log('Filtered Items1:', items)
		$scope.sendSelectedItems(items)
		// You can add additional logic here if needed
	}
})
