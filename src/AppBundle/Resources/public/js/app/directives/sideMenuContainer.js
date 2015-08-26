'use strict';

angular.module('eveTool')
    .directive('sideMenuContainer', [ '$animate', function($animate){
        return {
            restrict: 'E',
            scope: {},
            controller: function($scope){
                var button = $scope.button;
                var sideMenu = $scope.sideMenu;

                $scope.open = false;

                this.addSideMenu = function(menu){
                    sideMenu = menu;
                };

                this.addButton = function(btn){
                    button = btn;
                };

                this.toggleOpen = function(){
                    $scope.open = !$scope.open;
                    return $scope.open;

                };

                $scope.$watch('open', function(value){
                    if (value){
                        console.log(sideMenu);
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
