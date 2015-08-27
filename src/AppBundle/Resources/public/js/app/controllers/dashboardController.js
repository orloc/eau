'use strict';

angular.module('eveTool')
    .controller('dashboardController', ['$scope', '$http', function($scope, $http){

        $http.get(Routing.generate('api.corporation.assets', { id: 4})).then(function(data){
            $scope.assets = data.data;
        });
    }]);
