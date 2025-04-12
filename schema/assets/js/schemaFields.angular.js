
angular.module('schemaFieldApp', ['dndLists', 'apiServiceModule'])
    .controller('SchemaFieldController', ['$scope', 'apiService', '$timeout', function ($scope, apiService, $timeout) {
        var vm = this;
        var dragging = false;

        $scope.UoM = null
        $scope.schemaFields = null
        apiService.getAllData().then((data) => {
            $scope.data = data
            vm.schemaFields = data.schemaFields
            vm.units = data.uomList
            console.log($scope.data)
        })



        // Mock data
        // vm.schemaFields = [
        //     {
        //         "_id": 985,
        //         "name": "EngineVolume",
        //         "schemaId": 1,
        //         "type": "String",
        //         "order": 10,
        //         "UoFId": 1017
        //     },
        //     {
        //         "_id": 986,
        //         "name": "Power",
        //         "schemaId": 1,
        //         "type": "Int",
        //         "order": 20,
        //         "UoFId": 1018
        //     },
        //     {
        //         "_id": 987,
        //         "name": "Price",
        //         "schemaId": 1,
        //         "type": "Float",
        //         "order": 30,
        //         "UoFId": 1019
        //     }
        // ];

        vm.types = ['String', 'Int', 'Float'];

        // // Units data with formatted display
        // vm.units = [
        //     {
        //         "_id": "67f8f4405ee18756d20b663d",
        //         "id": 1017,
        //         "name": "РљСѓР±РёС‡РµСЃРєРёРµ СЃР°РЅС‚РёРјРµС‚СЂС‹",
        //         "abbreviation": "СЃРјВі",
        //         "nameWithAbbr": "РљСѓР±РёС‡РµСЃРєРёРµ СЃР°РЅС‚РёРјРµС‚СЂС‹ (СЃРјВі)",
        //         "description": null
        //     },
        //     {
        //         "_id": "67f8f4405ee18756d20b663e",
        //         "id": 1018,
        //         "name": "Р›РѕС€Р°РґРёРЅС‹Рµ СЃРёР»С‹",
        //         "abbreviation": "Р».СЃ.",
        //         "nameWithAbbr": "Р›РѕС€Р°РґРёРЅС‹Рµ СЃРёР»С‹ (Р».СЃ.)",
        //         "description": "РњРѕС‰РЅРѕСЃС‚СЊ РґРІРёРіР°С‚РµР»СЏ"
        //     },
        //     {
        //         "_id": "67f8f4405ee18756d20b663f",
        //         "id": 1019,
        //         "name": "Р СѓР±Р»Рё",
        //         "abbreviation": "в‚Ѕ",
        //         "nameWithAbbr": "Р СѓР±Р»Рё (в‚Ѕ)",
        //         "description": "Р РѕСЃСЃРёР№СЃРєРёРµ СЂСѓР±Р»Рё"
        //     }
        // ];

        vm.getUnitById = function (id) {
            return vm.units.find(function (unit) {
                return unit.id === id;
            }) || { name: '', abbreviation: '' };
        };

        vm.onDragStart = function (field) {
            dragging = true;
        };

        vm.onDragEnd = function () {
            dragging = false;
            $timeout(function () {
                vm.updateOrders();
            }, 100);
        };

        vm.updateOrders = function () {
            if (dragging) return;

            angular.forEach(vm.schemaFields, function (field, index) {
                field.order = (index + 1) * 10;
            });
            vm.saveAllFields();
        };

        vm.saveField = function (field) {
            console.log('Field saved:', field);
        };

        vm.saveAllFields = function () {
            console.log('All fields saved:', vm.schemaFields);
            apiService.saveSchemaFields(1, vm.schemaFields)
        };
    }]);