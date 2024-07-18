<?php
/* @var $this VisitStatisticsController */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/bower_components/jquery/dist/jquery.min.js');
$cs->registerScriptFile('/bower_components/angular/angular.min.js');
$cs->registerCssFile('https://stackpath.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css');
$cs->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');

$this->breadcrumbs = array(
    'Статистика посещений',
);
$menuPath = Yii::getPathOfAlias('stat.views');
$this->menu = require_once($menuPath.DIRECTORY_SEPARATOR.'commonMenu.php');
?>

<h1>Статистика посещений</h1>

<script src="<?php echo Yii::app()->baseUrl; ?>/protected/modules/stat/assets/VisitStatisticsController.js"></script>

<div class="container-fluid" ng-app="visitStatisticsApp" ng-controller="VisitStatisticsController">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <label for="datePicker">Выберите дату:</label>
                <input type="date" id="datePicker" ng-model="selectedDate" ng-change="loadDailyStatistics()" class="form-control w-auto d-inline-block">
            </div>

            <div class="mb-4">
                <h2>Сводка</h2>
                <div class="alert alert-info">
                    <p>Всего уникальных IP: {{ totalUniqueIPs }}</p>
                    <p>Всего визитов: {{ totalVisits }}</p>
                </div>
            </div>

            <h2>Ежедневная статистика</h2>
            <div class="table-responsive w-100">
                <table class="table table-striped table-bordered w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th>Домен</th>
                            <th>Уникальные IP</th>
                            <th>Количество визитов</th>
                            <th>Яндекс.Метрика</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="stat in dailyStatistics">
                            <td>{{ stat.domain }}</td>
                            <td>{{ stat.unique_ips }}</td>
                            <td>{{ stat.visits_count }}</td>
                            <td>
                                <span ng-if="stat.hasMetrika">Есть</span>
                                <span ng-if="!stat.hasMetrika" class="text-danger">Нет</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <h2>Последние визиты</h2>
            <button class="btn btn-secondary mb-4" ng-click="toggleFilters()">Показать/скрыть фильтры</button>
            <div ng-show="showFilters">
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="showFilteredVisits" ng-model="showFilteredVisits" ng-change="loadRecentVisits()">
                    <label class="form-check-label" for="showFilteredVisits">Показать отфильтрованные визиты</label>
                </div>
                <div class="mb-4">
                    <label for="recentVisitsCount">Количество последних визитов:</label>
                    <input type="number" id="recentVisitsCount" ng-model="recentVisitsCount" ng-change="loadRecentVisits()" class="form-control w-auto d-inline-block" min="1">
                </div>
                <div class="mb-4">
                    <label for="siteFilter">Фильтр по сайту:</label>
                    <select id="siteFilter" ng-model="selectedSite" ng-options="site for site in sites" ng-change="loadRecentVisits()" class="form-control w-auto d-inline-block">
                        <option value="">Все сайты</option>
                    </select>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="stopUpdate" ng-model="stopUpdate" ng-change="toggleUpdate()">
                    <label class="form-check-label" for="stopUpdate">Остановить обновление</label>
                </div>
            </div>
            <div class="table-responsive w-100 mb-4">
                <table class="table table-striped table-bordered w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>IP Адрес</th>
                            <th>User Agent</th>
                            <th>Время визита</th>
                            <th>Посещенная страница</th>
                            <th>Домен</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="visit in recentVisits | limitTo:recentVisitsCount | filter:filterVisits | filter:siteFilter">
                            <td>{{ visit.id }}</td>
                            <td>{{ visit.ip_address }}</td>
                            <td>
                                {{ visit.user_agent }}
                                <button class="btn btn-sm btn-danger ml-2" ng-click="filterUserAgent(visit.user_agent)" ng-disabled="isUserAgentFiltered(visit.user_agent)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                            <td>{{ visit.visit_time }}</td>
                            <td>{{ visit.page_visited }}</td>
                            <td>{{ visit.domain }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>