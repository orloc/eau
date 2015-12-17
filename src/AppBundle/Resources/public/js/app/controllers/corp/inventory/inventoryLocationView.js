'use strict';

angular.module('eveTool')
    .controller('inventoryLocationViewController', ['$scope', 'corporationDataManager', function($scope, corporationDataManager){
        $scope.open_items = [];
        $scope.locations = [];
        $scope.loading = true;

        $scope.openedLocation = function(loc){
            if (loc.assets === null){
                corporationDataManager.getCorpLocationAssets($scope.selected_corp, loc.id).then(function(data){
                    loc.assets = data;
                });
            }
        };

        $scope.openContents = function (item){
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

        $scope.getHeadingName = function(loc){
            return (loc.name !== null && loc.name.length > 0) ? loc.name : 'Unknown Location';
        };

        $scope.$on('view_changed', function(event, val){
            if (val === 1){
                $scope.loading = true;
                corporationDataManager.getCorpInventorySorted($scope.selected_corp, $scope.translateView(val)).then(function(data){
                    $scope.locations = data;
                    angular.forEach($scope.locations, function(d, k){
                        $scope.locations[k].assets = null;
                    });
                    $scope.loading = false;
                });
            }
        });
    }]);
