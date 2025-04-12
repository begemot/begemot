var app = angular.module('schema', [])

app.service('schemaData', function () {

	this.data = window.schemaRawData || {}

	// Создаем копию данных, но гарантируем, что это объект
	this.originalData = JSON.parse(JSON.stringify(this.data)) || {}

	console.log('Загруженные данные схемы:', this.data)

	this.getData = function () {

		return this.data
	}

	this.getOriginalData = function () {
		return this.originalData
	}
})

app.controller('update', [
	'$scope',
	'$http',
	'schemaData',
	function ($scope, $http, schemaData) {
		$scope.allData = schemaData.getData()
		$scope.originalData = schemaData.getOriginalData()

		console.log('allData:', $scope.allData)
		console.log('originalData:', $scope.originalData)

		$scope.buttonState = {}

		$scope.isChanged = function (value, field) {

			// 	// Безопасное получение исходных данных
			const originalData = $scope.allData
			// console.log($scope.originalData['fields'][field])

			// 	// Сравнение значений с учетом типа данных
			const isValueChanged = value == $scope.originalData['fields'][field]['value']

			// 	// Оптимизированный возврат результата
			return !isValueChanged
			// return true;
		}

		$scope.updateField = function (schema, field) {
			let dataToSend = {
				schemaId: $scope.allData.schemaId,
				fieldId: schema.fieldId,
				value: schema.value,
				linkType: window.schemaLinkType,
				groupId: window.schemaGroupId,
			}

			$scope.buttonState[field] = {
				class: 'btn-primary',
				text: 'Сохранение...',
			}
			console.log(dataToSend)
			$http({
				method: 'POST',
				url: '/schema/api/updateField',
				headers: { 'Content-Type': 'application/json' },

				data: dataToSend,
			})
				.then(function (response) {
					console.log('Успешное обновление:', response.data)

					$scope.buttonState[field] = {
						class: 'btn-success',
						text: 'Сохранено',
					}
					setTimeout(() => {
						$scope.$apply(() => {
							$scope.buttonState[field] = {
								class: 'btn-primary',
								text: 'Сохранить',
							}
						})
					}, 2000)
				})
				.catch(function (error) {
					console.log('Ошибка обновления:', error)
					$scope.buttonState[field] = { class: 'btn-danger', text: 'Ошибка' }
				})
		}
	},
])
