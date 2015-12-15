'use strict';

angular.module('eveTool')
    .controller('userEditController', ['$scope', '$http', 'dataDispatcher', 'userRoleManager',  function($scope, $http, dataDispatcher, userRoleManager){
        $scope.submitLoading = false;
        $scope.edit_password = false;
        $scope.editUser = {};
        $scope.my_id = userRoleManager.getUserId();

        // if user auth has the right roles get all of them
        $scope.roles = userRoleManager.getRoles();
        var canAccess = function(){
            return $scope.my_id === $scope.editUser.id;
        };


        $scope.$on('update_user', function(event, item){
            $scope.editUser = item.user;
            $scope.hasAccess = canAccess();
            $scope.editUser.role = userRoleManager.getHighestFromMap(
                userRoleManager.mapRoles(item.user.roles)
            ).role;
            $scope.current_index = item.index;

        });

        $scope.toggleEditPassword = function(){
            $scope.edit_password = !$scope.edit_password;
        };

        $scope.update = function(){
            $scope.submitLoading = true;
            $http.put(Routing.generate('api.user_update', { id: $scope.editUser.id }), $scope.editUser).then(function(data){
                $scope.users[$scope.current_index] = data.data;
                $scope.submitLoading = false;
                $scope.editUser = {};
                $scope.errors = {};

                dataDispatcher.addEvent('close_window', true);

            }).catch(function(data){
                $scope.errors = data.data;
                $scope.submitLoading = false;
            });
        };

        $scope.delete = function(id){
            var response = window.confirm('Are you sure you wish to delete this user?');

            if (response === true){
                $http.delete(Routing.generate('api.user_delete', { id: id })).then(function(data){
                    dataDispatcher.addEvent('close_window');
                    dataDispatcher.addEvent('update_list');
                });
            }
        };
    }]);
