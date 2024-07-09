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
        $http.post('/schema/Manage/MassFieldImportFromMd', data)
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
                
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td> Тип двигателя</td>
              
                    </tr>
                    <tr>
                        <td>Усиление переднего и заднего мостов </td>
               
                    </tr>
                    <tr>
                        <td>и так далее</td>
                    
                    </tr>
                </tbody>
            </table>
            <h3>Таблица для которая нужна на вход(такую отдает GPT)</h3>
            <pre>
            | Характеристика                           |
|------------------------------------------|
| Название                                 |
| Колесная формула                         |
| Мощность двигателя                       |
| Тип двигателя                            |

| Усиление переднего и заднего мостов      |
| Подножка                                 |
| Шины                                     |
| Диски                                    |
| Давление на грунт на колесах             |
| Давление на грунт на гусеницах           |
| Клиренс на шинах                         |
| Клиренс на гусеницах                     |
| Гарантия                                 |
| Условия эксплуатации                     |
| Температура эксплуатации                 |
| Цвет                                     |
| Дополнительные характеристики            |
| Дополнительный генератор                 |

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