'use strict';

angular.module('eveTool')
    .directive('eveDataTable', [ function(){
        return {
            restrict: 'A',
            transclude: true,
            scope: {
                tableHeaders: "=tableHeaders"
            },
            link : function(scope, element, attributes) {
                console.log(scope.tableHeaders);
            },
            templateUrl: Routing.generate('template.data_table')
        };
    }]);
