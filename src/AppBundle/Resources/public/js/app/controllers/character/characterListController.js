'use strict';

angular.module('eveTool')
    .controller('characterListController', ['$scope', '$http', 'dataDispatcher', function($scope, $http, dataDispatcher){
        $scope.characters = [];
        $scope.selected_character = null;
        $scope.api_credentials = null;

        $http.get(Routing.generate('api.characters')).then(function(data){
            $scope.characters = data.data;
        });

        $scope.selectCharacter = function(c){
            $scope.selected_character = c;

            $http.get(Routing.generate('api.character.apicredentials', { id: c.id })).then(function(data){
                $scope.api_credentials = data.data;

            }).then(function(){
                dataDispatcher.addEvent('new_char_api', c);
            });
        };

        $scope.removeChar = function(){
            $scope.selected_character = null;
            $scope.api_credentials = null;
        };


        $scope.$on('update_list', function(event, item){
            $scope.characters.push(item);
        });

        $scope.$on('update_key_list', function(event, item){
            $scope.api_credentials.push(item);
        });
    }]);
