'use strict';

angular.module('eveTool')
    .controller('inventorySummaryViewController', ['$scope', 'corporationDataManager', function($scope, corporationDataManager ){
        $scope.items = [];
        $scope.image_width = 32;
        $scope.max_size = 10;
        $scope.per_page = 10;
        $scope.page = 1;
        $scope.total_items = 0;

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

        $scope.per_page_selection = [
            { label: '10', value: 10},
            { label: '15', value: 15},
            { label: '25', value: 25},
            { label: '50', value: 50},
            { label: '100', value: 100}
        ];

        function update() {
            $scope.$parent.loading = true;
            corporationDataManager.getCorpInventorySummary($scope.selected_corp, $scope.page, $scope.per_page).then(function(data){
                $scope.per_page = data.num_items_per_page;
                $scope.total_items = data.total_count;
                $scope.page = data.current_page_number;

                var items = data.items;
                var newData = [];
                angular.forEach(items, function(d){
                    d.location_summary = getLocation(d.descriptors);
                    newData.push(d);
                });
                $scope.items = newData;
                $scope.$parent.loading = false;
            });
        }

        $scope.$on('view_changed', function(event, val){
            if (val === 3){
                if ($scope.items.length === 0){
                    update();
                }
            }
        });

        $scope.pageChanged = function(){
            if (!$scope.$parent.loading){
                update();
            }
        };

        $scope.$watch('per_page', function(){
            if (!$scope.$parent.loading) {
                update();
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
