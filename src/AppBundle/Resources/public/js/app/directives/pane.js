angular.module('eveTool')
.directive('evePane', ['dataDispatcher', function(dataDispatcher) {
    return {
        require: '^eveTabs',
        restrict: 'E',
        transclude: true,
        scope: {
            title: '@',
            templateUrl: '@'
        },
        link: function(scope, element, attrs, tabsCtrl) {
            tabsCtrl.addPane(scope);

            scope.select = function(){
                tabsCtrl.select(scope);
            };

            scope.$watch('selected', function(){
                if (scope.selected){
                    tabsCtrl.setTabTemplate(scope.templateUrl);
                }
            });
        },
        templateUrl: Routing.generate('template.evepanes')
    };
}]);