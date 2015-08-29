'use strict';

angular.module('eveTool')
    .directive('slideButton', [ function(){
        return {
            restrict: 'E',
            require: '^sideMenuContainer',
            scope: {},
            link : function(scope, element, attributes, container) {
                var openType = typeof attributes.openType  != 'undefined'
                    ? attributes.openType
                    : false;

                scope.openType = openType;

                scope.toggleOpen = function(){
                    scope.open = container.toggleOpen(scope.openType);
                };

                container.addButton(scope);
            },
            templateUrl : Routing.generate('template.slidebutton')
        };
    }]);