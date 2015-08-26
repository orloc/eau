'use strict';

angular.module('eveTool')
    .controller('corpNewController', ['$scope', '$http', 'dataDispatcher', function($scope, $http, dataDispatcher){
        $scope.submitLoading = false;
        $scope.newCorp = {};

        $scope.submit = function(){
            $scope.submitLoading = true;

            $http.post(Routing.generate('api.corp_create'), $scope.newCorp).then(function(data){
                $scope.submitLoading = false;
                $scope.newCorp = {};
                $scope.errors = [];

                dataDispatcher.addEvent('update_list', data.data);
                dataDispatcher.addEvent('close_window', true);
            }).catch(function(data){
                $scope.errors = data.data;
                $scope.submitLoading = false;
            });
        };

    }]);
