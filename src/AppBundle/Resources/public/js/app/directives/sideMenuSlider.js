'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'E',
            transclude: true,
            controller: function(scope, element){
                scope.open = false;

                scope.$on("open_slide", function(event, args){
                    scope.open = args.open;
                });
            },
            link : function(scope, element, attributes) {
                scope.$observe('open', function(value){
                    value ? $animate.addClass(element, 'menuSlide') : $animate.addClass(element, 'menuSlide');
                });

            },
            templateUrl: Routing.generate('template.slidemenu')
        };

    }]);
