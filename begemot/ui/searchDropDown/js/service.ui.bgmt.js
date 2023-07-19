angular.module('UiBgmt', []);

angular.module('UiBgmt').service('UiManager', ['$http', function ($http) {

    this.elementsCount = 0;
    this.modalsCollection = [];
    this.registerUiObject = function (uiObject){
        this.elementsCount++
        return this.elementsCount
    }

}])

//app.directive('tiles', ['$http', 'galleryControl', function ($http, galleryControl) {