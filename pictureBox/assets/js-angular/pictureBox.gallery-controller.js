angular.module('pictureBox').controller('gallery', ['$scope', '$http', 'galleryControl', 'values', function ($scope, $http, galleryControl, values) {
    // ['$scope', 'Upload', '$timeout', function ($scope, Upload, $timeout) {
    // $scope.galId = 'sqlLiteTest';
    //$scope.id = 1;
    $scope.galList = null;
    $scope.subGalConfigs = null;
    $scope.values = values;

    var loadGalList = function () {
        if (this.galList == null) {
            $http.get('/pictureBox/api/getGalleries', {
                params: {
                    galleryId: values.galId,
                    id: values.id,
                    subGallery: galleryControl.activeGallery
                }
            }).then(function (responce) {

                $scope.galList = Object.keys(responce.data)
                $scope.subGalConfigs = responce.data
            });
        }
    }
    loadGalList();
    $scope.getGalList = function () {
        return galleryControl.galList
    }
    $scope.testHook = '';


    $scope.images = [];
    $scope.deleted = [];
    $scope.lastImageId = -1;

    $scope.search = {params: {deleted: false}};
    // $scope.search.params.deleted = false;

    galleryControl.titleModal = {
        id: '',
        title: '',
        alt: ''
    }

    galleryControl.allImagesModal = {
        id: '123',
        image: '23',
    }
    $scope.getPreview = function () {
       
    
        return galleryControl.currentPreviewSrc;
    }

    $scope.getConfig = function () {
        return galleryControl.config;
    }
    $scope.getAllImagesModal = function () {
        return galleryControl.allImagesModal;
    }

    $scope.getTitleModal = function () {
        return galleryControl.titleModal;
    }

    galleryControl.activeFilter = null;
    $scope.getActiveFilterName = function () {
        return galleryControl.activeFilter.name;
    }
    $scope.imagesSortedKeys = [];

    //Переменная, через которую внешние компоненты понимают в какую галлерею сохранять

    $scope.madeSubGalleryActive = function (name) {
        galleryControl.activeSubGallery = name;

    }


    $scope.madeFilterActive = function (name) {
        filter = galleryControl.config.imageFilters[name][0]
        console.log(galleryControl.config.imageFilters[name])


        // galleryControl.activeFilter.name = name
        // galleryControl.activeFilter.width = filter.param.width
        //galleryControl.activeFilter.height = filter.param.height
        galleryControl.setActiveFilter({
            name: name,
            width: filter.param.width,
            height: filter.param.height
        });
        galleryControl.currentPreviewSrc = galleryControl.allImagesModal.image[galleryControl.activeFilter.name] + '?' + _.random(1000);
        console.log(galleryControl.currentPreviewSrc)
    }

    galleryControl.dataCollection = {};

}]);