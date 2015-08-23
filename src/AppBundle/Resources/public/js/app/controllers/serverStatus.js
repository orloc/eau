'use strict';

angular.module('eveTool')
    .controller('serverStatusController', ['$scope', '$http', function($scope, $http){
        $scope.status = {};

        $http.get(Routing.generate('api.server.status')).then(function(data){
            $scope.status = data.data;
        });
    }]);
