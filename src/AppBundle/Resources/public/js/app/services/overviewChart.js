'use strict';

angular.module('eveTool')
    .service('overviewChart', [ function(){


        return {
            addEvent: function(name, payload){
                events.push({name: name, payload: payload});
            }
        };

    }]);
