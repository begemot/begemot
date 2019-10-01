angular.module('performApp').directive('myCustomer', function() {
    return {
        restrict: 'E',
        templateUrl: '/protected/modules/contentTask/views/taskPerform/jstpl/directives/subtaskStatus.html',
        link:function(scope, element, attr) {
            element.css({
                position: 'relative',
                top:'10px',
                backgroundColor: 'lightgrey',
                cursor: 'pointer',
            });
        }
    };
});

