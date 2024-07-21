<?php
$this->menu = require dirname(__FILE__) . '/../default/commonMenu.php';

$cs = Yii::app()->clientScript;

// Подключение jQuery и Lodash из bower_components
$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/jquery/dist/jquery.min.js', 1);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/jquery/dist/jquery.min.js', 1);
$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/lodash/dist/lodash.min.js', CClientScript::POS_BEGIN);

// Подключение скриптов из модуля begemot
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/commonUi.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/modal.commonUi.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/jsonTable.commonUi.directive.js', CClientScript::POS_BEGIN);

?>

<script>
var app = angular.module('myApp', ['commonUi']);

app.controller('FormController', ['$http', '$scope', function($http, $scope) {
    var ctrl = this;
    ctrl.formData = {};
    $scope.test = 111;
    $scope.inputData = [{
            name: 'Название Field 1'
        },
        {
            name: 'Название Field 2'
        }
    ]
    $scope.outputData = []

    // ctrl.submitForm = function() {
    //     // Prepare data to send
    //     var data = ctrl.formData;

    //     // Send POST request using $http service
    //     $http.post('/schema/Manage/MassFieldImportFromMd', data)
    //         .then(function(response) {
    //             // Handle successful submission (e.g., display success message)
    //             console.log("Form submitted successfully!", response);
    //         })
    //         .catch(function(error) {
    //             // Handle submission errors
    //             console.error("Error submitting form:", error);
    //         });
    // };
}]);
</script>

<style>
.hidden-content {
    display: none;
}
</style>
<div class="container" ng-app="myApp" ng-controller="FormController as ctrl">
    <h2>Массовый ввод SchemaField по json</h2>



    <div class="container mt-5">
        <div class="row mt-5">
            <h2>Импорт данных JSON</h2>
            <json-table input-data="inputData" output-data="outputData"
                send-data-url='/schema/Manage/MassFieldImportFromMd' additional-data-for-send='selectedItem' mt='false'>
            </json-table>


        </div>
    </div>




</div>