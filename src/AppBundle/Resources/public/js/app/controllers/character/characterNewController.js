'use strict';

angular.module('eveTool')
    .controller('characterNewController', ['$scope', '$http','dataDispatcher', function($scope, $http, dataDispatcher){
        $scope.submitLoading = false;
        $scope.newCharacter = {};

        $scope.submit = function(){
            $scope.submitLoading = true;
            $http.post(Routing.generate('api.character_create'), $scope.newCharacter).then(function(data){
                $scope.submitLoading = false;
                $scope.newCharacter = {};
                $scope.errors = [];

                dataDispatcher.addEvent('update_list', data.data);
                dataDispatcher.addEvent('close_window', true);
            }).catch(function(data){
                $scope.errors = data.data;
                $scope.submitLoading = false;
            });
        };

    }]);
