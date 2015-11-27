'use strict';

angular.module('eveTool')
    .service('userRoleManager', [ function() {
        var roles = [
            {
                role: 'ROLE_CORP_MEMBER',
                name:'Corp Member',
                weight: 0
            },
            {
                role: 'ROLE_DIRECTOR',
                name:'Director',
                weight: 1
            },
            {
                role: 'ROLE_CEO',
                name:'CEO',
                weight: 2
            },
            {
                role: 'ROLE_ALLIANCE_LEADER',
                name:'Alliance Leader',
                weight: 3
            }
        ];

        var privateRoles = [
            {
                role: 'ROLE_ADMIN',
                name:'Admin',
                weight: 4
            },
            {
                role: 'ROLE_SUPER_ADMIN',
                name:'Super Admin',
                weight: 5
            }
        ];

        function getTopRole(roles){
            var weights = getRoles(true, true);
            var topRole = null;

            for (var i = 0; i <= roles.length-1; i++) {
                var role = roles[i];

                if (topRole === null) {
                    topRole = weights[role];
                } else if (weights[role].weight > topRole.weight){
                    topRole = weights[role];
                }

            }

            return topRole !== null ? topRole.name : 'N/A';
        }

        function getRoles(asHash, allRoles){
            var newRoles = _.clone(roles);

            if(typeof allRoles !== 'undefined' && allRoles === true){
                for(var n = 0; n <= privateRoles.length - 1; n++){
                    newRoles.push(privateRoles[n]);
                }
            }

            if(typeof asHash !== 'undefined' && asHash === true){
                var tmpRoles = {};

                for(var i = 0; i <= newRoles.length - 1 ; i++){
                    tmpRoles[newRoles[i].role] = newRoles[i];
                }

                newRoles = tmpRoles;
            }

            return newRoles;
        }

        return {
            getRoles: function(asHash, allRoles){
                return getRoles(asHash, allRoles);
            },
            getTopRole: function(roles){
                return getTopRole(roles);
            }
        };
    }]);
