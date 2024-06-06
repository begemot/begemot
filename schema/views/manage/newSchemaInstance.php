<?php $data = "| Характеристика                          | Единицы измерения | UoMId | БТР-60            | БТР-70          | BТР-80          | БТР-90          |
|-----------------------------------------|-------------------|-------|-------------------|-----------------|-----------------|-----------------|
| Колесная формула                        |                   | NULL  | 8 × 8 / 4         | 8 × 8 / 4       | 8 × 8 / 4       | 8 × 8           |
| Снаряженная масса                       | т                 | 1000  | 9.9               | 11.5            | 13.6            | 22              |
| Экипаж                                  | чел               | 1001  | 2                 | 2               | 3               | 3               |
| Пассажиры                               | чел               | 1001  | 14                | 8               | 7               | 7               |
| Двигатель                               |                   | NULL  | Спаренные рядные 6-цилиндровые карбюраторные жидкостного охлаждения ГАЗ-40П | Два ЗМЗ-4905    | КамАЗ 7403 и ЯМЗ-238М2 мощностью 240 л.с. ЯМЗ-236Н | ЯМЗ 2В-06-2С |
| Мощность                                | лс                | 1002  | 2 × 90            | 2 × 120         | 260             | 510             |
| Удельная мощность                       | лс/т              | 1003  | 18.2              | 20              | 19.1            | нет данных      |
| Трансмиссия                             |                   | NULL  | механическая      | механическая    | автоматическая  | автоматическая гидромеханическая реверсивная |
| Шины                                    |                   | NULL  | пневматические, бескамерные | пневматические, бескамерные | пневматические, бескамерные | нет данных      |
| Подвеска                                |                   | NULL  | индивидуальная торсионная с гидравлическими амортизаторами | индивидуальная торсионная с гидравлическими амортизаторами | индивидуальная торсионная с гидравлическими амортизаторами | независимая торсионная с телескопическими гидроамортизаторами |
| Длина троса лебедки                     | м                 | 1004  | 50                | 50              | 50              | нет данных      |
| Предельное тяговое усилие на крюке      | тс                | 1005  | 6 (12 с блоком)   | 6 (12 с блоком) | 6 (12 с блоком) | нет данных      |
| Длина                                   | мм                | 1006  | 7560              | 7535            | 7650            | 8200            |
| Ширина                                  | мм                | 1006  | 2830              | 2800            | 2900            | 3100            |
| Высота                                  | мм                | 1006  | 2235              | 2235-2320       | 2350-2460       | 3000            |
| Колея                                   | мм                | 1006  | 2380              | 2380            | 2410            | нет данных      |
| Клиренс                                 | мм                | 1006  | 475               | 475             | 475             | 510             |
| Радиус поворота                         |                   | NULL  | 13.2              | 13.2            | 13.2            | нет данных      |
| Скорость                                |                   | 1008  | 80                | 80              | 80              | более 100       |
| Скорость по воде                        |                   | 1008  | 10                | 9-10            | 9               | 12              |
| Преодолеваемый ров                      | м                 | 1004  | 2.0               | 2.0             | 2.0             | нет данных      |
| Преодолеваемая стенка                   | м                 | 1004  | 0.5               | 0.5             | 0.5             | нет данных      |
| Преодолеваемый подъем                   | град.             | 1007  | 30                | 30              | 30              | нет данных      |
| Скорость по пересеченной местности      |                   | NULL  | нет данных        | 25-30 по грунтовым дорогам и колонным путям | 20-40 по грунту | нет данных      |
| Запас хода по шоссе                     | км                | 1009  | 500               | 400-600         | 600             | 800             |
| Запас хода по пересеченной местности    | км                | 1009  | нет данных        | 250-375 по грунтовым дорогам и колонным путям | 200-500 по грунтовым дорогам | нет данных      |
";?>

<script>
    var app = angular.module('myApp', []);

    app.controller('FormController', ['$http', function($http) {
      var ctrl = this;
      ctrl.formData = {};

      ctrl.submitForm = function() {
        // Prepare data to send
        var data = ctrl.formData;

        // Send POST request using $http service
        $http.post('/schema/Manage/MassDataProcess', data)
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

  <div class="container" ng-app="myApp" ng-controller="FormController as ctrl">
    <h2>Form with Textarea</h2>
    <form ng-submit="ctrl.submitForm()">
      <div class="control-group">
        <label class="control-label" for="inputName">Name</label>
        <div class="controls">
          <input type="text" id="inputName" ng-model="ctrl.formData.name" placeholder="Your Name">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputMessage">Message</label>
        <div class="controls">
          <textarea id="inputMessage" rows="4" ng-model="ctrl.formData.message" placeholder="Your Message">



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
  <?php print_r($data);?>