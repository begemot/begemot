angular.module('pictureBox').controller('massResize', ['$scope', '$http', 'galleryControl', 'values', function ($scope, $http, galleryControl, values) {

    $scope.galId = values.galId
    $scope.id = values.id
    $scope.activeGallery = values.activeGallery
    $scope.activeImageIndex = 0

    $scope.settingsForm = {
        galleryId: 'catalogCategory',
        tag: 'catalogImage',
        width: 475,
        height: 235,
        ids: '4319 4322 4320 4321 4268 4265 4292 4343 4328 4344 4315 4341 4323 4331 4332 4309 4310 4342 4345 4336 4304 4269 4305 4285 4301 4303 4290 4339 4340 4346 4347 4326 4314 4294 4318 4278 4250 4334 4311 4308 4312 4313 4275 4295 4335 4333 4298 4296 4267 4317 4300 4297 4281 4293 4274 4277 4286 4288 4272 4324 4327 4325 4287 4289 4283 4291 4284 4279 4282 4270 4280'
    }
    

    galleryControl.activeFilter.width = $scope.settingsForm.width
    galleryControl.activeFilter.height = $scope.settingsForm.height
    galleryControl.activeFilter.name = $scope.settingsForm.tag

    console.log(galleryControl.activeFilter);

    $scope.IDsArray = [];
    $scope.parseIds = () => {
        $scope.IDsArray = $scope.settingsForm.ids.split(' ')
        console.log($scope.IDsArray);
    }

    $scope.parseIds();


    $scope.formChange = () => {
        // console.log($scope.settingsForm);
    }


    $scope.getData = () => {
        // console.log($scope.IDsArray);
        $http.get('/pictureBox/api/GetData', {
            params: {
                galleryId: $scope.settingsForm.galleryId,
                id: $scope.id,
                subGallery: $scope.activeGallery
            }
        }).then(function (responce) {

            $scope.interfaceUpdate(responce.data);
        });
    }
    $scope.nextImage = () => {

        $scope.id = $scope.IDsArray.shift()

        $scope.activeImageIndex = 0
        $scope.getData()
    }
    $scope.nextImage()
    $scope.getData()

    $scope.interfaceUpdate = function (data) {
        if (data.images.length==0) return;
        $scope.galleryData = _.values(data.images);

        $scope.image = $scope.galleryData.shift()

        $scope.activeImageIndex = $scope.image.id


        $scope.activeImageSrc = $scope.image.original
        $scope.activeImageSrcTag = $scope.image[$scope.settingsForm.tag]
        // console.log($scope.settingsForm.tag)
    }


    $scope.getConfig = function () {

        return galleryControl.config;
    }

}]);