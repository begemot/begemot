var app = angular.module('pictureBox', ["dndLists", 'ngFileUpload','UiBgmt']);


app.directive('tiles', ['$http', 'galleryControl', function ($http, galleryControl) {
    return {
        restrict: 'E',
        scope: {},
        templateUrl: '/protected/modules/pictureBox/assets/js-angular/tpl/tiles.html?1223',
        link: function (scope, element, attrs) {


            scope.galId = attrs.galleryId;
            scope.id = attrs.id;

            scope.activeGallery = attrs.activeGallery;


            scope.sendData = function () {
                $http.post('/pictureBox/api/obectSave', {

                    images: scope.images,
                    deleted: scope.deleted

                }, {
                    params: {
                        galleryId: scope.galId,
                        id: scope.id,
                        subGallery: scope.activeGallery
                    }
                })
            }

            scope.imageShownChange = function (imageId) {
                image = _.find(scope.images, {id: imageId})
                if (!_.has(image, 'params'))
                    image.params = {show: 1}
                image.params.show = !image.params.show

                scope.sendData()
            }

            scope.imageFavChange = function (imageId) {
                image = _.find(scope.images, {id: imageId})
                if (!_.has(image, 'params'))
                    image.params = {fav: 1}
                image.params.fav = !image.params.fav

                scope.sendData()
            }

            scope.imageDelete = function (imageId) {

                //  console.log(scope.deleted)
                // console.log(_.remove($scope.images,{id:imageId}))
                scope.deleted = _.concat(scope.deleted, _.remove(scope.images, {id: imageId}));
                //  console.log(scope.deleted)
                scope.sendData();


            }

            scope.orderSave = function () {
                scope.sendData();
            }


            scope.setGallery = (gal) => {
                galleryControl.activeSubGallery = gal;
                scope.getData()
            }

            scope.getDataHook = scope.getData = function () {
                $http.get('/pictureBox/api/GetData', {
                    params: {
                        galleryId: scope.galId,
                        id: scope.id,
                        subGallery: scope.activeGallery
                    }
                }).then(function (responce) {

                    scope.imagesUpdate(responce.data);
                });

            }
            scope.imagesUpdate = function (data) {

                scope.images = Array();

                scope.images = _.values(data.images)

                scope.images = _.sortBy(scope.images, 'order')
                scope.lastImageId = data.lastImageId + 0;
                scope.galList = data.subGalleryList;


                galleryControl.config = data.config

                firstFilter = _.keys(galleryControl.config.imageFilters)[1]
                if (galleryControl.activeFilter == null) {
                    galleryControl.setActiveFilter({
                        name: firstFilter,
                        width: galleryControl.config.imageFilters[firstFilter][0].param.width,
                        height: galleryControl.config.imageFilters[firstFilter][0].param.height
                    })
                }

                scope.updateExternalData()
            }

            scope.setGallery(scope.activeGallery);

            scope.showAltTitleModal = function (i) {

                image = _.find(scope.images, {id: i.id})
                console.log(image)
                galleryControl.titleModal = {
                    id: i.id,
                    title: image.title,
                    alt: image.alt
                }
                scope.setGallery(scope.activeGallery)
            }

            $('#titleAltModal').on('hidden.bs.modal', function (e) {

                if (scope.activeGallery != galleryControl.activeSubGallery) return;
                console.log(galleryControl.titleModal)

                image = _.find(scope.images, {id: galleryControl.titleModal.id})
                image.title = galleryControl.titleModal.title
                image.alt = galleryControl.titleModal.alt

                scope.sendData()
                $('#titleAltModal').modal('hide');
            })

            $('#resizeModal').on('shown', function () {
                // code to be executed when the modal is shown
                console.log('окно показалось');
                $(this).css('margin-top', '-400px');
            });

            $('#resizeModal').on('hidden', function () {
                // code to be executed when the modal is hidden
                $(this).css('margin-top', '-800px');
                console.log('окно скрылось');
            });

            scope.showAllImagesModal = function (i) {

                image = _.find(scope.images, {id: i.id})
                console.log(image)

                galleryControl.allImagesModal = {
                    id: image.id,
                    image: image,

                }
                galleryControl.currentPreviewSrc = image[galleryControl.activeFilter.name] + '?' + _.random(1000);
                scope.setGallery(scope.activeGallery)
                // scope.titleModal = {
                //     id: i.id,
                //     title: image.title,
                //     alt: image.alt
                // }
            }


            scope.getLastImageId = function () {
                return $http.get('/pictureBox/api/getLastItemId', {
                    params: {
                        galleryId: scope.galId,
                        id: scope.id
                    }
                })
            }

            scope.updateExternalData = function () {
                data = {};
                data.lastImageId = scope.lastImageId;
                data.sendData = scope.sendData;
                data.images = scope.images;
                data.getData = scope.getData;

                galleryControl.dataCollection[scope.activeGallery] = data;

                console.log(galleryControl.dataCollection);
            }

        }
    }
}])

app.directive('upload', ['Upload', '$timeout', 'galleryControl', function (Upload, $timeout, galleryControl) {
    return {
        restrict: 'E',

        templateUrl: '/protected/modules/pictureBox/assets/js-angular/tpl/tilesUpload.html?123',
        link: function (scope, element, attrs) {


            scope.galId = attrs.galleryId;
            scope.id = attrs.id;


            scope.uploadFiles = function (files, errFiles) {
                console.log(files)
                scope.files = files;
                scope.errFiles = errFiles;


                scope.filePointer = 0
                scope.uploadFile = function () {

                    file = scope.files[scope.filePointer]
                    //scope.files = _.drop(scope.files)
                    console.log('Загружаем файл')
                    console.log(scope.filePointer)

                    imageId = 0;
                    imageId = parseInt(galleryControl.dataCollection[galleryControl.activeSubGallery].lastImageId);
                    imageId++;
                    galleryControl.dataCollection[galleryControl.activeSubGallery].lastImageId = imageId;
                    images = galleryControl.dataCollection[galleryControl.activeSubGallery].images;
                    file.upload = Upload.upload({
                        url: '/pictureBox/api/upload',
                        data: {
                            file: file,
                            galleryId: scope.galId,
                            id: scope.id,

                            lastId: imageId,
                            //  lastId: scope.lastImageId,
                            subGallery: galleryControl.activeSubGallery
                        }
                    });

                    file.upload.then(function (response) {
                        console.log('Закончили загрузку');


                        response.data[0].order = images.length
                        response.data[0].params = {show: true, fav: false}
                        images.push(response.data[0])
                        console.log(response.data);

                        if (scope.filePointer < files.length - 1) {
                            scope.filePointer++
                            scope.uploadFile()
                        } else {
                            galleryControl.dataCollection[galleryControl.activeSubGallery].sendData();
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

