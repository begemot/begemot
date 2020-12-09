app.controller('performController', ['$scope', '$http', '$location','subTaskService','$route', '$routeParams', function ($scope, $http, $location,subTaskService,$route, $routeParams) {

    $scope.$route = $route;
    $scope.$location = $location;
    $scope.$routeParams = $routeParams;


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

    $scope.$on('$routeChangeStart', function($event, next, current) {
        console.log('изменение ссылки')
        console.log(next,next.params.tabName)

        var tabName = next.params.tabName

        if (tabName && tabName!='audit'){
            console.log('Делаем поиск');
            $scope.makeSearch(tabName)
        }


    });

    $scope.accessCode = accessCode

    subTaskService.accessCode = accessCode

    $scope.searchId = '';
    $scope.searchTitle = '';

    $scope.taskId = null;
    $scope.dataFields = []
    $scope.auditActiveTab = 0
    $scope.auditData = []
    $scope.symbSum = 0

    $http.get('/contentTask/taskPerform/dataFields/accessCode/' + $scope.accessCode).then(function(response){
        $scope.dataFields = response.data

    }).then(function(){$scope.loadAuditData()})

    $scope.setauditActiveField = function (index){
        $scope.auditActiveTab = index
        $scope.loadAuditData()
    }

    $scope.loadAuditData = function(){
        $http.get('/contentTask/taskPerform/loadAuditData/accessCode/' + $scope.accessCode+'/name'+'/'+$scope.dataFields[$scope.auditActiveTab].name).then(function(response){
            $scope.auditData = response.data
            $scope.symbSum = 0
            $scope.getSymbolsCount()
        })
    }

    $scope.sendCheckRequest = function(name,subTaskId,mode=null){
        console.log('Отправляем запрос на проверку')
        console.log(name)
        $http.get('/contentTask/taskPerform/sendCheckRequest/accessCode/' + $scope.accessCode + '/id/' + subTaskId+'/name/'+name+'/mode/'+mode).then(function (response) {
            $scope.loadAuditData()
        })
    }

    $scope.updateCheckRequest = function(index,name,subTaskId,uid){
        console.log('Запрашиваем данные')
        console.log(name)
        $http.get('/contentTask/taskPerform/updateCheckRequest/accessCode/' + $scope.accessCode + '/id/' +subTaskId+'/name/'+name+'/uid/'+uid).then(function (response) {
            data = response.data
            $scope.auditData[index].seoCheck = data


        })
    }



    $scope.getSymbolsCount = function (){
        for (var i=0;i<$scope.auditData.length;i++){
            // console.log($scope.auditData[i]);
            $scope.symbSum=parseInt($scope.symbSum, 10)+parseInt($scope.auditData[i].seoCheck.count_chars_without_space,10)
        }


    }

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




    $scope.setPage = function (page, type) {
        $scope.activePage[type] = page
        $scope.makeSearch(type)
    }



    $scope.makeSearch = function (type = "new") {
        $scope.resultItems = [];
        $http.get('/contentTask/taskPerform/ajaxAddedList/accessCode/' + $scope.accessCode + '?page=' + $scope.activePage[type] + '&id=' + $scope.searchId + '&title=' + $scope.searchTitle + "&type=" + type).then(function (response) {
            $scope.resultItems[type] = response.data.data;
            $scope.recordsCount[type] = response.data.count;
            console.log($scope.resultItems[type]);
        });
    }

    $scope.getStatusCounts = function () {
        $http.get('/contentTask/taskPerform/ajaxStatusList/accessCode/' + $scope.accessCode).then(function (response) {
            $scope.recordsCount = response.data
        });
    }

    subTaskService.getStatusCounts().then(()=>{ $scope.recordsCount = subTaskService.statusCounts} )


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

    // Экспорт на сайт
    $scope.pushToSite = function(subtaskId,taskId, index){

        subTaskService.pushToSite(subtaskId,taskId).then(()=>{
            $scope.resultItems.done[index].exported = 1
        })

    }

    $scope.approve = function(subtaskId,lineId){

        $http.get('/contentTask/taskPerform/markAsDone/accessCode/' + $scope.accessCode + '/id/' + subtaskId).then(function (response) {

        }).then(function () {
            $scope.resultItems.review.splice(lineId, 1);
            $scope.recordsCount.review = $scope.recordsCount.review-1;
        });
    }


}]).config(['$routeProvider',($routeProvider)=>{

    $routeProvider.when('/',{
        templateUrl:'/protected/modules/contentTask/views/taskPerform/jstpl/taskInfo.html',
        controller:'performController'
    }).when('/tab/:tabName',{

        controller:'performController'
    }).otherwise({
        template:'Нет такого шаблона!'
    });
}])

