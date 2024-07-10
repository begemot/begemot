angular.module('pictureBox').controller('massResize', ['$scope', '$http', 'galleryControl', 'values', function ($scope, $http, galleryControl, values) {

    $scope.galId = values.galId
    $scope.id = values.id
    $scope.activeGallery = values.activeGallery
    $scope.activeImageIndex = 0

    $scope.settingsForm = {
        galleryId: 'catalogItem',
        tag: 'main',
        width: 234,
        height: 134,
        ids: ''
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
        console.log($scope.settingsForm)
        if ($scope.settingsForm.ids==''){
            $scope.getAllIds()
        }else {
            $scope.parseIds()
            $scope.nextImage()
            $scope.getData()
        }

    }
    $scope.getAllIds = function(){
        
        $http.get('/pictureBox/api/GetAllIds/gallery/'+$scope.settingsForm.galleryId).then(function (responce) {
            $scope.IDsArray = responce.data
            $scope.nextImage()
            $scope.getData()
            // $scope.interfaceUpdate(responce.data);
        });
    }

    $scope.getData = () => {
        // console.log($scope.IDsArray);
        $http.get('/pictureBox/api/GetData?XDEBUG_SESSION_START=1', {
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