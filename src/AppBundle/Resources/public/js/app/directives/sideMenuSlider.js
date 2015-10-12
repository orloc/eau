'use strict';

angular.module('eveTool')
    .directive('sideMenuSlider', ['$animate', function($animate){
        return {
            restrict: 'E',
            transclude: true,
            require: '^sideMenuContainer',
            scope: {
                formContext: "=formContext",
                menuWidth: "=menuWidth"
            },
            link : function(scope, element, attributes, container) {
                scope.formContext = typeof attributes.formContext != 'undefined'
                    ? attributes.formContext
                    :  false;

                scope.active = false;

                $(element).css('width', attributes.menuWidth+'px').css('right', '-'+attributes.menuWidth+'px');

                $(element).show();

                container.addSideMenu(scope, element);
            },
            templateUrl: Routing.generate('template.slidemenu')
        };

    }]);
