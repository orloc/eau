'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'E',
            transclude: true,
            require: '^sideMenuContainer',
            scope: {},
            link : function(scope, element, attributes, container) {
                container.addSideMenu(element);
            },
            templateUrl: Routing.generate('template.slidemenu')
        };

    }]);
