'use strict';

angular.module('eveTool')
    .controller('buyBackConfigController', ['$scope', '$http', function($scope, $http){

        $scope.configuration = {};

        $http.get(Routing.generate('api.regions')).then(function(data){
            $scope.regions = data.data;
            console.log($scope.regions);
        });
    }]);
