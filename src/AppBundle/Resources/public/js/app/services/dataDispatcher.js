'use strict';

angular.module('eveTool')
    .service('dataDispatcher', ['$rootScope', function($rootScope){

        var events = [];

        $rootScope.$watch(function(){
            return events;
        }, function(val){
            angular.forEach(val, function(v){
                $rootScope.$broadcast(v.name, v.payload);
            });
        }, true);

        return {
            addEvent: function(name, payload){
                events.push({name: name, payload: payload});
            }
        };

    }]);
