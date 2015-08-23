'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'A',
            scope: {
                things: "=compareTo"
            },
            link : function(scope, element, attributes, ngModel) {
                $scope.nav_open 
            }
        };

    }]);
