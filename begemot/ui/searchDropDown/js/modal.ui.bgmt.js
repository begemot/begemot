angular.module('UiBgmt').directive('modal', ['$http', 'UiManager', function ($http, UiManager) {
    return {
        restrict: 'E',
        transclude: true,
        scope: {
            title: '@',
            modalObject: '=modalObject',
            modalId:'=modalId'
        },
        templateUrl: '/protected/modules/begemot/ui/searchDropDown/tpl/modal.html',


        link: function (scope, element) {

            this.getBootstrapModal = function () {
                return UiManager.modalsCollection['#modal' + scope.uiElementId]
            }
            scope.uiElementId = UiManager.registerUiObject(this)
            console.log('#modal' + scope.uiElementId)
            scope.modalId = '#modal' + scope.uiElementId
            angular.element(element).children().attr('id', 'modal' + scope.uiElementId)
            UiManager.modalsCollection['#modal' + scope.uiElementId]= new bootstrap.Modal('#modal' + scope.uiElementId, {
                    keyboard: false
                })

            scope.modalObject = this
        }
    }
}]);

angular.module('UiBgmt').directive('modalAndButton', ['$http', 'UiManager', function ($http, UiManager) {
    return {
        restrict: 'E',
        transclude: true,
        scope: {
            modalTitle: '@',
            btnLabel: '@'

        },
        templateUrl: '/protected/modules/begemot/ui/searchDropDown/tpl/modalAndButton.html',
        link: function (scope) {
            scope.modalObject = {1: 1};
            scope.modalId=0
            scope.showModal = function () {

             //   bootstrapModal = scope.modalObject.getBootstrapModal()
                // bootstrapModal.show()
                    //  console.log(bootstrapModal)
                console.log(scope.modalId)
                UiManager.modalsCollection[scope.modalId].show()
                // bootstrapModal.modalObject.bootstrapModal.show();
            }
        }
    }
}]);