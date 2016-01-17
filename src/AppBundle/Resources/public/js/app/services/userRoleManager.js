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

        function getUserId(){
            if (typeof current_user === 'undefined'){
                return false;
            }

            return current_user;
        }

        function getCurrentRoles(){
            if (typeof logged_in_user_roles === 'undefined'){
                return false;
            }

            var actualRoles = logged_in_user_roles;
            return mapRoles(actualRoles);
        }

        function mapRoles (actualRoles){
            var roles = getRoles(true, true);
            var mungedRoles = [];
            for(var i = 0; i <= actualRoles.length - 1; i++){
                var exists = _.find(roles, function(ar){
                    return ar.role === actualRoles[i];
                });

                if (typeof exists !== 'undefined'){
                    mungedRoles.push(exists);
                }
            }

            return mungedRoles;
        }

        function hasRole(role, roleList){
            var hasRole = false;
            var i = 0;

            while( hasRole === false && i <= roleList.length - 1 ){
                if (roleList[i].role === 'ROLE_SUPER_ADMIN'){
                    hasRole = true;
                    break;
                }

                if (role === roleList[i].role) {
                    hasRole = true;
                }

                i++;
            }

            return hasRole;
        }

        function getHighestFromMap(roleList) {
            var highestRole = null;
            for (var i = 0; i <= roleList.length - 1; i++) {
                if (highestRole === null || roleList[i].weight > highestRole.weight) {
                    highestRole = roleList[i];
                }

            }

            return highestRole;
        }

        function isGranted(role, roleList){
            // Super Admin > Admin > Alliance Leader > CEO > Director > Member
            if (typeof roleList === 'undefined' || roleList.length <= 0){ return false; }

            var roles = getRoles(false, true);
            var requested_role = _.find(roles, function(r){
                return r.role === role;
            });

            var  highestRole = getHighestFromMap(roleList);

            console.log(requested_role.weight <=  highestRole.weight);

            return requested_role.weight <= highestRole.weight;
        }

        return {
            getRoles: function(asHash, allRoles){
                return getRoles(asHash, allRoles);
            },
            getTopRole: function(roles){
                return getTopRole(roles);
            },
            getCurrentRoles : function(){
                return getCurrentRoles();
            },
            getUserId : function(){
                return getUserId();
            },
            mapRoles : function(roles){
                return mapRoles(roles);
            },
            isGranted: function(role, roleList) {
                return isGranted(role, roleList);
            },
            getHighestFromMap: function(roleList){
                return getHighestFromMap(roleList);
            },
            hasRole : function(role, roleList){
                return hasRole(role, roleList);
            }
        };
    }]);
