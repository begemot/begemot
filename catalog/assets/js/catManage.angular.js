var app = angular.module ('catManage',[])

app.service('dnd', ['$http', function ($http) {

    var disabled = false;


    this.turnOn = function(){
        this.disabled = false
    }

    this.turnOff = function(){
        this.disabled = true
    }

    var dropCallBackCollection = new Array

    this.draggedObject = null;
    // this.dndTargetObject = null;

    this.startDrag = function(el){
        this.draggedObject = el
    }

    this.stopDrag = function(){
        this.draggedObject = null;
    }
    this.somethingWasDroppedOnMe = function(targetEl){
        if (!this.disabled) {
            this.runDropCallBackList(this.draggedObject, targetEl)
            this.stopDrag()
        }
    }

    this.registerDropCallBack = function(callback){
        dropCallBackCollection.push(callback)

    }
    this.runDropCallBackList = function(draggedElem,targetElem){
        _.forEach(dropCallBackCollection, function (callback) {
            callback(draggedElem,targetElem);
        });
    }

}]);


app.controller('ui', ['$scope', '$http', 'categoriesData','dnd', function ($scope, $http, categoriesData,dnd) {

   // console.log(categoriesData.categories);

    $scope.cats =_.orderBy(categoriesData.categories, [function(o) {
        return parseInt(o.order, 10);
    }]) ;

    $scope.startDrag = function(ev){
        console.log(ev)
    }

    $scope.wasDropped = function(draggedElem,targetElem){
        console.log('$scope.wasDropped');

        $scope.disableAll();
        // actionMoveCat
        $http({
            url: '/catalog/CatCategory/MoveCat',
            data: {
                dragged:{
                    id:draggedElem.attr('catId')
                },
                target:{
                    id:targetElem.attr('catId')
                },
                type:targetElem.attr('el-type')
            },
            method: 'POST'

        }).then(function (data) {
            console.log(data)
            $scope.cats =_.sortBy(data.data, [function(o) {
                return parseInt(o.order, 10);
            }]) ;
            $scope.enableAll();
        });
    }

    dnd.registerDropCallBack($scope.wasDropped)

    $scope.disableLine = function(order){
        _.forEach($scope.cats, function(value, key) {
            if (value.order==order){
                $scope.cats[key].disabled = true
            }
        });

    }
    $scope.enableLine = function(order){
        _.forEach($scope.cats, function(value, key) {
            if (value.order==order){
                $scope.cats[key].disabled = false
            }
        });

    }

    $scope.disableAll = function(){
        _.forEach($scope.cats, function(value, key) {
            $scope.cats[key].disabled = true
        });
    }
    $scope.enableAll = function(){
        _.forEach($scope.cats, function(value, key) {
            $scope.cats[key].disabled = false
        });
    }

}])


app.directive('aDraggable', ['$rootScope','dnd', function($rootScope,dnd) {
    return {
        restrict: 'A',
        link: function(scope, el, attrs, controller) {
            console.log("linking draggable element");

            angular.element(el).attr("draggable", "true");
            var id = attrs.id;


            el.bind("dragstart", function(e) {
                console.log('начали перетаскивать');

                dnd.startDrag(el)
                $rootScope.$emit("LVL-DRAG-START");
            });

            el.bind("dragend", function(e) {
                console.log('закончили перетаскивать');
                dnd.stopDrag(el)
                $rootScope.$emit("LVL-DRAG-END");
            });
        }
    }
}]);


app.directive('aDropTarget', ['$rootScope','dnd', function($rootScope,dnd) {
    return {
        restrict: 'A',
        scope: {
            onDrop: '&'
        },
        link: function(scope, el, attrs, controller) {

            var id=attrs.id
            el.bind("dragover", function(e) {
                console.log('надо мной объект');
                if (e.preventDefault) {
                    e.preventDefault(); // Necessary. Allows us to drop.
                }

                e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.
                return false;
            });

            el.bind("dragenter", function(e) {
                console.log('объект появился надо мной');
                // this / e.target is the current hover target.
                angular.element(e.target).addClass('dragOver');
            });

            el.bind("dragleave", function(e) {
                console.log('надо мной больше нет объекта');
                angular.element(e.target).removeClass('dragOver');  // this / e.target is previous target element.
            });

            el.bind("drop", function(e) {
                console.log('на меня сбросили объект');
                if (e.preventDefault) {
                    e.preventDefault(); // Necessary. Allows us to drop.
                }

                if (e.stopPropagation) {
                    e.stopPropagation(); // Necessary. Allows us to drop.
                }
                angular.element(e.target).removeClass('dragOver');
                dnd.somethingWasDroppedOnMe(el)
                console.log( 'сбросили объект с id '+id);
                // var data = e.dataTransfer.getData("text");
                // var dest = document.getElementById(id);
                // var src = document.getElementById(data);

               // scope.onDrop({dragEl: src, dropEl: dest});
            });

            $rootScope.$on("LVL-DRAG-START", function() {
               // var el = document.getElementById(id);
              //  angular.element(el).addClass("lvl-target");
            });

            $rootScope.$on("LVL-DRAG-END", function() {
              //  var el = document.getElementById(id);
             //   angular.element(el).removeClass("lvl-target");
              //  angular.element(el).removeClass("lvl-over");
            });
        }
    }
}]);