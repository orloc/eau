'use strict';

angular.module('eveTool')
.controller('userListController', ['$scope', '$http','$document', function($scope, $http, $document){
    $scope.users = [];
    $scope.nav_open = false;
    $scope.edit_open = false;
    $scope.edit_loaded = false;
    $scope.submitLoading = false;

    $scope.newUser = {};
    $scope.editUser = {};
    $scope.roles = [
        {
            role: 'ROLE_ADMIN',
            name:'Admin'
        }
    ];

    $http.get(Routing.generate('api.users')).then(function(data){
            $scope.users = data.data;
    });


    $scope.submit = function(){
        $scope.submitLoading = true;
        $http.post(Routing.generate('api.user_create'), $scope.newUser).then(function(data){
            $scope.users.push(data.data);
            $scope.submitLoading = false;

            $scope.newUser = {};
            if ($scope.nav_open){
                toggleNav('.new');
            }
        }).catch(function(data){
            $scope.errors = data.data;
            $scope.submitLoading = false;
        });
    };

    $scope.update = function(){
        $scope.submitLoading = true;
        $http.put(Routing.generate('api.user_update', { id: $scope.editUser.id }), $scope.editUser).then(function(data){
            $scope.users[$scope.current_index] = data.data;
            $scope.submitLoading = false;

            if ($scope.edit_open){
                toggleNav('.edit');
            }
        }).catch(function(data){
            $scope.errors = data.data;
            $scope.submitLoading = false;
        });
    };

    $scope.openNew = function(){
        toggleNav('.new');
        $scope.nav_open = !$scope.nav_open;
    };

    $scope.openEdit = function(id, index){
        if (id === $scope.edit_id){
            toggleNav('.edit');
            $scope.edit_open = !$scope.edit_open;
        } else {
            toggleNav('.edit');
            $scope.edit_open = !$scope.edit_open;
            $scope.edit_id = id;
            $http.get(Routing.generate('api.user_show', { id: id })).then(function(data){
                $scope.editUser = data.data;
                $scope.editUser.role = data.data.roles[0];
                $scope.edit_loaded = true;
                $scope.current_index = index;
            });
        }

    };

    $scope.delete = function(id){
        var response = window.confirm('Are you sure you wish to delete this user?');

        if (response){
            $http.delete(Routing.generate('api.user_delete', { id: id })).then(function(data){
                console.log(data);
                $scope.users.splice($scope.current_index, 1);
                toggleNav('.edit');
            });
        }
    };

    function toggleNav(className) {
        if (!$scope.nav_open && !$scope.edit_open){
            $('.push-menu'+className).animate({
                right: "0px"
            }, 300);

            $('body').animate({
                left: "-350px"
            }, 300);
        } else {
            $('.push-menu'+className).animate({
                right: "-350px"
            }, 300);

            $('body').animate({
                left: "0px"
            }, 300);

            $scope.edit_id = null;
            $scope.edit_open = false;
            $scope.edit_loaded = false;
            $scope.current_index = null;
            $scope.editUser = {};
            $scope.newUser = {};
            $scope.editUser = {};
            $scope.errors = {};
        }

    }
}]);