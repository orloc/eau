'use strict';

angular.module('eveTool')
    .controller('inventorySummaryViewController', ['$scope', 'corporationDataManager', function($scope, corporationDataManager ){
        $scope.items = [];
        $scope.image_width = 32;
        $scope.tableHeaders = [
            {
                name: 'Name', sortable: true,
                has_image: true, field_name: 'descriptors.name'
            },
            {
                name: 'Quantity', sortable: true, is_number: true,
                has_image: false, field_name: 'asset_count'
            },
            {
                name: 'Location', sortable: true,
                has_image: false, field_name: 'descriptors.stationName'
            }
        ];

        $scope.$on('view_changed', function(event, val){
            if (val === 3){
                if ($scope.items.length === 0){
                    $scope.loading = true;
                    corporationDataManager.getCorpInventorySummary($scope.selected_corp).then(function(data){
                        var newData = [];
                        angular.forEach(data, function(d){
                            d.location_summary = getLocation(d.descriptors);
                            newData.push(d);
                        });
                        $scope.items = newData;
                        $scope.loading = false;
                    });
                }
            }
        });

        var getLocation = function(desc){
            if (typeof desc === 'undefined'){
                return;
            }

            var location = [
                desc.region,
                desc.system,
                function(desc){
                    return desc.stationName === null
                        ? 'IN SPACE' : desc.stationName;
                }(desc)
            ];

            return location.join(' - ');
        };
    }]);
