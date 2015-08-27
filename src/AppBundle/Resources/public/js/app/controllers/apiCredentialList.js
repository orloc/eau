'use strict';

angular.module('evetool')
.controller('apicredentialscontroller', ['$scope', '$http', function($scope, $http){
    $scope.api_credentials = [];

    $scope.newcorp = {};

    $http.get(routing.generate('api.api_credentials')).then(function(data){
        $scope.api_credentials = data.data;
    });
}]);