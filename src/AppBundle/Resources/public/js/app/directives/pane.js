angular.module('eveTool')
.directive('evePane', function() {
    return {
        require: '^eveTabs',
        restrict: 'E',
        transclude: true,
        scope: {
          title: '@'
        },
        link: function(scope, element, attrs, tabsCtrl) {
          tabsCtrl.addPane(scope);
        },
        templateUrl: Routing.generate('template.evepanes')
    };
});