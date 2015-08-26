'use strict';

angular.module('eveTool')
    .controller('corpOverviewController', ['$scope', function($scope, $http){
        $scope.selected_corp = null;

        $scope.$on('selected_corporation', function(event, data){
            $scope.selected_corp = data;
        });

    }]);
