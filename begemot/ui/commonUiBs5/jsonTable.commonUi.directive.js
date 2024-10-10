angular.module('commonUi').directive('jsonTable', function ($http, $timeout) {
    return {
        restrict: 'E',
        scope: {
            inputData: '=',
            outputData: '=',
            sendDataUrl: '@',
            additionalDataForSend: '=',
        },
        templateUrl:
            '/protected/modules/begemot/ui/commonUiBs5/templates/jsonTable.commonUi.template.html',
        link: function (scope) {
            console.log(scope.additionalDataForSend)
            scope.stepBystepSend = false;
            scope.data = angular.copy(scope.inputData); // Initialize with a copy of inputData
            scope.showModal = false;
            scope.modalJson = '';
            scope.appendData = false;

            scope.isSending = false; // Variable to track sending status
            scope.progress = 0; // Variable to track progress percentage

            scope.removeRow = function (index) {
                scope.data.splice(index, 1);
            }

            scope.addRow = function () {
                if (scope.data.length > 0) {
                    var newRow = {};
                    angular.forEach(scope.data[0], function (value, key) {
                        newRow[key] = '';
                    });
                    scope.data.push(newRow);
                }
            }

            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            scope.saveData = async function () {
                scope.outputData = angular.copy(scope.data);
                var cleanData = angular.copy(scope.data, []);
                var totalItems = cleanData.length;
                var sentItems = 0;

                scope.isSending = true; // Show progress bar
                scope.progress = 0; // Reset progress

                for (let item of cleanData) {
                    delete item.$$hashKey;
                    var dataToSend = {
                        data: [item],
                        additionalData: scope.additionalDataForSend,
                    };

                    try {
                        await $http.post(scope.sendDataUrl, dataToSend);
                        sentItems++;
                        scope.progress = Math.round((sentItems / totalItems) * 100);
                        scope.$apply(); // Update scope changes
                        if (scope.stepBystepSend)
                            await sleep(3000);
                    } catch (error) {
                        console.error('Error sending data element:', error);
                        alert('Error sending data element');
                      
                    }
                }

                scope.isSending = false; // Hide progress bar
                if (sentItems === totalItems) {
                    alert('All data sent successfully');
                }
            }

            scope.importJson = function () {
                scope.modalJson = JSON.stringify(scope.data, null, 2);
                var modalElement = new bootstrap.Modal(
                    document.getElementById('jsonModal')
                );
                modalElement.show();
            }

            scope.exportJson = function () {
                var cleanData = angular.copy(scope.data, []);
                angular.forEach(cleanData, function (item) {
                    delete item.$$hashKey;
                });
                scope.modalJson = JSON.stringify(cleanData, null, 2);
                var modalElement = new bootstrap.Modal(
                    document.getElementById('jsonModal')
                );
                modalElement.show();
            }

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
            }

            scope.isImageUrl = function (url) {
                return /^https?:\/\/.*\.(jpeg|jpg|gif|png)$/.test(url);
            }

            // Watch for changes in inputData and update scope.data accordingly
            scope.$watch('inputData', function (newVal, oldVal) {
                if (newVal !== oldVal) {
					console.log(newVal)
                    scope.data = angular.copy(newVal);
                }
            }, true);
        },
    }
});
