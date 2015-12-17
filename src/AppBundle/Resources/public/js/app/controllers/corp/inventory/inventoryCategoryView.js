'use strict';

angular.module('eveTool')
    .controller('inventoryCategoryViewController', ['$scope', 'corporationDataManager', function($scope, corporationDataManager ){
        $scope.category_tree = [];
        $scope.open_items = [];
        $scope.loading = false;

        $scope.openCategory = function (item){
            if ($scope.isOpen(item) === true){
                delete $scope.open_items[item.id];
            } else {
                $scope.open_items[item.id] = item;
            }
        };

        $scope.isOpen = function(item){
            var ret =  typeof $scope.open_items[item.id] !== 'undefined';
            return ret;
        };

        $scope.$on('view_changed', function(event, val){
            console.log(val, 'categroy');
            if (val === 2){
                $scope.loading = true;
                corporationDataManager.getCorpInventorySorted($scope.selected_corp, $scope.translateView(val)).then(function(data){
                    $scope.category_tree = data;
                    $scope.loading = false;
                });
            }
        });

    }]);
