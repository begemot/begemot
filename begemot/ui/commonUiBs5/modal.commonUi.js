// app.js
angular.module('commonUi')
.directive('modal', function($document, $timeout) {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      title: '@',
      visible: '='
    },
    template: `
      <div ng-show="visible">
        <div class="modal-backdrop fade show" ng-style="{'z-index': zIndex}" ng-click="close()"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" ng-style="{'z-index': zIndex}">
          <div class="modal-dialog" role="document">
            <div class="modal-content" ng-click="$event.stopPropagation()">
              <div class="modal-header">
                <h5 class="modal-title">{{ title }}</h5>
                <button type="button" class="btn-close" aria-label="Close" ng-click="close()"></button>
              </div>
              <div class="modal-body" ng-transclude>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" ng-click="close()">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `,
    link: function(scope, element, attrs) {
      
      scope.zIndex = 1050; // Default Bootstrap z-index

      scope.close = function() {
        scope.visible = false;
      };

      scope.$watch('visible', function(newValue, oldValue) {
        if (newValue) {
          $timeout(function() {
            var modalElement = element.find('.modal');
            var computedZIndex = window.getComputedStyle(modalElement[0]).zIndex;
            scope.zIndex = parseInt(computedZIndex, 10) || 1050;

            modalElement.addClass('show d-block').removeClass('d-none');
            element.find('.modal-backdrop').addClass('show d-block').removeClass('d-none');
            
            // Добавляем обработчик кликов на документ при открытии модального окна
            $document.on('click', documentClickHandler);
          });
        } else {
          element.find('.modal').removeClass('show d-block').addClass('d-none');
          element.find('.modal-backdrop').removeClass('show d-block').addClass('d-none');
          
          // Удаляем обработчик кликов с документа при закрытии модального окна
          $document.off('click', documentClickHandler);
        }
      });

      // Обработчик кликов по документу
      function documentClickHandler(event) {
        // Проверяем, кликнули ли не по элементу модального окна
        if (!element[0].contains(event.target) && scope.visible) {
          scope.$apply(scope.close);
        }
      }

      // Предотвращаем закрытие при клике внутри модального окна
      element.find('.modal-content').on('click', function(event) {
        event.stopPropagation();
      });
    }
  };
});
