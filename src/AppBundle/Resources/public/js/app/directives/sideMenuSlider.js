'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'E',
            transclude: true,
            scope: {
                headerName: "=headerName",
                openEvent: "=openEvent"
            },
            link : function(scope, element, attributes) {
                scope.headerName = attributes.headerName;

                attributes.$observe('open', function(value){
                    value ? $animate.addClass(element, 'menuSlide') : $animate.addClass(element, 'menuSlide');
                });
            },
            templateUrl: Routing.generate('template.slidemenu')
        };

    }]);
