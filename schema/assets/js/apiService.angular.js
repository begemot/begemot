angular.module('apiServiceModule', [])
    .factory('apiService', ['$http', '$q', function ($http, $q) {
        return {
            // Получить список единиц измерения
            getUoMList: function () {
                return $http.get('/schema/api/SUoMList')
                    .then(response => response.data);
            },

            // Получить поля схемы по ID схемы
            getSchemaFields: function (schemaId) {
                return $http.get(`/schema/api/schemaFieldList/schemaId/${schemaId}`)
                    .then(response => response.data);
            },

            // Сохранить поля схемы
            saveSchemaFields: function (schemaId, fieldsData) {
                return $http.post(`/schema/api/saveFieldsList`, fieldsData)
                    .then(response => response.data)
                    .catch(error => $q.reject(error.data || 'Ошибка сохранения полей схемы'));
            },

            // Получить список схем
            getSchemaList: function () {
                return $http.get('/schema/api/schemaList')
                    .then(response => response.data);
            },

            // Получить все данные (UoM, схемы, поля схемы)
            getAllData: function () {
                return $q.all([
                    this.getUoMList(),
                    this.getSchemaList(),
                    this.getSchemaFields(1) // Предполагаем, что нужен ID схемы = 1
                ]).then(([uomList, schemaList, schemaFields]) => ({
                    uomList,
                    schemaList,
                    schemaFields
                }));
            }
        };
    }]);