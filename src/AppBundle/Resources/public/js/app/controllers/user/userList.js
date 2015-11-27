'use strict';

angular.module('eveTool')
.controller('userListController', ['$scope', '$http','dataDispatcher', 'userRoleManager',  function($scope, $http, dataDispatcher, userRoleManager){
    $scope.users = [];

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
            var main = getMain(u.characters);

            u.main_id = main;
        });


        $scope.users = data.data;
    });

   $scope.getRole = function(u){
       return userRoleManager.getTopRole(u.roles);
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

    $scope.populateEdit = function(user){

        dataDispatcher.addEvent('update_user', user);
    };

    $scope.$on('update_list', function(event, item){
        $http.get(Routing.generate('api.users')).then(function(data){
            $scope.users = data.data;
        });

    });

}]);