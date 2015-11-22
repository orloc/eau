'use strict';

angular.module('eveTool')
    .service('selectedCorpManager', [ function(){
        var selected_corporation = {};

        return {
            set: function(name){
                selected_corporation = name;
                return this;
            },
            get: function(){
                return selected_corporation;
            }
        };

    }]);
