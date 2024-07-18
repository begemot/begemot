// Создаем модуль AngularJS
var app = angular.module('visitStatisticsApp', []);

// Создаем контроллер
app.controller('VisitStatisticsController', function($scope, $http, $interval) {
    $scope.dailyStatistics = [];
    $scope.recentVisits = [];
    $scope.sites = [];
    $scope.filteredUserAgents = [];
    $scope.selectedDate = '';
    $scope.totalUniqueIPs = 0;
    $scope.totalVisits = 0;
    $scope.showFilteredVisits = false;
    $scope.recentVisitsCount = 100;
    $scope.selectedSite = '';
    $scope.showFilters = false;
    $scope.stopUpdate = false;

    let updateRecentVisits;
    let updateDailyStatistics;

    $scope.loadDailyStatistics = function() {
        var url = '/stat/visitStatistics/getDailyStatistics';
        if ($scope.selectedDate) {
            url += '?date=' + encodeURIComponent($scope.selectedDate);
        }

        $http.get(url)
            .then(function(response) {
                $scope.dailyStatistics = response.data;
                $scope.calculateSummary();
                $scope.loadSites();
                $scope.checkMetrika();
            }, function(error) {
                console.error('Ошибка загрузки ежедневной статистики:', error);
            });
    };

    $scope.checkMetrika = function() {
        angular.forEach($scope.dailyStatistics, function(stat) {
            $http.get('/stat/visitStatistics/checkMetrika?domain=' + encodeURIComponent(stat.domain))
                .then(function(response) {
                    stat.hasMetrika = response.data.hasMetrika;
                }, function(error) {
                    console.error('Ошибка проверки метрики:', error);
                });
        });
    };

    $scope.loadRecentVisits = function() {
        var url = '/stat/visitStatistics/getRecentVisits';
        var params = [];

        if (!$scope.showFilteredVisits) {
            params.push('excludeFiltered=true');
        }
        params.push('limit=' + encodeURIComponent($scope.recentVisitsCount));
        if ($scope.selectedSite) {
            params.push('site=' + encodeURIComponent($scope.selectedSite));
        }

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        $http.get(url)
            .then(function(response) {
                $scope.recentVisits = response.data;
            }, function(error) {
                console.error('Ошибка загрузки последних визитов:', error);
            });
    };

    $scope.loadFilteredUserAgents = function() {
        $http.get('/stat/visitStatistics/getFilteredUserAgents')
            .then(function(response) {
                $scope.filteredUserAgents = response.data;
            }, function(error) {
                console.error('Ошибка загрузки отфильтрованных user agents:', error);
            });
    };

    $scope.filterUserAgent = function(userAgent) {
        $http.post('/stat/visitStatistics/filterUserAgent', { userAgent: userAgent })
            .then(function(response) {
                $scope.loadDailyStatistics();
                $scope.loadFilteredUserAgents();
            }, function(error) {
                console.error('Ошибка фильтрации user agent:', error);
            });
    };

    $scope.isUserAgentFiltered = function(userAgent) {
        return $scope.filteredUserAgents.includes(userAgent);
    };

    $scope.calculateSummary = function() {
        $scope.totalUniqueIPs = 0;
        $scope.totalVisits = 0;
        $scope.dailyStatistics.forEach(function(stat) {
            $scope.totalUniqueIPs += stat.unique_ips;
            $scope.totalVisits += stat.visits_count;
        });
    };

    $scope.loadSites = function() {
        $scope.sites = $scope.dailyStatistics.map(function(stat) {
            return stat.domain;
        });
    };

    $scope.filterVisits = function(visit) {
        if ($scope.showFilteredVisits) {
            return true;
        }
        return !$scope.isUserAgentFiltered(visit.user_agent);
    };

    $scope.siteFilter = function(visit) {
        if ($scope.selectedSite) {
            return visit.domain === $scope.selectedSite;
        }
        return true;
    };

    $scope.toggleFilters = function() {
        $scope.showFilters = !$scope.showFilters;
    };

    $scope.toggleUpdate = function() {
        if ($scope.stopUpdate) {
            if (updateRecentVisits) {
                $interval.cancel(updateRecentVisits);
            }
            if (updateDailyStatistics) {
                $interval.cancel(updateDailyStatistics);
            }
        } else {
            updateRecentVisits = $interval($scope.loadRecentVisits, 1000);
            updateDailyStatistics = $interval($scope.loadDailyStatistics, 5000);
        }
    };

    $scope.loadDailyStatistics();
    $scope.loadRecentVisits();
    $scope.loadFilteredUserAgents();

    updateRecentVisits = $interval($scope.loadRecentVisits, 1000);
    updateDailyStatistics = $interval($scope.loadDailyStatistics, 5000);
});
