'use strict';
angular.module('eveTool')
    .directive('eveTabs', ['dataDispatcher', function(dataDispatcher) {
        return {
            restrict: 'E',
            transclude: true,
            scope: {},
            controller: function($scope) {
                $scope.templateUrl = '';
                var panes = $scope.panes = [];

                $scope.select = function(pane) {
                    angular.forEach(panes, function(pane) {
                        pane.selected = false;
                    });
                    pane.selected = true;

                    $scope.selected_pane = pane.title;
                };

                this.setTabTemplate = function(templateUrl){
                    $scope.templateUrl = templateUrl;
                };

                this.addPane = function(pane) {
                    if (panes.length === 0) {
                        $scope.select(pane);
                    }
                    panes.push(pane);
                };

                $scope.$watch('templateUrl', function(){
                    dataDispatcher.addEvent('tab_changed', $scope.selected_pane);
                });
            },
            templateUrl: Routing.generate('template.evetabs')
        };

    }]);