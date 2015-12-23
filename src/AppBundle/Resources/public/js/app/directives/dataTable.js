'use strict';

angular.module('eveTool')
    .directive('eveDataTable', ['$filter', function($filter){
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
                    var val = _.get(row, header.field_name, 'N/A');

                    if (typeof header.filter !== 'undefined' ){
                        if (header.filter === 'currency'){
                            val = $filter(header.filter)(val, 'ISK');
                        } else {
                            val = $filter(header.filter)(val);
                        }
                    }

                    return val;
                };
            },
            link : function(scope, element, attributes) {
                scope.orderBy = scope.tableHeaders[0].field_name;
            },
            templateUrl: Routing.generate('template.datatable')
        };
    }]);
