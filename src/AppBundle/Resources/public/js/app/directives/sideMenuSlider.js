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

                var width = scope.menuWidth;

                scope.active = false;

                scope.openMenu = function(speed){
                    if (typeof speed === 'undefined'){
                        speed = 300;
                    }

                    $(element).animate({
                        right: "0px"
                    }, { duration: speed, queue: false });

                    $('body').animate({
                        left: "-"+width
                    }, { duration: speed, queue: false });

                    scope.active = true;

                    $('#page-overlay').fadeIn('fast');
                };

                scope.closeMenu = function(speed){
                    if (typeof speed === 'undefined'){
                        speed = 300;
                    }

                    $(element).animate({
                        right: "-"+width
                    }, { duration: speed, queue: false });

                    $('body').animate({
                        left: "0px"
                    }, { duration: speed, queue: false });

                    scope.active = false;

                    $('#page-overlay').fadeOut('fast');
                };

                $(element).css('width', attributes.menuWidth+'px').css('right', '-'+attributes.menuWidth+'px');

                $(element).show();

                container.addSideMenu(scope, element);
            },
            templateUrl: Routing.generate('template.slidemenu')
        };

    }]);
