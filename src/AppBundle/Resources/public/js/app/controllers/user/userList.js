'use strict';

angular.module('eveTool')
.controller('userListController', ['$scope', '$http','dataDispatcher',  function($scope, $http, dataDispatcher){
    $scope.users = [];

    $http.get(Routing.generate('api.users')).then(function(data){
            $scope.users = data.data;
    });

    $scope.populateEdit = function(user){

        dataDispatcher.addEvent('update_user', user);
    };

    $scope.$on('update_list', function(event, item){
        $http.get(Routing.generate('api.users')).then(function(data){
            $scope.users = data.data;
        });

    });

}]);