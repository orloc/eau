'use strict';

angular.module('eveTool')
    .controller('characterNewController', ['$scope', '$http','dataDispatcher', function($scope, $http, dataDispatcher){
        $scope.submitLoading = false;
        $scope.newCharacter = {};
        $scope.newKey = {};
        $scope.stage = 1;
        $scope.char_result = null;
        $scope.selected_character = null;

        $scope.submit = function(){
            $scope.submitLoading = true;
            $http.post(Routing.generate('api.character_create.validate'), $scope.newCharacter).then(function(data){
                $scope.submitLoading = false;
                $scope.newCharacter = {};
                $scope.errors = [];

                $scope.char_result = data.data;

                angular.forEach($scope.char_result.result.key.characters, function(c){
                    if (c.best_guess){
                        $scope.selected_character = c;
                    }
                });

                $scope.stage = 2;

            }).catch(function(data){
                $scope.errors = [data.data];
                $scope.submitLoading = false;
            });
        };

        $scope.finalSubmit = function(){
            if ($scope.selected_character === null || $scope.char_result === null){
                return;
            }

            var obj = {
                char: $scope.selected_character,
                api_key: $scope.char_result.result.key.api_key,
                verification_code: $scope.char_result.result.key.verification_code,
                full_key: $scope.char_result
            };

            $http.post(Routing.generate('api.character_create.finalize'), obj).then(function(data){
                console.log(data)  ;
            });
        };

        $scope.selectCharacter = function(c){
            $scope.selected_character = c;
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
