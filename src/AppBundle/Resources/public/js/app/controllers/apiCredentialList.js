'use strict';

angular.module('eveTool')
.controller('apiCredentialsController', ['$scope', '$http', function($scope, $http){
    $scope.api_credentials = [];

    $scope.newCorp = {};

    $http.get(Routing.generate('api.api_credentials')).then(function(data){
        $scope.api_credentials = data.data;
    });
}]);