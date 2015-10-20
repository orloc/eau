'use strict';

angular.module('eveTool')
    .controller('userNewController', ['$scope', '$http','dataDispatcher', function($scope, $http, dataDispatcher){
        $scope.submitLoading = false;
        $scope.newUser = {};
        $scope.roles = [
            {
                role: 'ROLE_ADMIN',
                name:'Admin'
            },
            {
                role: 'ROLE_USER',
                name:'User'
            },
            {
                role: 'ROLE_SUPER_ADMIN',
                name:'Super Admin'
            }
        ];

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
