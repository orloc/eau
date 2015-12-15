'use strict';

angular.module('eveTool')
.controller('userListController', ['$scope', '$http','dataDispatcher', 'userRoleManager',  function($scope, $http, dataDispatcher, userRoleManager){
    $scope.users = [];

    $scope.currentRoles = userRoleManager.getCurrentRoles();
    $scope.my_highest_role = userRoleManager.getHighestFromMap($scope.currentRoles);
    $scope.my_id = userRoleManager.getUserId();

    $scope.hasRole = function(role){
        return userRoleManager.isGranted(role, $scope.currentRoles);
    };

    $scope.compareRoles = function(user){
        if (user.id === userRoleManager.getUserId()){ return true; }

        var roles = userRoleManager.mapRoles(user.roles);
        var highest = userRoleManager.getHighestFromMap(roles);

        return highest.weight < $scope.my_highest_role.weight;
    };

    $scope.populateEdit = function(user, index){
        dataDispatcher.addEvent('update_user', { user:user, index:index});
    };

    $scope.$on('update_list', function(event, item){
        $http.get(Routing.generate('api.users')).then(function(data){
            $scope.users = data.data;
        });

    });

    $http.get(Routing.generate('api.users')).then(function(data){
        var getMain = function(chars){
            var id = false;
            angular.forEach(chars, function(c){
                if (c.is_main){
                    id = c.eve_id;
                }
            });
            return id;
        };

        angular.forEach(data.data, function(u){
            u.main_id = getMain(u.characters);
            u.named_role = userRoleManager.getTopRole(u.roles);
        });

        $scope.users = data.data;
    });

   $scope.getRole = function(u){
       return u.named_role;
   };

    $scope.hasApiKey = function(u){
        if (typeof u.characters === 'undefined' || u.characters.length <= 0){
            return false;
        }

        var hasKey = false;
        angular.forEach(u.characters, function(c){
            if (c.has_key){ hasKey = true; }
        });

        return hasKey;
    };


}]);