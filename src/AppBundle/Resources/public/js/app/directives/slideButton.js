'use strict';

angular.module('eveTool')
    .directive('slideButton', [ function(){
        return {
            restrict: 'E',
            require: '^sideMenuContainer',
            scope: {},
            link : function(scope, element, attributes, container) {
                scope.toggleOpen = function(){
                    scope.open = container.toggleOpen();
                };

                container.addButton(scope);
            },
            templateUrl : Routing.generate('template.slidebutton')
        };
    }]);