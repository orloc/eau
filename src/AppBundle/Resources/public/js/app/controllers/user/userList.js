'use strict';

angular.module('eveTool')
.controller('userListController', ['$scope', '$http','$document', function($scope, $http){
    $scope.users = [];

    $http.get(Routing.generate('api.users')).then(function(data){
            $scope.users = data.data;
    });

    $scope.$on('update_list', function(event, item){
        $scope.users.push(item);
    });

}]);