// app.js
angular.module('commonUi', [])
.directive('modal', function() {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      title: '@',
      visible: '='
    },
    template: `
      <div class="modal fade" tabindex="-1" role="dialog" ng-class="{ 'show d-block': visible, 'd-none': !visible }">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
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
    `,
    link: function(scope, element, attrs) {
      
      scope.close = function() {
        scope.visible = false;
      };

      scope.$watch('visible', function(newValue, oldValue) {
        if (newValue) {
          element.find('.modal').addClass('show d-block').removeClass('d-none');
        } else {
          element.find('.modal').removeClass('show d-block').addClass('d-none');
        }
      });
    }
  };
});