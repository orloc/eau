'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'E',
            transclude: true,
            require: '^sideMenuContainer',
            scope: {
                formContext: "=formContext"
            },
            link : function(scope, element, attributes, container) {
                scope.formContext = typeof attributes.formContext != 'undefined'
                    ? attributes.formContext
                    :  false;

                scope.active = false;

                container.addSideMenu(scope, element);
            },
            templateUrl: Routing.generate('template.slidemenu')
        };

    }]);
