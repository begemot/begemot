var app = angular.module('myApp', ['uiCatalog']);

app.controller('myCtrl', function ($scope, $http) {

	$scope.selectedItems = [];
	$scope.schemaLinks = []
	$scope.selectedSchemaLink = null
	$scope.schemaLinkData = null
	$scope.attachButtonVisible = false
	$scope.updateAttachButtonVisibility = function () {
		$scope.attachButtonVisible = $scope.selectedItems.length > 0 && $scope.selectedSchemaLink != null;
	};

	$scope.$watch('selectedItems', $scope.updateAttachButtonVisibility, true);

	$scope.$watch('selectedSchemaLink', $scope.updateAttachButtonVisibility)

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

	$scope.selectSchemaLink = function (item) {
		$scope.selectedSchemaLink = item
		$scope.schemaLinkData = undefined
		// Load data for selected schemaLink
		$http
			.get('/schema/api/getSchemaData', {
				params: { linkType: item.linkType, linkId: item.linkId },
			})
			.then(function (response) {
				console.log(response)
				$scope.schemaLinkData = response.data
			})
			.catch(function (error) {
				console.error('Error loading schemaLink data:', error)
			})
	}

	$scope.isSchemaLinkSelected = function (item) {
		return $scope.selectedSchemaLink && $scope.selectedSchemaLink.id === item.id
	}

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
	$scope.onSelectAndUnselect = function (items) {
		console.log('Filtered Items:', items);
		// You can add additional logic here if needed
	};

	$scope.loadSchemaLinks()
});
