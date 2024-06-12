var app = angular.module('myApp', [])
app.controller('myCtrl', function ($scope, $http) {
	$scope.catItems = []
	$scope.schemaLinks = []
	$scope.selectedItems = []
	$scope.selectedSchemaLink = null
	$scope.schemaLinkData = null
	$scope.filterText = ''

	$scope.attachButtonVisible = false

	$scope.msg = 'Нет сообщений.'

	// Function to check if the attach button should be visible
	$scope.updateAttachButtonVisibility = function () {
		$scope.attachButtonVisible =
			$scope.selectedItems.length > 0 && $scope.selectedSchemaLink !== null
	}

	// Watch for changes in selectedItems and selectedSchemaLink
	$scope.$watch('selectedItems', $scope.updateAttachButtonVisibility, true)
	$scope.$watch('selectedSchemaLink', $scope.updateAttachButtonVisibility)

	// Load data from server /catalog/api/itemListJson
	$scope.loadCatItems = function (filterText) {
		$http
			.get('/catalog/api/itemListJson', {
				params: { name: filterText },
			})
			.then(function (response) {
				// Exclude already selected items
				$scope.catItems = response.data.filter(function (item) {
					return !$scope.selectedItems.some(function (selectedItem) {
						return selectedItem.id === item.id
					})
				})
			})
			.catch(function (error) {
				console.error('Error loading catalog data:', error)
			})
	}

	// Load data from server /schema/api/schemaLinks
	$scope.loadSchemaLinks = function () {
		$http
			.get('/schema/api/schemaLinks?except=CatItem')
			.then(function (response) {
				$scope.schemaLinks = response.data
			})
			.catch(function (error) {
				console.error('Error loading schema data:', error)
			})
	}

	// Initial data load
	$scope.loadCatItems('')
	$scope.loadSchemaLinks()

	// Watch for changes in filterText and reload data
	$scope.$watch('filterText', function (newVal) {
		$scope.loadCatItems(newVal)
	})

	$scope.selectItem = function (item) {
		var index = $scope.catItems.indexOf(item)
		if (index !== -1) {
			$scope.catItems.splice(index, 1)
			$scope.selectedItems.push(item)
		}
	}

	$scope.deselectItem = function (item) {
		var index = $scope.selectedItems.indexOf(item)
		if (index !== -1) {
			$scope.selectedItems.splice(index, 1)
			$scope.catItems.push(item)
			// Refresh filter after deselecting an item
			$scope.loadCatItems($scope.filterText)
		}
	}

	$scope.selectSchemaLink = function (item) {
		$scope.selectedSchemaLink = item
		$scope.schemaLinkData = undefined
		// Load data for selected schemaLink
		$http
			.get('/schema/api/getSchemaData', {
				params: { linkType: item.linkType, linkId: item.linkId },
			})
			.then(function (response) {
				console.log(response.data[1].data)
				$scope.schemaLinkData = response.data[1].data
			})
			.catch(function (error) {
				console.error('Error loading schemaLink data:', error)
			})
	}

	$scope.isSchemaLinkSelected = function (item) {
		return $scope.selectedSchemaLink && $scope.selectedSchemaLink.id === item.id
	}

	// Function to submit selected data to the server
	$scope.submitData = function () {
		$scope.msg = 'Отправляем данные...'
		var data = {
			selectedItems: $scope.selectedItems,
			selectedSchemaLink: $scope.selectedSchemaLink,
		}

		$http
			.post('/catalog/catalogAndSchema/attachSchema', data)
			.then(function (response) {
				$scope.msg = 'Отправлено и обработано!'
				console.log('Data submitted successfully:', response)
				// Handle success response
			})
			.catch(function (error) {
				$scope.msg = 'Ошибка!'
				console.error('Error submitting data:', error)
				// Handle error response
			})
	}
})
