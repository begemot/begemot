angular.module('pictureBox').service('galleryControl', ['$http', 'values', function ($http, values) {

    /**
     * Сервис galleryControll позволяет загрузить все данные конкретной галлереи и управлять ей.
     *
     * Работает как синглтон на странице, что бы делать распределенный интерфейс по управлению изображениями.
     *
     * Все компоненты на странице находящиеся в разных контроллерах и даже приложениях могут обращаться к данным этого сервиса
     * и все данные у всех будут актуальны. Так же сервис берет на себя ответственность по рассылке сообщений всем, кто подписан,
     * о том, что данные изменились.
     */


    this.galId
    this.id
    this.activeGallery

    this.activeSubGallery = 'default';
    this.activeFilter = {};

    this.dataCollection = {};

    this.allImagesModal = {};
    this.config = {};
    this.currentPreviewSrc = '';
    this.titleModal = {};

    var activeFilterChangesCallbacks = [];

    this.activeFilterChangesAddCallback = function (callback) {
        activeFilterChangesCallbacks.push(callback);
    }
    this.setActiveFilter = function (filterNew) {
        console.log('this.setActiveFilter');
        this.activeFilter = filterNew;
        notifyObservers();
    }

    var notifyObservers = function () {
        console.log('notifyObservers');
        _.forEach(activeFilterChangesCallbacks, function (callback) {

            callback();
        });
    };


}]);