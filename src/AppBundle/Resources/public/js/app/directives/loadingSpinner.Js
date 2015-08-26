'use strict';

angular.module('eveTool')
    .directive('loading-spinner', [ function(){
        return {
            restrict: 'E',
            scope: {},
            templateUrl : Routing.generate('template.loading.spinner')
        };
    }]);
