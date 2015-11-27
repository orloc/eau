'use strict';

angular.module('eveTool')
    .controller('userNewController', ['$scope', '$http','dataDispatcher', 'userRoleManager', function($scope, $http, dataDispatcher, userRoleManager){
        $scope.submitLoading = false;
        $scope.newUser = {};

        // if user auth has the right roles get all of them
        $scope.roles = userRoleManager.getRoles();

        $scope.submit = function(){
            $scope.submitLoading = true;
            $http.post(Routing.generate('api.user_create'), $scope.newUser).then(function(data){
                $scope.users.push(data.data);
                $scope.submitLoading = false;
                $scope.newUser = {};
                $scope.errors = [];

                dataDispatcher.addEvent('update_list', data.data);
                dataDispatcher.addEvent('close_window', true);
            }).catch(function(data){
                $scope.errors = data.data;
                $scope.submitLoading = false;
            });
        };

    }]);
