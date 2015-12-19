'use strict';

angular.module('eveTool')
    .controller('inventorySummaryViewController', ['$scope', 'corporationDataManager', function($scope, corporationDataManager ){
        $scope.items = [];
        $scope.loading = false;
        $scope.image_width = 16;

        $scope.$on('view_changed', function(event, val){
            if (val === 3){
                $scope.loading = true;
                corporationDataManager.getCorpInventorySummary($scope.selected_corp).then(function(data){
                    $scope.items = data;
                    $scope.loading = false;
                });
            }
        });

        $scope.getLocation = function(desc){
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
