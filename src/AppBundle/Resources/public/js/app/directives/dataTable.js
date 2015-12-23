'use strict';

angular.module('eveTool')
    .directive('eveDataTable', [ function(){
        return {
            restrict: 'E',
            scope: {
                tableHeaders: "=tableHeaders",
                tableData: "=tableData",
                image_width: "=imageWidth"


            },
            controller: function($scope) {
                $scope.isReverse = false;
                $scope.image_width = 32;

                $scope.getColumnValue = function(row, header){
                    return  _.get(row, header.field_name, 'N/A');
                };
            },
            link : function(scope, element, attributes) {
                scope.orderBy = scope.tableHeaders[0].field_name;

            },
            templateUrl: Routing.generate('template.datatable')
        };
    }]);
