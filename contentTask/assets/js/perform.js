var app = angular.module('performApp', ['ngResource', 'ngSanitize', 'bw.paging']);



app.controller('edit', ['$scope', '$http', '$location', '$sce', function ($scope, $http, $location, $sce) {

    //Текущая итерация
    $scope.currentIteration = -1;

    $scope.isAdmin = false;
    //Проверяем пользователя на авторизованность как суперпользователя
    $http.get('/contentTask/taskPerform/isAdmin/').then(function (response) {
        $scope.isAdmin = response.data == "admin"
        console.log("Стату админа:"+$scope.isAdmin)
    })

    $scope.editors = {};
    $scope.accessCode = accessCode

    $scope.subtaskId = subtaskId

    $scope.editIsStopped = true



    //То что находится в $scope.visibleData отбражается в формах
    $scope.visibleData = [];
    // Базовые данные, не перезаписываются
    $scope.baseData = [];
    // Данные с которыми работают в текущий момент
    $scope.currentData = [];
    //активная вкладка одного из текстовых полей
    $scope.dataActiveTab = "";

    $scope.loadData = function () {
        $http.get('/contentTask/taskPerform/ajaxGetDataAndFields/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId).then(function (response) {
            $scope.baseData = response.data.base
            $scope.currentData = response.data.current

            $scope.dataActiveTab = $scope.baseData[0].name;


            $scope.subtaskStatus = response.data.status
            $scope.currentIteration = response.data.iteration
            $scope.updateUI()

            console.log('Загруженная итерация:'+$scope.currentIteration)
            console.log('Статус подзадания:'+$scope.subtaskStatus)
        }).then(function () {

            for (var i = 0; i < $scope.currentData.length; i++) {
                $scope.editors[$scope.currentData[i].name] = "simple";
            }
            console.log($scope.subtaskStatus=='review');
        });
    }

    // Большие куски разметки связаны с этим массивом видимости
    $scope.panels = {
        base: true,
        current: false,
        review: false,
        images:false
    };
    //Видимость элементов, список элементов, которые можно скрыть или показать
    $scope.visible = {
        btnSave: true,

        panelBtnReview: ($scope.currentIteration>0) ||
            ($scope.isAdmin == true && ($scope.subtaskStatus=='review')),

        btnSendForReview: true,
        pageBase: true,
        pageCurrent: true,
        pageReview: false,
        infoReview: false,

        dataDiv: true,
        reviewDiv: false,
        imagesDiv: false
    }


    $scope.saveText = "Сохранить";
    $scope.reviewText = "Отправить на проверку";




    /**
     * В зависимости от того, какую нажали вкладку включаем
     * и выключаем необходимые видимости элементов. Так же запускаем функцию,
     * которая выполняет инициализацию данных для вида вкладки.
     */
    $scope.updateUI = function () {
        status = $scope.subtaskStatus
        if (status == 'edit' || status == 'new') {
            $scope.activateBase()
            console.log($scope.visible.btnSave)



        }

        if (status == 'review') {
            $scope.visible.btnSave = false;
            $scope.visible.pageBase = false;
            $scope.visible.pageCurrent = false
            $scope.visible.btnSendForReview = false

            $scope.visible.infoReview = true
            $scope.visible.pageReview = true
            //Разрешаем или нет редактировать правки
            $scope.reviewEdit = true

            $scope.activateCurrent()
            $scope.editIsStopped = true

        }

        if (status == 'mistake') {

            $scope.visible.btnSave = true;
            $scope.visible.pageBase = true;
            $scope.visible.pageCurrent = true
            $scope.visible.btnSendForReview = true
            $scope.visible.infoReview = false
            $scope.visible.pageReview = true
            $scope.panel('base')
        }

        if (status == 'done') {
            $scope.visible.btnSave = false
            $scope.visible.btnSendForReview = false
            $scope.visible.pageCurrent = false
            $scope.visible.pageBase = false
        }

    }

    $scope.editorView = function (itemName, editorType) {
        $scope.editors[itemName] = editorType;
    }

    $scope.loadData()

    $scope.activateBase = function () {
        $scope.editIsStopped = true
        $scope.visibleData = $scope.baseData
    }

    $scope.activateCurrent = function () {
        $scope.editIsStopped = false
        $scope.visibleData = $scope.currentData
    }

    $scope.activateReview = function (iteration) {
        $scope.visible.dataDiv = false
        $scope.visible.reviewDiv = true
        // console.log($scope.visibleData)
        $scope.reviewTab = $scope.visibleData[0].name

        $http.get('/contentTask/taskPerform/ajaxGetReviewData/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId + '/iteration/' + iteration).then(function (response) {
            console.log(response.data)
            if (response.data.mistakeData != undefined) {

                $scope.mistakesData = response.data.mistakeData
            }
            if (response.data.baseText != undefined) {
                $scope.baseText = response.data.baseText
                $scope.baseText.edit = false
            }

            if (response.data.mistakeCount != undefined) {
                $scope.mistakeCount = response.data.mistakeCount
            }
            if (response.data.visibleData != undefined) {
                $scope.reviewData = response.data.visibleData
            } else {
                $scope.reviewData = Object.assign({}, $scope.visibleData)
            }

            for (var i = 0; i < $scope.visibleData.length; i++) {

                // $scope.visibleData[i].data = response.data.visibleData[i].data
                $scope.trustedHTML[i] = $sce.trustAsHtml($scope.reviewData[i].data)
            }

            $()
            console.log($scope.trustedHTML)

        }).then(function () {
            // $scope.loadData()
        });

    }

    $scope.save = function () {
        $scope.saveText = "Сохраняю..."
        $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
        return $http.post(
            '/contentTask/taskPerform/save/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId,
            {data: $scope.currentData}).then(function () {
            $scope.saveText = "Сохранить"
        })
    }

    $scope.sendToReview = function () {
        $scope.reviewText = "Отправляю..."

        $scope.save().then(function(){
            $http.get('/contentTask/taskPerform/sendToReview/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId).then(function (response) {
                $scope.reviewText = "Отправить на проверку"
            }).then(function () {
                $scope.loadData()
            });
        });

    }

    $scope.panel = function (panelName) {
        console.log("Выбираем панель");
        Object.keys($scope.panels).map(function (objectKey, index) {

            if (panelName == objectKey)
                $scope.panels[objectKey] = true
            else $scope.panels[objectKey] = false
        });

        if (panelName == "base") {
            $scope.activateBase()
            $scope.visible.dataDiv = true
            $scope.visible.reviewDiv = false

        }

        if (panelName == "current") {
            $scope.activateCurrent();
            $scope.visible.dataDiv = true
            $scope.visible.reviewDiv = false

        }

        if (panelName == "review"){
            if ( $scope.subtaskStatus == 'review'){

                $scope.activateReview($scope.currentIteration);
            } else if ($scope.subtaskStatus == 'mistake') {
                $scope.activateReview($scope.currentIteration-1);
            }
        } else {

        }

        if (panelName == "images"){
            $scope.visible.dataDiv = false
            $scope.visible.imagesDiv = true
            $scope.visible.reviewDiv = false
        } else {
            $scope.visible.imagesDiv = false
        }

    }


    // Режим редактуры и все что с ним связано
    $scope.reviewData = {}
    $scope.reviewEdit = false
    $scope.mistakeCount = 0
    $scope.mistakesData = []
    $scope.baseText = {
        edit: false,
        text: ""
    }
    $scope.trustedHTML = {};

    $scope.markMistake = function () {

        var selObj = window.getSelection();
        txt = window.getSelection().toString();
        if (txt.length > 0) {

            $scope.mistakeCount++
            console.log($scope.mistakesData)
            $scope.mistakesData.push({tab: $scope.reviewTab, text: "", edit: false, id: $scope.mistakeCount})
            console.log('Правка на вкладке: ' + $scope.reviewTab);
            console.log($scope.mistakesData)


            // alert(selObj);
            var selRange = selObj.getRangeAt(0);
            // selRange.deleteContents()
            var range = selRange
            var newNode = document.createElement("span");
            newNode.setAttribute("id", "miss" + $scope.mistakeCount);
            newNode.setAttribute("style", "background-color:#F1D82A");
            newNode.appendChild(range.extractContents());
            newNode.addEventListener('click', $scope.misstakeClick);
            range.insertNode(newNode);
            console.log($scope.visibleData)
            $scope.packHtmlData();
        } else {
            console.log("Пользовательское выделение отсутствует")
        }


        // range.surroundContents(newNode);
    }
    //Нажатие на правку в тексте
    $scope.misstakeClick = function () {
        alert(this)
    }

    $scope.mistakeHoverIn = function (event, index) {

        $("#miss" + ($scope.mistakesData[index].id)).css('color', 'white');
        $("#miss" + ($scope.mistakesData[index].id)).css('background-color', 'black');

        $("#reviewTab_" + $scope.mistakesData[index].tab + " a i").css('display', 'inline-block');

        // console.log(jQueryElement)
        // console.log("Навели" + index);
        // console.log($scope.mistakesData[index])
    }

    $scope.mistakeHoverOut = function (event, index) {
        $("#miss" + ($scope.mistakesData[index].id)).css('color', 'black');
        $("#miss" + ($scope.mistakesData[index].id)).css('background-color', '#F1D82A');
        // console.log("#reviewTab_" + $scope.mistakesData[index].tab);
        $("#reviewTab_" + $scope.mistakesData[index].tab + " a i").css('display', 'none');
        // console.log("увели" + index);
    }
    $scope.setActiveReviewTab = function (tab) {
        $scope.reviewTab = tab
    }

    $scope.btnTextReviewSave = "Сохранить"
    $scope.btnTextReviewReturn = "Вернуть на доработку"
    $scope.btnTextMarkAsDone= "Принять"

    $scope.saveReviewData = function () {
        $scope.btnTextReviewSave = "Сохраняю..."
        console.log('Отправляем на сохранение правки!');

        console.log($scope.baseText);
        console.log($scope.mistakesData);
        console.log($scope.subtaskId);

        $scope.baseText.edit=false


        for (var i = 0; i < $scope.mistakesData.length; i++) {
           $scope.mistakesData[i].edit=false
        }

        dataForSend = {
            baseText: $scope.baseText,
            mistakeData: $scope.mistakesData,
            visibleData: $scope.visibleData,
            mistakeCount: $scope.mistakeCount
        }


        $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
        return $http.post(
            '/contentTask/taskPerform/ajaxReviewDataSave/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId,
            dataForSend).then(function () {
            $scope.btnTextReviewSave = "Сохранить"
        })

    }

    $scope.removeReview = function (index) {
        $("#miss" + $scope.mistakesData[index].id).replaceWith(function () {
            return $(this).html();
        })
        $("#reviewTab_" + $scope.mistakesData[index].tab + " a i").css('display', 'none');
        $scope.mistakesData.splice(index, 1)
        console.log($scope.mistakesData)

    }

    $scope.packHtmlData = function () {

        for (var i = 0; i < $scope.visibleData.length; i++) {
            var name = $scope.visibleData[i].name
            $scope.visibleData[i].data = $('#display_' + name).html()
            console.log($('#display_' + name).html());
            // $scope.visibleData[i].data = $sce.trustAsHtml( $('#' + name).html())
        }

        // $('.tab-pane div').each(function () {
        //     console.log(this)
        // })
    }

    $scope.sendBackToWork = function(){
        $scope.saveReviewData().then(
            function (){
                $http.get('/contentTask/taskPerform/sendBackToWork/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId).then(function (response) {
                    $scope.btnTextReviewReturn = "Соединяемся"
                }).then(function () {
                    $scope.btnTextReviewReturn = "Вернуть на доработку"
                    //TODO:при возврате в работу для правок выключить режим редактирования
                    $scope.subtaskStatus = 'mistake'
                    $scope.reviewEdit = false;
                    $scope.updateUI()
                });
            }
        );
    }

    $scope.markAsDone = function(){
        $http.get('/contentTask/taskPerform/markAsDone/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId).then(function (response) {
            $scope.btnTextMarkAsDone = "Соединяемся..."
        }).then(function () {
            $scope.btnTextMarkAsDone = "Приянято"
            //TODO:при возврате в работу для правок выключить режим редактирования
            $scope.subtaskStatus = 'done'
            $scope.reviewEdit = false;
            $scope.updateUI()
        });
    }


}]);

