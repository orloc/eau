'use strict';

angular.module('eveTool')
    .controller('marketOrderStatsController', ['$scope', '$http','corporationDataManager', '$filter', 'userRoleManager', function($scope, $http, corporationDataManager, $filter, userRoleManager){
        $scope.selected_items = [];
        $scope.selected_price_profiles = [];

        $scope.clearItems = function(){
            $scope.selected_items = [];
        };

        $scope.is_granted = function(role){
            return userRoleManager.isGranted(role, roles);
        };

    }]);
