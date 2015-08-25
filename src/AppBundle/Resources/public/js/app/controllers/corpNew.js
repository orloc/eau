'use strict';

angular.module('eveTool')
    .controller('corpNewController', ['$scope', '$http', function($scope, $http){
        $scope.submitLoading = false;
        $scope.newCorp = {};

        $scope.submit = function(){
            $scope.submitLoading = true;
            /*
            $http.post(Routing.generate('api.corp_create'), $scope.newCorp).then(function(data){
                $scope.corps.push(data.data);
                $scope.submitLoading = false;

                $scope.newUser = {};
            }).catch(function(data){
                $scope.errors = data.data;
                $scope.submitLoading = false;
            });
            */
        };

    }]);
