'use strict';

angular.module('eveTool')
    .controller('inventoryTableViewController', ['$scope', 'corporationDataManager', 'dataDispatcher', function($scope, corporationDataManager, dataDispatcher){
        $scope.predicate = 'total_price';
        $scope.reverse = true;
        $scope.max_size = 10;
        $scope.per_page = 10;
        $scope.page = 1;
        $scope.assets = [];
        $scope.total_items = 0;

        $scope.per_page_selection = [
            { label: '10', value: 10},
            { label: '15', value: 15},
            { label: '25', value: 25},
            { label: '50', value: 50},
            { label: '100', value: 100}
        ];

        function updateInventory(){
            $scope.assets = [];
            $scope.$parent.loading = true;
            return corporationDataManager.getCorpInventory($scope.selected_corp, $scope.page, $scope.per_page).then(function(data){
                var items = data.items;

                $scope.assets = items.items;
                $scope.per_page = data.num_items_per_page;
                $scope.page = data.current_page_number;
                $scope.$parent.loading = false;


                return data;
            });
        }

        $scope.$on('view_changed', function(event, val ){
            if (val === 'all'){
                if ($scope.assets.length === 0){
                    updateInventory().then(function(data){
                        $scope.total_items = data.total_count;
                        $scope.$parent.total_price = data.items.total_price;
                    });
                }
            }
        });

        $scope.pageChanged = function(){
            if (!$scope.loading) {
                updateInventory();
            }
        };

        $scope.$watch('per_page', function(){
            if (!$scope.loading) {
                updateInventory();
            }
        });


        $scope.getM3 = function(item){
            if (item && typeof item.descriptors != 'undefined' && typeof item.descriptors.volume !== 'undefined')
                return parseFloat(item.descriptors.volume) * item.quantity;
        };

        $scope.order = function(predicate){
            $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
            $scope.predicate = predicate;
        };
    }]);
