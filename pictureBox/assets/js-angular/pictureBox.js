


var app = angular.module('pictureBox', ["dndLists", 'ngFileUpload']);




app.controller('gallery', ['$scope', '$http',function ($scope, $http) {
    // ['$scope', 'Upload', '$timeout', function ($scope, Upload, $timeout) {
    // $scope.galId = 'sqlLiteTest';
    //$scope.id = 1;



    $scope.testHook = '';

    $scope.pictureBoxDataObject = [];
    $scope.images = [];
    $scope.deleted = [];
    $scope.lastImageId = -1;

    $scope.search = {params: {deleted: false}};
    // $scope.search.params.deleted = false;


    $scope.titleModal = {
        id: '',
        title: '',
        alt: ''
    }

    $scope.allImagesModal = {
        id: '',
        image: '',
    }

    $scope.activeFilter = null;




    $scope.madeFilterActive = function(name){
        filter = $scope.config.imageFilters[name][0]
        console.log($scope.config.imageFilters[name])
        $scope.activeFilter.name = name
        $scope.activeFilter.width = filter.param.width
        $scope.activeFilter.height = filter.param.height

        $scope.currentPreviewSrc =$scope.allImagesModal.image[$scope.activeFilter.name]+'?'+_.random(1000);
        console.log($scope.currentPreviewSrc)
    }

    $scope.imagesUpdate = function (data) {

        $scope.images = Array();

        $scope.images = _.values(data.images)

        $scope.images = _.sortBy($scope.images, 'order')
        $scope.lastImageId = data.lastImageId;
        $scope.galList = data.subGalleryList;


        $scope.config = data.config

        firstFilter = _.keys($scope.config.imageFilters)[1]
        if (  $scope.activeFilter ==null){
            $scope.activeFilter = {
                name:firstFilter,
                width:$scope.config.imageFilters[firstFilter][0].param.width,
                height:$scope.config.imageFilters[firstFilter][0].param.height
            }
        }


    }


    $scope.imagesSortedKeys = [];

    $scope.getData = function () {
        $http.get('/pictureBox/api/GetData', {
            params: {
                galleryId: $scope.galId,
                id: $scope.id,
                subGallery:$scope.activeSubGallery
            }
        }).then(function (responce) {

            $scope.imagesUpdate(responce.data);
        });

    }

    $scope.setGallery = (gal)=>{
        $scope.activeSubGallery = gal;
        $scope.getData()
    }



    $scope.sendData = function () {
        $http.post('/pictureBox/api/obectSave', {

            images: $scope.images,
            deleted: $scope.deleted

        }, {
            params: {
                galleryId: $scope.galId,
                id: $scope.id,
                subGallery:$scope.activeSubGallery
            }
        })
    }


    $scope.orderSave = function () {
        $scope.sendData();
    }

    $scope.getLastImageId = function () {
        return $http.get('/pictureBox/api/getLastItemId', {
            params: {
                galleryId: $scope.galId,
                id: $scope.id
            }
        })
    }


    $scope.imageShownChange = function (imageId) {
        image = _.find($scope.images, {id: imageId})
        if (!_.has(image, 'params'))
            image.params = {show: 1}
        image.params.show = !image.params.show

        $scope.sendData()
    }

    $scope.imageFavChange = function (imageId) {
        image = _.find($scope.images, {id: imageId})
        if (!_.has(image, 'params'))
            image.params = {fav: 1}
        image.params.fav = !image.params.fav

        $scope.sendData()
    }

    $scope.imageDelete = function (imageId) {

        console.log($scope.deleted)
        // console.log(_.remove($scope.images,{id:imageId}))
        $scope.deleted = _.concat($scope.deleted, _.remove($scope.images, {id: imageId}));
        console.log($scope.deleted)
        $scope.sendData();


    }




}]);


app.directive('tiles', function () {
    return {
        restrict: 'E',

        templateUrl: '/protected/modules/pictureBox/assets/js-angular/tpl/tiles.html?123',
        link: function (scope, element, attrs) {
            console.log(attrs);

            scope.galId = attrs.galleryId;
            scope.id = attrs.id;

            scope.setGallery('default');
            scope.showAltTitleModal = function (i) {

                image = _.find(scope.images, {id: i.id})
                console.log(image)
                scope.titleModal = {
                    id: i.id,
                    title: image.title,
                    alt: image.alt
                }
            }

            $('#titleAltModal').on('hidden.bs.modal', function (e) {
                console.log(scope.titleModal)

                image = _.find(scope.images, {id: scope.titleModal.id})
                image.title = scope.titleModal.title
                image.alt = scope.titleModal.alt

                scope.sendData()

            })


            scope.showAllImagesModal = function (i) {

                image = _.find(scope.images, {id: i.id})
                console.log(image)

                scope.allImagesModal = {
                    id: image.id,
                    image: image,

                }
                scope.currentPreviewSrc =image[scope.activeFilter.name]+'?'+_.random(1000);

                // scope.titleModal = {
                //     id: i.id,
                //     title: image.title,
                //     alt: image.alt
                // }
            }




        }
    }
})

app.directive('upload', ['Upload', '$timeout', function (Upload, $timeout) {
    return {
        restrict: 'E',

        templateUrl: '/protected/modules/pictureBox/assets/js-angular/tpl/tilesUpload.html?12123',
        link: function (scope, element, attrs) {


            scope.galId = attrs.galleryId;
            scope.id = attrs.id;


            scope.uploadFiles = function (files, errFiles) {
                console.log(files)
                scope.files = files;
                scope.errFiles = errFiles;


                scope.filePointer = 0
                scope.uploadFile = function () {
                    scope.lastImageId++;
                    file = scope.files[scope.filePointer]
                    //scope.files = _.drop(scope.files)
                    console.log('Загружаем файл')
                    console.log(scope.filePointer)
                    console.log(file)
                    file.upload = Upload.upload({
                        url: '/pictureBox/api/upload',
                        data: {
                            file: file,
                            galleryId: scope.galId,
                            id: scope.id,
                            lastId: scope.lastImageId,
                            subGallery:scope.activeSubGallery
                        }
                    });

                    file.upload.then(function (response) {
                        console.log('Закончили загрузку');


                        response.data[0].order = scope.images.length
                        response.data[0].params = {show: true, fav: false}
                        scope.images.push(response.data[0])
                        console.log(response.data);

                        if (scope.filePointer < files.length - 1) {
                            scope.filePointer++
                            scope.uploadFile()
                        } else {
                            scope.sendData();
                        }

                        $timeout(function () {

                            file.result = response.data;

                        });

                    }, function (response) {
                        if (response.status > 0)
                            scope.errorMsg = response.status + ': ' + response.data;
                    }, function (evt) {

                        file.progress = Math.min(100, parseInt(100.0 *
                            evt.loaded / evt.total));

                    });
                }
                scope.uploadFile()


            }


        }
    }
}])

