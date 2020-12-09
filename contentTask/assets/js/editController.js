app.controller('edit', ['$scope', '$http', '$location', '$sce', '$routeParams', '$route', 'subTaskService', 'viewSchema', '$q', function ($scope, $http, $location, $sce, $routeParams, $route, subTaskService, viewSchema, $q) {

    //Текущая итерация
    $scope.currentIteration = -1;

    $scope.isAdmin = false;


    $scope.editors = {};
    $scope.accessCode = accessCode
    subTaskService.accessCode = accessCode

    $scope.subtaskId = subtaskId
    subTaskService.subtaskId = subtaskId

    $scope.imagesShow = false

    console.log('Отправляем запрос на загрузку данных подзадания');
    $scope.loadData = function () {
        subTaskService.loadData().then(() => {
            console.log('Получили данные подзадания');
            $scope.baseData = subTaskService.baseData
            $scope.currentData = subTaskService.currentData
            $scope.dataActiveTab = $scope.baseData[0].name;


            $scope.subtaskStatus = subTaskService.subtaskStatus
            $scope.currentIteration = subTaskService.currentIteration

            for (var i = 0; i < $scope.currentData.length; i++) {
                $scope.editors[$scope.currentData[i].name] = "simple"
            }

            $scope.applyViewsParams()
            // console.log($scope.subtaskStatus=='review');
        })
    }

    console.log('Отправляем запрос на проверку админа');
    $http.get('/contentTask/taskPerform/isAdmin/').then(function (response) {
        console.log('Вернулся запрос на проверку админа');
        $scope.isAdmin = response.data == "admin"

    }).then(function () {

            $scope.loadData()
        }
    )


    //достаем базовую схему видимости
    $scope.applyViewsParams = () => {
        if ($scope.isAdmin) {
            userType = 'admin'
        } else {
            userType = 'editor'
        }
        console.log('Применяем стили',$scope.subtaskStatus,userType)
        console.log(viewSchema[userType])
        if (userType == 'editor') {
            console.log('Применяем схемы редактора, и сливаем:');
            console.log(viewSchema.base);
            console.log(viewSchema['editor'][$scope.subtaskStatus]);
            $scope.visible = _.merge($scope.visible,viewSchema.base, viewSchema['editor'][$scope.subtaskStatus])
        } else if (userType == 'admin') {
            console.log('Применяем схемы админа ');
            $scope.visible = _.merge($scope.visible,viewSchema.base, viewSchema['editor'][$scope.subtaskStatus], viewSchema['admin'][$scope.subtaskStatus])
        }

        if ($scope.tabName == "ReviewAdmin") {
            if ($scope.subtaskStatus == 'review') {

                $scope.activateReview($scope.currentIteration);
            } else if ($scope.subtaskStatus == 'mistake' || $scope.subtaskStatus == 'done') {
                $scope.activateReview($scope.currentIteration - 1);
            }
        } else {

        }

        if ($scope.tabName == "Images") {
            $scope.imagesShow = true
        } else {
            $scope.imagesShow = false
        }

        console.log('Массив примененных стилей')
        console.log($scope.visible)
    }

    //То что находится в $scope.visibleData отбражается в формах
    $scope.visibleData = [];
    // Базовые данные, не перезаписываются
    $scope.baseData = [];
    // Данные с которыми работают в текущий момент
    $scope.currentData = [];
    //активная вкладка одного из текстовых полей


    $scope.$routeParams = $routeParams
    $scope.tabName = 'Base';

    $scope.$on('$routeChangeStart', function ($event, next, current) {
        console.log('изменение ссылки')
        console.log(next, next.params.tabName)
        var tabName = next.params.tabName
        if (!tabName) {
            $scope.tabName = 'Base'
        } else {
            $scope.tabName = tabName
        }

        $scope.applyViewsParams()
    });


    $scope.saveText = "Сохранить";
    $scope.reviewText = "Отправить на проверку";


    $scope.editorView = function (itemName, editorType) {
        $scope.editors[itemName] = editorType;
    }


    $scope.activateReview = function (iteration) {


        $scope.reviewTab = subTaskService.currentData[0].name
        if (!$scope.reviewData)
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
                    console.log('загружаем данные для ошибок из бд')
                    $scope.reviewData = response.data.visibleData
                } else {
                    $scope.reviewData = _.clone( subTaskService.currentData)
                }

                keys = _.keys($scope.reviewData)

                for (var i = 0; i < keys.length; i++) {
                    rowName = keys[i]
                    $scope.trustedHTML[i] = $sce.trustAs($sce.HTML, $scope.reviewData[rowName].data)
                }
                console.log($scope.trustedHTML)
            })
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

        $scope.save().then(function () {
            $http.get('/contentTask/taskPerform/sendToReview/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId).then(function (response) {
                $scope.reviewText = "Отправить на проверку"
            }).then(function () {
                $scope.loadData()
            });
        });

    }


    // Режим редактуры и все что с ним связано
    $scope.reviewData = null

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
        // console.log(selObj)
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
    }

    $scope.mistakeHoverOut = function (event, index) {
        $("#miss" + ($scope.mistakesData[index].id)).css('color', 'black');
        $("#miss" + ($scope.mistakesData[index].id)).css('background-color', '#F1D82A');

        $("#reviewTab_" + $scope.mistakesData[index].tab + " a i").css('display', 'none');

    }
    $scope.setActiveReviewTab = function (tab) {
        $scope.reviewTab = tab
    }

    $scope.btnTextReviewSave = "Сохранить"
    $scope.btnTextReviewReturn = "Вернуть на доработку"
    $scope.btnTextMarkAsDone = "Принять"

    $scope.saveReviewData = function () {
        $scope.btnTextReviewSave = "Сохраняю..."
        console.log('Отправляем на сохранение правки!');

        console.log($scope.baseText);
        console.log($scope.mistakesData);
        console.log($scope.subtaskId);

        $scope.baseText.edit = false


        for (var i = 0; i < $scope.mistakesData.length; i++) {
            $scope.mistakesData[i].edit = false
        }

        dataForSend = {
            baseText: $scope.baseText,
            mistakeData: $scope.mistakesData,
            visibleData: $scope.reviewData,
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

        var keys = _.keys($scope.reviewData)
        for (var i = 0; i < keys.length; i++) {
            rowName = keys[i]
            var name = $scope.reviewData[rowName].name
            $scope.reviewData[rowName].data = $('#display_' + name).html()
            $scope.trustedHTML[rowName] = $sce.trustAs($sce.HTML, $scope.reviewData[rowName].data)
        }

    }

    $scope.sendBackToWork = function () {
        $scope.saveReviewData().then(
            function () {
                $http.get('/contentTask/taskPerform/sendBackToWork/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId).then(function (response) {
                    $scope.btnTextReviewReturn = "Соединяемся"
                }).then(function () {
                    $scope.btnTextReviewReturn = "Вернуть на доработку"
                    //TODO:при возврате в работу для правок выключить режим редактирования
                    $scope.subtaskStatus = 'mistake'
                    $scope.loadData()

                });
            }
        );
    }

    $scope.markAsDone = function () {
        $http.get('/contentTask/taskPerform/markAsDone/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId).then(function (response) {
            $scope.btnTextMarkAsDone = "Соединяемся..."
        }).then(function () {
            $scope.btnTextMarkAsDone = "Приянято"
            //TODO:при возврате в работу для правок выключить режим редактирования
            $scope.subtaskStatus = 'done'


        });
    }

    $scope.sendCheckRequest = function(name,mode=null){
        console.log('Отправляем запрос на проверку')
        console.log(name)
        $http.get('/contentTask/taskPerform/sendCheckRequest/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId+'/name/'+name+'/mode/'+mode).then(function (response) {

        }).then(function () {

        });
    }

    $scope.updateCheckRequest = function(index,uid){
        console.log('Запрашиваем данные')
        console.log(name)
        $http.get('/contentTask/taskPerform/updateCheckRequest/accessCode/' + $scope.accessCode + '/id/' + $scope.subtaskId+'/name/'+name+'/uid/'+uid).then(function (response) {
            data = response.data
            $scope.currentData[index].checkResult = data
        })
    }

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
