angular.module('videoApp', ['commonUi'])
.directive('videoGallery', function() {
    return {
        restrict: 'E',
        scope: {
            modelName: '@?',
            modelId: '@?'
        },
        templateUrl: '/protected/modules/videoGallery/assets/js/templates/video-gallery.html',
        controller: function($scope, $http, $sce) {
            $scope.videos = [];
            $scope.entityLinks = [];
            $scope.addFormVisible = false;
            $scope.editFormVisible = false;
            $scope.confirmDeleteVisible = false;
            $scope.newVideo = {};
            $scope.currentVideo = {};
            $scope.videoToDelete = null;

            $scope.loadVideos = function() {
                $http.get('/videoGallery/api/index').then(function(response) {
                    $scope.videos = response.data
                        .map(video => {
                            video.isYouTube = $scope.isYouTubeUrl(video.url);
                            if (video.isYouTube) {
                                video.thumbnailUrl = $scope.getYouTubeThumbnailUrl(video.url);
                            }
                            return video;
                        })
                        .sort((a, b) => b.create_time - a.create_time); // Сортировка по create_time

                    if ($scope.modelName && $scope.modelId) {
                        $scope.loadEntityLinks();
                    }
                });
            };

            $scope.loadEntityLinks = function() {
                $http.get('/videoGallery/api/entityLinks', {
                    params: { modelName: $scope.modelName, modelId: $scope.modelId }
                }).then(function(response) {
                    $scope.entityLinks = response.data;
                    $scope.videos.forEach(video => {
                        video.isLinked = $scope.entityLinks.some(link => link.video_id === video.id);
                    });
                });
            };

            $scope.toggleLink = function(video) {
                if (video.isLinked) {
                    $scope.removeLink(video);
                } else {
                    $scope.addLink(video);
                }
            };

            $scope.addLink = function(video) {
                const newLink = {
                    video_id: video.id,
                    entity_model: $scope.modelName,
                    entity_id: $scope.modelId
                };
                $http.post('/videoGallery/api/addEntityLink', { VideoEntityLink: newLink }).then(function(response) {
                    if (response.data.status === 'success') {
                        video.isLinked = true;
                    }
                });
            };

            $scope.removeLink = function(video) {
                const linkToRemove = $scope.entityLinks.find(link => link.video_id === video.id);
                if (linkToRemove) {
                    $http.post('/videoGallery/api/deleteEntityLink?id=' + linkToRemove.id).then(function(response) {
                        if (response.data.status === 'success') {
                            video.isLinked = false;
                        }
                    });
                }
            };

            $scope.showAddForm = function() {
                $scope.addFormVisible = true;
                $scope.editFormVisible = false;
            };

            $scope.addVideo = function() {
                $http.post('/videoGallery/api/create', { VideoGalleryVideo: $scope.newVideo }).then(function(response) {
                    if (response.data.status === 'success') {
                        $scope.loadVideos();
                        $scope.newVideo = {};
                        $scope.addFormVisible = false;
                    }
                });
            };

            $scope.showEditForm = function(video) {
                $scope.currentVideo = angular.copy(video);
                $scope.editFormVisible = true;
                $scope.addFormVisible = false;
            };

            $scope.updateVideo = function() {
                $http.post('/videoGallery/api/update?id=' + $scope.currentVideo.id, { VideoGalleryVideo: $scope.currentVideo }).then(function(response) {
                    if (response.data.status === 'success') {
                        $scope.loadVideos();
                        $scope.editFormVisible = false;
                    }
                });
            };

            $scope.confirmDelete = function(video) {
                $scope.videoToDelete = video;
                $scope.confirmDeleteVisible = true;
            };

            $scope.deleteVideo = function() {
                $http.post('/videoGallery/api/delete?id=' + $scope.videoToDelete.id).then(function(response) {
                    if (response.data.status === 'success') {
                        $scope.loadVideos();
                        $scope.confirmDeleteVisible = false;
                    }
                });
            };

            // Utility function to check if URL is a YouTube link
            $scope.isYouTubeUrl = function(url) {
                return url && url.match(/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            };

            // Utility function to get the YouTube thumbnail URL
            $scope.getYouTubeThumbnailUrl = function(url) {
                var videoId = url.match(/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/)[1];
                return 'https://img.youtube.com/vi/' + videoId + '/hqdefault.jpg';
            };

            // Watch for changes in modelName and modelId
            $scope.$watchGroup(['modelName', 'modelId'], function(newValues, oldValues) {
                if (newValues !== oldValues) {
                    $scope.loadVideos();
                }
            });

            // Load initial data
            $scope.loadVideos();
        }
    };
});
