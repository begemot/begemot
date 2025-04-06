angular.module('myApp', [])
  .controller('MainController', function () {
    var ctrl = this;
    ctrl.filters = {
      _id: '',
      title: '',
      schemaGroup: ''
    };
    ctrl.collection = initialData;
    console.log(ctrl.filters);
    // Сортировка
    ctrl.sortField = '_id'; // Поле по умолчанию для сортировки
    ctrl.reverseSort = false; // Направление сортировки

    // Функция для сортировки
    ctrl.sortBy = function (field) {
      if (ctrl.sortField === field) {
        ctrl.reverseSort = !ctrl.reverseSort;
      } else {
        ctrl.sortField = field;
        ctrl.reverseSort = false;
      }
    };

    ctrl.getFilteredItems = function () {
      return ctrl.collection.filter(function (item) {
        // Проверяем, что элемент существует и имеет свойство _id
        if (!item || !item._id) {
          return false; // Пропускаем этот элемент
        }
        return (
          item._id.toLowerCase().includes(ctrl.filters._id.toLowerCase()) &&
          item.title.toLowerCase().includes(ctrl.filters.title.toLowerCase()) &&
          item.schemaGroup.toString().includes(ctrl.filters.schemaGroup)
        );
      });
    };

  });