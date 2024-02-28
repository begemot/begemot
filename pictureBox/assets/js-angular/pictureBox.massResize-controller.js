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
        ids: '4317 4293 4274 4315 4302 4304 4268 4273 4269 4298 4296 4301 4314 4280 4299 4311 4276 4307 4279 4265 4282 4309 4291 4297 4271 4264 4303 4290 4295 4308 4281 4275 4294 4318 4306 4286 4288 4287 4289 4278 4277 4284 4283 4270 4266 4305 4285 4272 4292 4310 4316 4300 4312 4313 4267 4250 4248'
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