'use strict';

angular.module('eveTool')
    .directive('sideMenuContainer', [ '$animate', function($animate){
        return {
            restrict: 'E',
            scope: {},
            controller: function($scope){
                var buttons = [];
                var sideMenus = [];

                $scope.open = false;

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

                    m.scope.active = true;
                    $scope.open = !$scope.open;
                    return $scope.open;
                };

                $scope.$on('close_window', function(){
                    $scope.open = !$scope.open;
                    angular.forEach(buttons, function (b){
                        b.open = $scope.open;
                    });
                });

                $scope.$watch('open', function(value){
                    var sideMenu = _.find(sideMenus, function(menu){
                        return menu.scope.active === true;
                    });

                    if (typeof sideMenu !== 'undefined'){
                        var width = sideMenu.scope.menuWidth;
                        if (typeof sideMenu != 'undefined'){
                            if (value){
                                $(sideMenu.menu).animate({
                                    right: "0px"
                                }, 300);

                                $('body').animate({
                                    left: "-"+width
                                }, 300);
                            } else {
                                $(sideMenu.menu).animate({
                                    right: "-"+width
                                }, 300);

                                $('body').animate({
                                    left: "0px"
                                }, 300);
                            }
                        }
                    }


                });
            },
            transclude: true
        };

    }]);
