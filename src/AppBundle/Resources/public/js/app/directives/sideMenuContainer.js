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

                this.addSideMenu = function(menu){
                    sideMenu.push(menu);
                };

                this.addButton = function(btn){
                    buttons.push(btn);
                };

                this.toggleOpen = function(window){
                    $scope.open = !$scope.open;
                    return $scope.open;
                };



                $scope.$on('close_window', function(){
                    button.open = $scope.open = !$scope.open;
                });

                $scope.$watch('open', function(value){
                    if (value){
                        $(sideMenu).animate({
                            right: "0px"
                        }, 300);

                        $('body').animate({
                            left: "-350px"
                        }, 300);
                    } else {
                        $(sideMenu).animate({
                            right: "-350px"
                        }, 300);

                        $('body').animate({
                            left: "0px"
                        }, 300);
                    }

                });
            },
            transclude: true
        };

    }]);
