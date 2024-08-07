var app = angular.module('myApp', ['uiCatalog', 'commonUi']);

app.controller('myCtrl', function ($scope, $http) {
    $scope.selectedItems = [];
    $scope.celectedItem = undefined
    // Функция, которая будет вызываться при изменении selectedItems
    $scope.onSelectItem = function (item) {
        console.log('Первый элемент selectedItems:', item);
        $scope.selectedItem = item
        $scope.id = item.id
        $scope.getImagesData()
        // Здесь можно добавить дополнительную логику для обработки элемента item
    };

    // Функция, которая будет вызываться, когда selectedItems становится пустым
    $scope.deselectItem = function () {
        console.log('selectedItems пустой');
        // Здесь можно добавить дополнительную логику, которая должна выполняться, когда selectedItems становится пустым
    };

    // Используем $watchCollection для отслеживания изменений в массиве selectedItems
    $scope.$watchCollection('selectedItems', function (newVal, oldVal) {
        if (newVal !== oldVal) {
            if (newVal.length > 0) {
                $scope.onSelectItem(newVal[0]);
            } else {
                $scope.deselectItem();
            }
        }
    });

    $scope.imagesData = []

    $scope.galleryId = 'catalogItem'

    $scope.getImagesData = function () {

        var url = '/pictureBox/api/getData?galleryId=' + $scope.galleryId + '&id=' + $scope.id;

        $http.get(url)
            .then(function (response) {
                $scope.data = response.data;
            })
            .catch(function (error) {
                console.error('Error fetching data:', error);
            });
    }

    $scope.updateTitle = function (id, title) {
        console.log(id + ' tialttle')
        params = {
            'gallery': $scope.galleryId,
            'id': $scope.id,
            'imageId': id,
            'title': title
        }
        $http.get('/pictureBox/api/updateTitle', { params: params }).then()
    }
    //$gallery, $id, $imageId, $alt
    $scope.updateAlt = function (id, alt) {
        console.log(id + ' tialttle')
        params = {
            'gallery': $scope.galleryId,
            'id': $scope.id,
            'imageId': id,
            'alt': alt
        }
        $http.get('/pictureBox/api/updateAlt', { params: params }).then()
    }

    $scope.saveData = function() {
        var galleryId = $scope.galleryId; // Укажите ваш galleryId
        var id = $scope.id; // Укажите ваш id
        var subGallery = 'default'; // Укажите ваш subGallery, если необходимо

        $http.post('/pictureBox/api/obectSave?galleryId=' + galleryId + '&id=' + id + '&subGallery=' + subGallery, $scope.data)
            .then(function(response) {
                console.log('Данные успешно отправлены', response.data);
            }, function(error) {
                console.error('Произошла ошибка при отправке данных', error);
            });
    };

   
    $scope.applyJsonTitles = function () {
        try {
          //  console.log($scope.jsonTitles)
          
            var titles = JSON.parse($scope.jsonTitles.data);
            
            for (var key in titles) {
                if (titles.hasOwnProperty(key)) {
                    
                    var title = titles[key].title;  
                    var alt = titles[key].alt;  
                    console.log(title)
                    console.log(alt)
                    if ($scope.data.images[key]) {
                        console.log(title)
                        console.log(alt)
                        $scope.data.images[key].title = title;
                        $scope.data.images[key].alt = alt;
                        
                        // $scope.updateTitle(key, title);
                    }
                }
            }
            console.log($scope.data.images)
            alert('Titles applied successfully!');
        } catch (e) {
            alert('Invalid JSON format!');
        }
      
    };
    $scope.windowVisible = false
    $scope.toggleJsonWindow = ()=>{
        var altTitels = {};
        for (var key in $scope.data.images) {
            if ($scope.data.images.hasOwnProperty(key)) {
                altTitels[key] = {
                    title:$scope.data.images[key].title,
                    alt:$scope.data.images[key].alt
                };
            }
        }
        $scope.jsonTitles.data = JSON.stringify(altTitels, null, 2);
        console.log($scope.jsonTitles.data )
        $scope.windowVisible = !$scope.windowVisible 
    }

    $scope.jsonTitles = {data:''}
});
