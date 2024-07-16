angular.module('commonUi')
    .directive('jsonTable', function ($http) {
        return {
            restrict: 'E',
            scope: {
                inputData: '=',
                outputData: '=',
                sendDataUrl: '@',
                additionalDataForSend:'='
            },
            templateUrl: '/protected/modules/begemot/ui/commonUiBs5/templates/jsonTable.commonUi.template.html',
            link: function (scope) {
                console.log(scope.additionalDataForSend)
                // scope.data = angular.copy(scope.inputData);

                // scope.data = angular.copy(scope.inputData);
                scope.data = [
                    {
                        "name": "Турист",
                        "price": 0,
                        "url": "https://avtoros.com/shaman/config//img/tourist1.jpg",
                        "group": "Салон"
                      },
                      {
                        "name": "Охотник",
                        "price": 850000,
                        "url": "https://avtoros.com/shaman/config//img/hunter1.jpg",
                        "group": "Салон"
                      }
                ]
                scope.showModal = false;
                scope.modalJson = '';
                scope.appendData = false;  // Добавлено свойство для галочки

                scope.removeRow = function (index) {
                    scope.data.splice(index, 1);
                };

                scope.addRow = function () {
                    if (scope.data.length > 0) {
                        var newRow = {};
                        angular.forEach(scope.data[0], function (value, key) {
                            newRow[key] = '';
                        });
                        scope.data.push(newRow);
                    }
                };

                scope.saveData = function () {
                    scope.outputData = angular.copy(scope.data);
                    // Удаляем $$hashKey перед отправкой
                    var cleanData = angular.copy(scope.data, []);
                    angular.forEach(cleanData, function (item) {
                        delete item.$$hashKey;
                    });

                    // Отправка данных методом POST
                    $http.post(scope.sendDataUrl, {data:cleanData,additionalData:scope.additionalDataForSend})
                        .then(function (response) {
                            alert('Data saved and sent successfully');
                        })
                        .catch(function (error) {
                            console.error('Error sending data:', error);
                            alert('Error sending data');
                        });
                };

                scope.importJson = function () {
                    scope.modalJson = JSON.stringify(scope.data, null, 2);
                    var modalElement = new bootstrap.Modal(document.getElementById('jsonModal'));
                    modalElement.show();
                };

                scope.exportJson = function () {
                    // Удаляем $$hashKey перед экспортом
                    var cleanData = angular.copy(scope.data, []);
                    angular.forEach(cleanData, function (item) {
                        delete item.$$hashKey;
                    });
                    scope.modalJson = JSON.stringify(cleanData, null, 2);
                    var modalElement = new bootstrap.Modal(document.getElementById('jsonModal'));
                    modalElement.show();
                };

                scope.applyModal = function () {
                    try {
                        var newData = JSON.parse(scope.modalJson);
                        if (scope.appendData) {
                            scope.data = scope.data.concat(newData);
                        } else {
                            scope.data = newData;
                        }
                        var modalElement = document.getElementById('jsonModal');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        modal.hide();
                    } catch (e) {
                        alert('Invalid JSON');
                    }
                };

                scope.isImageUrl = function (url) {
                    return /^https?:\/\/.*\.(jpeg|jpg|gif|png)$/.test(url);
                };
            }
        };
    });
