'use strict';

angular.module('eveTool')
    .directive('sideMenuContainer', [ '$animate', function($animate){
        return {
            restrict: 'E',
            scope: {},
            controller: function($scope){
                var buttons = [];
                var sideMenus = [];

                this.addSideMenu = function(scope, menu){
                    sideMenus.push({ scope: scope, menu: menu });
                };

                this.addButton = function(btn){
                    buttons.push(btn);
                };

                this.toggleOpen = function(openType){
                    if (!openType){
                        return;
                    }

                    var m = _.find(sideMenus, function(menu){
                        return openType === menu.scope.formContext;
                    });

                    var isOpen = m.scope.active;

                    if (isOpen){
                        m.scope.closeMenu();
                    } else {
                        angular.forEach(sideMenus, function(m){
                            if (m.scope.active){
                                m.scope.closeMenu();
                                var button = _.find(buttons, function(b){
                                    return b.openType === m.scope.formContext;
                                });

                                button.open = false;
                            }
                        });
                        m.scope.openMenu();
                    }

                    return !isOpen;
                };

                $scope.$on('close_window', function(){
                    angular.forEach(sideMenus, function(m){
                        m.scope.active = false;
                    });

                    angular.forEach(buttons, function (b){
                        b.open = false;
                    });
                });
            },
            transclude: true
        };

    }]);
