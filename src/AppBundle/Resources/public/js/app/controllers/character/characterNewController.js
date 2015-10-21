'use strict';

angular.module('eveTool')
    .controller('characterNewController', ['$scope', '$http','dataDispatcher', function($scope, $http, dataDispatcher){
        $scope.submitLoading = false;
        $scope.newCharacter = {};
        $scope.newKey = {};

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

        $scope.$on('new_char_api', function(event, data){
            $scope.current_char = data;
        });

        $scope.key_submit = function(char){
            $scope.submitLoading = true;
            $http.post(Routing.generate('api.character.apicredentials.update', { id: $scope.current_char.id }), $scope.newKey).then(function(data){
                $scope.submitLoading = false;
                $scope.newKey = {};
                $scope.errors = [];

                dataDispatcher.addEvent('update_key_list', data.data);
                dataDispatcher.addEvent('close_window', true);
            }).catch(function(data){
                $scope.errors = data.data;
                $scope.submitLoading = false;
            });

        }

    }]);
