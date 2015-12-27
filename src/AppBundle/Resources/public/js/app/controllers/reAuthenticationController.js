'use strict';

angular.module('eveTool')
    .controller('reAuthenticationController', ['$scope', '$uibModalInstance','$http','character', function($scope, $uibModalInstance, $http, character){
        $scope.state = 0;
        $scope.model = {};

        $scope.character = character;

        function getResult(){
            return { password: $scope.model.password };
        }

        $scope.ok = function(){
            $scope.error = false;
            $http.post(Routing.generate('api.reAuth'), getResult()).then(function(res){
                var res = res.data;
                if (res.result === true){
                    $scope.state = 1;
                    $http.get(Routing.generate('api.character.apicredentials', { id:  character.id })).then(function(data){
                        $scope.api_key = data.data;
                    });
                } else {
                   $scope.error = true;
                }
            });
        };

        $scope.cancel = function(){
            $uibModalInstance.dismiss('canceled');
        };
    }]);
