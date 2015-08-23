'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'A',
            scope: {
                things: "=compareTo"
            },
            link : function(scope, element, attributes) {
                scope.nav_open  = false;


            }
        };

    }]);
