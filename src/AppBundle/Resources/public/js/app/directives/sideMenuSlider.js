'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'E',
            transclude: true,
            scope: {
                headerName: "=headerName",
                openEvent: "=eventName"
            },
            link : function(scope, element, attributes) {
                scope.$on(scope.openEvent, function(event, args){
                    attributes.open = args.open;
                });

                attributes.$observe('open', function(value){
                    value ? $animate.addClass(element, 'menuSlide') : $animate.addClass(element, 'menuSlide');
                });
            },
            templateUrl: Routing.generate('template.slidemenu')
        };

    }]);
