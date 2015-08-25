'use strict';

angular.module('eveTool')
    .animation('.menuSlide',[function($animate){
        return {
            enter: function(element, doneFn) {
                $(element).animate({
                    right: "0px"
                }, 300);

                $(element).animate({
                    left: "-350px"
                }, 300, doneFn);
            },
            leave: function(element, doneFn){
                $(element).animate({
                    right: "-350px"
                }, 300);

                $(element).animate({
                    left: "0px"
                }, 300, doneFn);

            }
        };

    }]);
