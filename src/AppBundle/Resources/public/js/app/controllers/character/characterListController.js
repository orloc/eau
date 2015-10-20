'use strict';

angular.module('eveTool')
    .controller('characterListController', ['$scope', '$http', 'dataDispatcher', function($scope, $http, dataDispatcher){
        $scope.characters = [];

        $http.get(Routing.generate('api.characters')).then(function(data){
            console.log(data);
            $scope.characters = data.data;
        });

        $scope.$on('update_list', function(event, item){
            $scope.characters.push(item);
        });

    }]);
