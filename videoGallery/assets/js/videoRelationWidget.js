if (typeof angular === 'undefined') {
    console.error('AngularJS is not loaded. Make sure you include it before this script.');
} else {
    var app;
    
    try {
        app = angular.module('videoApp');
    } catch (e) {
        app = angular.module('videoApp', []);
    }
    
    app.controller('VideoRelationController', function($scope) {
        // Контроллер для управления видео-виджетом
    });
    
    app.directive('videoRelationWidget', function($http) {
        return {
            restrict: 'E',
            scope: {
                entityType: '@',
                entityId: '@'
            },
            template: `
<table class="video-table">
    <thead>
        <tr>
            <th>Выбрать</th>
            <th>Превью</th>
            <th>Название</th>
            <th>Действие</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="video in videos" 
            ng-class="{'selected': isVideoRelated(video.id)}" 
            ng-click="toggleRelation(video.id)" 
            style="cursor: pointer;">
            <td>
                <input type="checkbox" ng-checked="isVideoRelated(video.id)" ng-click="$event.stopPropagation()"/>
            </td>
            <td>
                <img ng-src="{{ video.thumbnail }}" alt="Превью" width="80" height="45" />
            </td>
            <td>{{ video.title }}</td>
            <td>
                <button ng-if="isVideoRelated(video.id)" ng-click="removeRelation(video.id, $event)">Удалить</button>
            </td>
        </tr>
    </tbody>
</table>

            `,
            link: function(scope) {
                scope.videos = [];
                scope.relatedVideos = [];
    
                // Загружаем список видео из галереи
                $http.get('/videoGallery/api').then(function(response) {
                    scope.videos = response.data;
                });
    
                // Загружаем уже связанные видео
                function loadRelations() {
                    $http.get('/api/video-relations?entity_type=' + scope.entityType + '&entity_id=' + scope.entityId)
                        .then(function(response) {
                            scope.relatedVideos = response.data.map(video => video.video_id);
                        });
                }
                loadRelations();
    
                // Проверяет, привязано ли видео
                scope.isVideoRelated = function(videoId) {
                    return scope.relatedVideos.includes(videoId);
                };
    
                // Добавление/удаление связи кликом по строке
                scope.toggleRelation = function(videoId) {
                    if (scope.isVideoRelated(videoId)) {
                        scope.removeRelation(videoId);
                    } else {
                        scope.addRelation(videoId);
                    }
                };
    
                // Добавление связи
                scope.addRelation = function(videoId) {
                    $http.post('/api/video-relations/create', {
                        video_id: videoId,
                        entity_type: scope.entityType,
                        entity_id: scope.entityId
                    }).then(function() {
                        loadRelations();
                    });
                };
    
    
                scope.removeRelation = function(videoId, event) {
                    if (event) event.stopPropagation();
                
                    $http.delete('/api/video-relations/' + videoId + '/delete', {
                        params: {
                            entity_type: scope.entityType,
                            entity_id: scope.entityId
                        }
                    }).then(function(response) {
                        if (response.data.success) {
                            scope.relatedVideos = scope.relatedVideos.filter(id => id !== videoId);
                        } else {
                            console.error('Ошибка при удалении:', response);
                        }
                    }).catch(function(error) {
                        console.error('Ошибка при удалении связи', error);
                    });
                };
                
                
            }
        };
    });
}
