<?php
$this->menu = require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<script>
var app = angular.module('myApp', []);

app.controller('FormController', ['$http', function($http) {
    var ctrl = this;
    ctrl.formData = {};

    ctrl.submitForm = function() {
        // Prepare data to send
        var data = ctrl.formData;

        // Send POST request using $http service
        $http.post('/schema/Manage/MassDataProcess?XDEBUG_SESSION_START', data)
            .then(function(response) {
                // Handle successful submission (e.g., display success message)
                console.log("Form submitted successfully!", response);
            })
            .catch(function(error) {
                // Handle submission errors
                console.error("Error submitting form:", error);
            });
    };
}]);
</script>

<style>
.hidden-content {
    display: none;
}
</style>
<div class="container" ng-app="myApp" ng-controller="FormController as ctrl">
    <h2>Массовая обработка MD данных</h2>
    <h3>Структура таблицы для примера</h3>



    <div class="container mt-5">
        <button id="toggleButton" class="btn btn-primary mb-3">Показать/Скрыть таблицы</button>
        <div id="content" class="hidden-content">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Характеристика</th>
                        <th>Четра</th>
                        <th>Четра 2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Название</td>
                        <td>Четра</td>
                        <td>Четра 2</td>
                    </tr>
                    <tr>
                        <td>Двигатель</td>
                        <td>ЯМЗ-236Б-2 с газотурбинным надувом</td>
                        <td>ЯМЗ-238Б-4 с турбонаддувом</td>
                    </tr>
                    <tr>
                        <td>Мощность</td>
                        <td>184 (250)</td>
                        <td>220 (300)</td>
                    </tr>
                </tbody>
            </table>
            <h3>Таблица для которая нужна на вход(такую отдает GPT)</h3>
            <pre>| Характеристика                         | Четра                                                         | Четра 2                    |
|----------------------------------------|----------------------------------------------------------------|----------------------------|
| Название                               | Четра                                                          | Четра 2                    |
| Двигатель                              | ЯМЗ-236Б-2 с газотурбинным надувом                              | ЯМЗ-238Б-4 с турбонаддувом |
| Мощность                               | 184 (250)                                                       | 220 (300)                  |
</pre>
        </div>
    </div>
    <script>
    document.getElementById('toggleButton').addEventListener('click', function() {
        var content = document.getElementById('content');
        if (content.classList.contains('hidden-content')) {
            content.classList.remove('hidden-content');
        } else {
            content.classList.add('hidden-content');
        }
    });
    </script>
    <form ng-submit="ctrl.submitForm()">

        <div class="control-group">
            <label class="control-label" for="inputMessage">Message</label>
            <div class="controls">
                <textarea style="height:300px;width:100%" id="inputMessage" rows="4" ng-model="ctrl.formData.message"
                    placeholder="Your Message">



          </textarea>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>