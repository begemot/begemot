angular.module('performApp').service('subTaskService', subTaskService)


function subTaskService($http) {
    var vm = this

    vm.accessCode = null;
    vm.statusCounts = null;

    vm.subTaskId = null
    vm.iteration = null
    vm.currentIteration = null

    vm.baseData = null
    vm.currentData = null
    vm.subtaskStatus = null

    vm.getCurrentData = () => {
        if (!vm.currentData){
            vm.loadData()
        }
        return vm.currentData
    }

    vm.getBaseData = () => {
        if (!vm.currentData){
            vm.loadData()
        }
        return vm.currentData
    }

    vm.getStatus = ()=>{
        if (!vm.subtaskStatus){
            vm.loadData()
        }
        return vm.subtaskStatus
    }

    vm.loadData = function(){
        return $http.get('/contentTask/taskPerform/ajaxGetDataAndFields/accessCode/' + vm.accessCode + '/id/' + vm.subtaskId).then(function (response) {
            vm.baseData = response.data.base
            vm.currentData = response.data.current


            vm.subtaskStatus = response.data.status
            vm.currentIteration = response.data.iteration
        })
    }

    //эти функции испльзуются в index и позже их надо переделать
    vm.pushToSite = (subtaskId, taskId) => {
        return $http.get('/contentTask/taskPerform/ajaxPushToSite/accessCode/' + vm.accessCode + "/taskId/" + taskId + "/subtaskId/" + subtaskId)
    }

    vm.getStatusCounts = () => {
        return $http.get('/contentTask/taskPerform/ajaxStatusList/accessCode/' + vm.accessCode).then(function (response) {
            console.log(response.data);
            vm.statusCounts = response.data
        });
    }

}