app.controller('performController', ['$scope', '$http', '$location', function ($scope, $http, $location) {

    $scope.activePage = {
        new: 1,
        edit: 1,
        mistake: 1,
        review: 1,
        done: 1
    }
    $scope.recordsCount = {
        new: 20
    }


    $scope.accessCode = accessCode

    $scope.searchId = '';
    $scope.searchTitle = '';

    $scope.taskId = null;


    $scope.loadList = function(){
        $http.get('/contentTask/taskPerform/taskInfo/accessCode/' + $scope.accessCode).then(function (response) {

            $scope.taskData = response.data;
            $scope.taskId = $scope.taskData.id
            $scope.createBtnVisible = false;
            console.log($scope.taskData.actions);

            for (i=0;i<$scope.taskData.actions.length;i++){
                console.log($scope.taskData.actions[i])
                if ($scope.taskData.actions[i].id=='create'){
                    $scope.createBtnVisible = true;
                }}


        });
    }
    $scope.loadList();
    // $http.get('/contentTask/taskPerform/ajaxAddedList/accessCode/' + $scope.accessCode).then(function (response) {

    // $scope.taskData = response.data;
    // });

    $scope.panel = function (panelName) {
        console.log("Выбираем панель " + panelName);
        $scope.clearPanels();
        if (panelName == "base") $scope.isBasePanelVisible = true;
        if (panelName == "taskNew") {
            $scope.isTaskNewPanelVisible = true;
            $scope.makeSearch();
        }
        if (panelName == "taskEdit") {
            $scope.isTaskEditPanelVisible = true;

            $scope.makeSearch('edit');
        }
        if (panelName == "taskMistake") {
            $scope.isTaskMistakePanelVisible = true;

            $scope.makeSearch('mistake');
        }
        if (panelName == "taskReview") {
            $scope.isTaskReviewPanelVisible = true;

            $scope.makeSearch('review');
        }
        if (panelName == "taskDone") {
            $scope.isTaskDonePanelVisible = true;

            $scope.makeSearch('done');
        }
    }

    $scope.clearPanels = function () {
        $scope.isBasePanelVisible = false;
        $scope.isTaskNewPanelVisible = false;
        $scope.isTaskEditPanelVisible = false;
        $scope.isTaskMistakePanelVisible = false;
        $scope.isTaskReviewPanelVisible = false;
        $scope.isTaskDonePanelVisible = false;
    }

    $scope.panel("base");

    $scope.setPage = function (page, type) {
        $scope.activePage[type] = page
        $scope.makeSearch(type)
    }

    $scope.makeSearch = function (type = "new") {
        $scope.resultItems = [];
        $http.get('/contentTask/taskPerform/ajaxAddedList/accessCode/' + $scope.accessCode + '?page=' + $scope.activePage[type] + '&id=' + $scope.searchId + '&title=' + $scope.searchTitle + "&type=" + type).then(function (response) {
            $scope.resultItems[type] = response.data.data;
            $scope.recordsCount[type] = response.data.count;

        });
    }

    $scope.getStatusCounts = function () {
        $http.get('/contentTask/taskPerform/ajaxStatusList/accessCode/' + $scope.accessCode).then(function (response) {
            $scope.recordsCount = response.data
        });
    }
    $scope.getStatusCounts();

    $scope.createElement = function(){
        console.log("создаем позицию")
        $http.get('/contentTask/taskPerform/ajaxCreateNew/accessCode/' + $scope.accessCode).then(function (response) {
            console.log(response.data)
        }).then(function(){
            console.log("Создали, обновляем список. ");
            $scope.loadList();
            $scope.getStatusCounts();
        });
    }

    $scope.pushToSite = function(subtaskId,taskId){

        $http.get('/contentTask/taskPerform/ajaxPushToSite/accessCode/' + $scope.accessCode+"/taskId/"+taskId+"/subtaskId/"+subtaskId).then(function (response) {
            console.log(response.data)
        });
    }

    $scope.approve = function(subtaskId,lineId){

        $http.get('/contentTask/taskPerform/markAsDone/accessCode/' + $scope.accessCode + '/id/' + subtaskId).then(function (response) {

        }).then(function () {
           $scope.resultItems.review.splice(lineId, 1);
            $scope.recordsCount.review = $scope.recordsCount.review-1;
        });
    }
    // $scope.makeSearch();
    // $scope.makeSearch('edit');

}])

app.directive('ckEditor', function () {
    return {
        require: '?ngModel',
        link: function (scope, elm, attr, ngModel) {
            var ck = CKEDITOR.replace(elm[0]);
            if (!ngModel) return;
            ck.on('instanceReady', function () {
                ck.setData(ngModel.$viewValue);
            });

            function updateModel() {
                scope.$apply(function () {
                    ngModel.$setViewValue(ck.getData());
                });
            }

            ck.on('change', updateModel);
            ck.on('key', updateModel);
            ck.on('dataReady', updateModel);

            ngModel.$render = function (value) {
                ck.setData(ngModel.$viewValue);
            };
        }
    };
});
