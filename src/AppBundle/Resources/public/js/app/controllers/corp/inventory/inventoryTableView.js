'use strict';

angular.module('eveTool')
    .controller('inventoryTableViewController', ['$scope', 'corporationDataManager', function($scope, corporationDataManager){
        $scope.predicate = 'total_price';
        $scope.reverse = true;
        $scope.max_size = 10;
        $scope.per_page = 10;
        $scope.page = 1;
        $scope.total_items = 0;
        $scope.assets = [];

        $scope.per_page_selection = [
            { label: '10', value: 10},
            { label: '15', value: 15},
            { label: '25', value: 25},
            { label: '50', value: 50},
            { label: '100', value: 100}
        ];

        $scope.tableHeaders = [
            {
                name: 'Name', sortable: true,
                has_image: true, field_name: 'descriptors.name'
            },
            {
                name: '#', sortable: true, filter: 'number',
                field_name: 'quantity'
            },
            {
                name: 'm3', sortable: true, filter: 'number',
                field_name: 'total_m3'
            },
            {
                name: 'Region', sortable: true,
                field_name: 'descriptors.region'
            },
            {
                name: 'Constellation', sortable: true,
                field_name: 'descriptors.constellation'
            },
            {
                name: 'System', sortable: true,
                field_name: 'descriptors.system'
            },
            {
                name: 'Station', sortable: true,
                field_name: 'descriptors.stationName'
            },
            {
                name: 'Avg Price', sortable: true, filter: 'currency',
                field_name: 'descriptors.price'
            },
            {
                name: 'Total Price', sortable: true, filter: 'currency',
                field_name: 'descriptors.total_price'
            }
        ];

        function updateInventory(){
            $scope.assets = [];
            $scope.$parent.loading = true;
            return corporationDataManager.getCorpInventory($scope.selected_corp, $scope.page, $scope.per_page).then(function(data){
                var outerItems = data.items;
                var items = [];

                angular.forEach(outerItems.items, function(item, k){
                    var i = item;
                    i.total_m3 = getM3(item);
                    items.push(i);
                });

                $scope.assets = items;
                $scope.per_page = data.num_items_per_page;
                $scope.page = data.current_page_number;
                $scope.$parent.loading = false;
                $scope.total_items = data.total_count;

                console.log(data.total_count);
                $scope.$parent.total_price = data.items.total_price;

                return data;
            });
        }

        var getM3 = function(item){
            if (item && typeof item.descriptors != 'undefined' && typeof item.descriptors.volume !== 'undefined')
                return parseFloat(item.descriptors.volume) * item.quantity;
        };


        $scope.$on('view_changed', function(event, val ){
            if (val === 'all'){
                updateInventory();
            }
        });

        $scope.pageChanged = function(){
            if (!$scope.$parent.loading) {
                updateInventory();
            }
        };

        $scope.$watch('per_page', function(){
            if (!$scope.$parent.loading) {
                updateInventory();
            }
        });

        $scope.order = function(predicate){
            $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
            $scope.predicate = predicate;
        };
    }]);
