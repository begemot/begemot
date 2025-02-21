var app = angular.module('schema', []);

app.service('schemaData', function () {
    this.data = window.schemaRawData || {};
    
    // Создаем копию данных, но гарантируем, что это объект
    this.originalData = JSON.parse(JSON.stringify(this.data)) || {};

    console.log("Загруженные данные схемы:", this.data);

    this.getData = function () {
        return Object.values(this.data);
    };

    this.getOriginalData = function () {
        return this.originalData;
    };
});

app.controller('update', ['$scope', '$http', 'schemaData', function ($scope, $http, schemaData) {
    $scope.allData = schemaData.getData();
    $scope.originalData = schemaData.getOriginalData();

    console.log("Данные в контроллере (преобразованные):", $scope.allData);

    // Храним состояние кнопок для каждого поля
    $scope.buttonState = {};

    $scope.isChanged = function (schema, field) {
        if (!schema || !field || !schema.id || !field.id) {
            return false; // Если данных нет, не считаем их измененными
        }
        
        let originalValue = $scope.originalData[schema.id]?.[field.id];
    
        return originalValue !== field.value;
    };
    

    $scope.updateField = function (schema, field) {
        let dataToSend = {
            schemaId: schema.id, 
            fieldId: field.id, 
            value: field.value, 
            linkType: window.schemaLinkType,
            groupId: window.schemaGroupId
        };
    
        console.log("Отправляемые данные:", dataToSend);
    
        // Меняем кнопку на "Сохранение..."
        $scope.buttonState[field.id] = { class: "btn-primary", text: "Сохранение..." };
    
        $http({
            method: 'POST',
            url: '/schema/api/updateField',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            transformRequest: function (obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: dataToSend
        })
        .then(function (response) {
            console.log("Успешное обновление:", response.data);
            if (response.data.success) {
                $scope.buttonState[field.id] = { class: "btn-success", text: "Сохранено" };
                // Обновляем оригинальные данные
                $scope.originalData[schema.id][field.id] = field.value;
            } else {
                $scope.buttonState[field.id] = { class: "btn-danger", text: "Ошибка" };
            }
        })
        .catch(function (error) {
            console.log("Ошибка обновления:", error);
            $scope.buttonState[field.id] = { class: "btn-danger", text: "Ошибка" };
        });
    };
}]);
