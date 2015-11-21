'use strict';

angular.module('eveTool')
.controller('userListController', ['$scope', '$http','dataDispatcher',  function($scope, $http, dataDispatcher){
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
        var weights = {
            ROLE_CORP_MEMBER : {
                name: 'Corp Member',
                weight: 0
            },
            ROLE_CEO : {
                name: 'CEO',
                weight: 1
            },
            ROLE_ALLIANCE_LEADER : {
                name: 'Alliance Leader',
                weight: 1
            },
            ROLE_ADMIN : {
                name: 'Admin',
                weight: 2
            },
            ROLE_SUPER_ADMIN : {
                name: 'Super Admin',
                weight: 3
            }
        };

       var topRole = null;

       for (var i = 0; i <= u.roles.length-1; i++) {
            var role = u.roles[i];

            if (topRole === null) {
                topRole = weights[role];
            } else if (weights[role].weight > topRole.weight){
                topRole = weights[role];
            }

       }

       return topRole !== null ? topRole.name : 'N/A';

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