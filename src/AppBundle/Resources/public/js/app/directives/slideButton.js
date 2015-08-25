'use strict';

angular.module('eveTool')
    .directive('slideButton', [ function(){
        return {
            restrict: 'E',
            require: '^sideMenuSlider',
            scope: {
                openEvent: "=openEvent"
            },
            link : function(scope, element, attributes, menu) {
                scope.open = false;
                scope.toggleOpen = function(){
                    scope.open = !scope.open;
                    console.log(menu);

                };

            },
            templateUrl : Routing.generate('template.slidebutton')
        };
    }]);