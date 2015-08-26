'use strict';

angular.module('eveTool')
    .controller('userEditController', ['$scope', '$http', function($scope, $http){
        $scope.edit_loaded = false;
        $scope.submitLoading = false;

        $scope.editUser = {};
        $scope.roles = [
            {
                role: 'ROLE_ADMIN',
                name:'Admin'
            }
        ];

        $scope.update = function(){
            $scope.submitLoading = true;
            $http.put(Routing.generate('api.user_update', { id: $scope.editUser.id }), $scope.editUser).then(function(data){
                $scope.users[$scope.current_index] = data.data;
                $scope.submitLoading = false;

                if ($scope.edit_open){
                    $scope.closeEdit();
                }
            }).catch(function(data){
                $scope.errors = data.data;
                $scope.submitLoading = false;
            });
        };

        $scope.delete = function(id){
            var response = window.confirm('Are you sure you wish to delete this user?');

            /*
            if (response){
                $http.delete(Routing.generate('api.user_delete', { id: id })).then(function(data){
                    console.log(data);
                    $scope.users.splice($scope.current_index, 1);
                });
            }
            */
        };
    }]);
