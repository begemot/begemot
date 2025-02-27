var app = angular.module('schema', [])

app.service('schemaData', function () {
	this.data = window.schemaRawData || {}

	// Создаем копию данных, но гарантируем, что это объект
	this.originalData = JSON.parse(JSON.stringify(this.data)) || {}

	console.log('Загруженные данные схемы:', this.data)

	this.getData = function () {
		return Object.values(this.data)
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

		$scope.isChanged = function (schema, field) {
			// Добавляем валидацию входных данных
			if (!_.isObject(schema)) return false
			if (!_.isObject(field)) return false
			if (!_.get(schema, 'id') || !_.get(field, 'id')) {
				if (console && console.debug) {
					console.debug('Invalid schema or field structure:', schema, field)
				}
				return false
			}

			// Безопасное получение исходных данных
			const originalSchema = _.get($scope.originalData, '[1]')
			const originalData = _.get(originalSchema, 'data')

			// Проверка существования исходных данных
			if (!_.isArray(originalData)) {
				if (console && console.warn) {
					console.warn('Original data not found or not an array')
				}
				return false
			}

			// Поиск оригинального значения
			const originalField = _.find(originalData, {
				id: _.toString(field.id),
			})

			// Сравнение значений с учетом типа данных
			const isValueChanged = !_.isEqual(
				_.get(originalField, 'value'),
				_.get(field, 'value')
			)

			// Оптимизированный возврат результата
			return isValueChanged
		}

		$scope.updateField = function (schema, field) {
			let dataToSend = {
				schemaId: schema.id,
				fieldId: field.id,
				value: field.value,
				linkType: window.schemaLinkType,
				groupId: window.schemaGroupId,
			}

			$scope.buttonState[field.id] = {
				class: 'btn-primary',
				text: 'Сохранение...',
			}

			$http({
				method: 'POST',
				url: '/schema/api/updateField',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				transformRequest: function (obj) {
					var str = []
					for (var p in obj)
						str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]))
					return str.join('&')
				},
				data: dataToSend,
			})
				.then(function (response) {
					console.log('Успешное обновление:', response.data)
					if (response.data.success) {
						$scope.buttonState[field.id] = {
							class: 'btn-success',
							text: 'Сохранено',
						}
						$scope.originalData[schema.id][field.id] = field.value
						// Сбрасываем состояние кнопки через 2 секунды (опционально)
						setTimeout(() => {
							$scope.$apply(() => {
								$scope.buttonState[field.id] = {
									class: 'btn-primary',
									text: 'Сохранить',
								}
							})
						}, 2000)
					} else {
						$scope.buttonState[field.id] = {
							class: 'btn-danger',
							text: 'Ошибка',
						}
					}
				})
				.catch(function (error) {
					console.log('Ошибка обновления:', error)
					$scope.buttonState[field.id] = { class: 'btn-danger', text: 'Ошибка' }
				})
		}
	},
])
