var app = angular.module('cacheApp', []);

app.controller('cacheCtrl', function ($scope, $http) {

    $scope.selectedOption = null
    $scope.sortReverse = true

    $scope.sortBy = null



    $scope.requestObject = {
        page: 1,
        perPage: 10,
        search: {},
        order:{}
    }
    $scope.currentPage = 1
    $scope.search = {}
// Fetch data from server and store in $scope.caches
    $scope.loadData = () => {

        $scope.requestObject.search = $scope.search;
        $http.get('/cache/cache/GetCacheDataPage.html', {params: $scope.requestObject}).then(function (response) {
            $scope.caches = response.data[0];
            $scope.totalPages = response.data[1].totalPages;
            $scope.currentPage = response.data[1].currentPage + 1;
            $scope.pageSize = response.data[1].pageSize;
            $scope.itemCount = response.data[1].itemCount;
        });

        $http.get('/cache/cache/getUniqueCacheGroups.html').then(function (response) {
            $scope.options = response.data
        });

    }
    $scope.sortColumn = (column) => {
        console.log(column)
        if($scope.sortBy == column){
            $scope.sortReverse = !$scope.sortReverse
            $scope.requestObject.order = {}
            $scope.requestObject.order[$scope.sortBy] = $scope.sortReverse
            $scope.loadData()
            // array(
//           // 'id' => true,
//            'cache_group' => true,
//            'cache_key' => false,
//           // 'value' => false,
//        );
        }else{
            $scope.sortReverse = true
            $scope.sortBy = column
            $scope.requestObject.order = {}
            $scope.requestObject.order[$scope.sortBy] = $scope.sortReverse
            $scope.loadData()
        }
    }
    $scope.loadData();
    $scope.setCurrentPage = (page) => {
        console.log(page)
        //$scope.currentPage = page
        $scope.requestObject.page = page
        $scope.loadData();
    }
});
app.directive('ajaxButton', ['$http', function ($http) {
    return {
        restrict: 'E',
        scope: {
            title: '@',
            url: '@',
            method: '@',
            data: '=',
            success: '&',
            error: '&'
        },
        replace: true,
        template: '<button type="button" class="btn btn-primary" ng-class="{\'btn-success\': isSuccess, \'btn-danger\': isError}" ng-disabled="isSending">' +
            '<span ng-if="isSending"><i class="fa fa-spinner fa-spin"></i> Sending...</span>' +
            '<span ng-if="isSuccess"><i class="fa fa-check"></i> Success</span>' +
            '<span ng-if="isError"><i class="fa fa-times"></i> Error</span>' +
            '<span ng-if="!isSending && !isSuccess && !isError">{{title}}</span>' +
            '</button>',
        link: function (scope, element, attrs) {
            scope.isSending = false;
            scope.isSuccess = false;
            scope.isError = false;

            element.bind('click', function () {
                scope.isSending = true;

                var request = {
                    url: scope.url,
                    method: scope.method,
                    data: scope.data
                };

                $http(request).then(function (response) {
                    scope.isSending = false;
                    scope.isSuccess = true;
                    scope.success({response: response});
                }, function (response) {
                    scope.isSending = false;
                    scope.isError = true;
                    scope.error({response: response});
                });
            });
        }
    };
}]);

app.directive('smartPagination', function () {
    return {
        restrict: 'E',
        scope: {
            totalPages: '=',
            currentPage: '=',
            maxPages: '=',
            setCurrentPage: '='
        },
        templateUrl: '/protected/modules/cache/views/cache/smartPagination.html',
        link: function (scope) {
            scope.pages = [];

            function updatePages() {

                // Determine range of pages to show
                //  console.log('!' + scope.currentPage)
                var start = scope.currentPage - 3;
                var end = scope.currentPage + 3;

                // Add ellipses if there are gaps between page ranges
                // if (start > 1) {
                //     scope.pages.push('...');
                // }
                if (scope.currentPage - 3 > 1) {
                    scope.pages.push(1);
                    scope.pages.push('...');
                }

                for (var i = start; i <= end; i++) {
                    console.log('!' + scope.totalPages)
                    if (i < 1) continue
                    if (i > scope.totalPages) continue

                    scope.pages.push(i);
                }
                if (scope.currentPage + 3 < scope.totalPages) {
                    scope.pages.push('... ');
                    scope.pages.push(scope.totalPages);
                }
                // if (end < scope.totalPages) {
                //     scope.pages.push('...');
                // }

                // // Add first and last pages if they're not already in the range
                // if (scope.pages.indexOf(1) < 0) {
                //     scope.pages.unshift(1);
                // }
                // if (scope.pages.indexOf(scope.totalPages) < 0) {
                //     scope.pages.push(scope.totalPages);
                // }

                console.log(scope.pages)
            }

            // Watch for changes to currentPage and update the pages accordingly
            scope.$watch('currentPage', function () {
                scope.pages = [];
                updatePages();
            });

            // Watch for changes to totalPages and update the pages accordingly
            scope.$watch('totalPages', function () {
                scope.pages = [];
                updatePages();
            });
        }
    };
});
app.directive('dropdown', function ($timeout, $document) {
    return {
        restrict: 'E',
        scope: {
            options: '=',
            selectedOption: '=',
            label: '@'
        },
        templateUrl: '/protected/modules/cache/views/cache/dropdown.html',
        link: function (scope, element, attrs) {
            // Dropdown toggle
            // element.find('.dropdown-toggle').on('click', function () {
            //     element.find('.dropdown-menu').toggleClass('show');
            // });

            scope.showDropdown = (event) => {
                element.find('.dropdown-menu')[0].classList.toggle('show');

            }


            scope.selectOption = (option) => {

                scope.selectedOption = option;
                console.log('нажали')
                console.log(scope.selectedOption)
            }

            var documentClickHandler = function (event) {
                if (!element[0].contains(event.target)) {
                    element.find('.dropdown-menu')[0].classList.remove('show');
                }
            };

            $document.on('click', documentClickHandler);

            // Clean up event listener when scope is destroyed
            scope.$on('$destroy', function () {
                $document.off('click', documentClickHandler);
            });

        }
    };
});
