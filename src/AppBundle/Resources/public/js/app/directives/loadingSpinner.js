'use strict';

angular.module('eveTool')
    .directive('loadingSpinner', [ function(){
        return {
            restrict: 'E',
            scope: {},
            templateUrl : Routing.generate('template.loading.spinner')
        };
    }]);
