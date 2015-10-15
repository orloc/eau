'use strict';

angular.module('eveTool')
    .controller('buyBackConfigController', ['$scope', '$http', function($scope, $http){

        $scope.configuration = getConfig();
        $scope.existing_configurations = [];
        $scope.regions = [];
        $scope.corporations = [];
        $scope.item_list = [];
        $scope.edit_id = null;

        $scope.original_existing = [];

        function getConfig()  {
            return {
                corporation: null,
                base_regions: [],
                base_markdown: null,
                search_item: null,
                override_price: null,
                type: null
            };
        }


        var updateBuyback = function(){
            $scope.existing_configurations = [];
            $http.get(Routing.generate('api.buyback_configuration')).then(function(data){
                $scope.existing_configurations = data.data;
                $scope.original_existing = data.data;
            });
        };

        updateBuyback();

        $http.get(Routing.generate('api.regions')).then(function(data){
            $scope.regions = data.data;
        });

        $http.get(Routing.generate('api.corps')).then(function(data){
            $scope.corporations = data.data;
        });

        $http.get(Routing.generate('api.item_list')).then(function(data){
            $scope.item_list = data.data;
        });

        $scope.addOverride = function(){
            $http.post(Routing.generate('api.buyback_configuration.new'), $scope.configuration).then(function(data){
                $scope.existing_configurations.push(data.data);
                $scope.configuration = getConfig();
            });
        };

        $scope.getRegionNames = function(ids){
            var names = '';
            var getRegion = function(id){
                var r = _.find($scope.regions, function(d){
                    return d.regionID === id;
                });

                return r;
            };

            if ($scope.regions.length){
                if (typeof ids != 'undefined'){
                    if (ids.length > 1){
                        angular.forEach(ids, function(id){
                            names += getRegion(id).regionName+", ";
                        });
                    } else {
                        names = getRegion(ids[0]).regionName;
                    }
                }
            }

            return names;
        };


        $scope.getType = function(type){
            var t = parseInt(type);

            switch (t){
                case 1:
                    return 'Global';
                case 2:
                    return 'Specific';
                case 3:
                    return 'Base Price';
            }
        };

        $scope.getDetail = function(item){
            if ($scope.item_list.length > 0){
                if (item.type == 2){
                    var foundItem = _.find($scope.item_list, function(d){
                        return parseInt(d.typeID) === item.single_item;
                    });

                    return  foundItem.typeName;
                } else if(item.type == 1) {
                    return item.base_markdown;
                } else if (item.type == 3){
                    return $scope.getRegionNames(item.regions);
                }
            }
        };

        $scope.toggleEdit = function(item){
            if (item.type !== 3){
                $scope.edit_id = item.id;
            }
        };

        $scope.update = function(item){
            $http.patch(Routing.generate('api.buyback_configuration.patch', { id: item.id }), item).then(function(data){
                $scope.edit_id = null;
            });
        };

        $scope.delete = function(item){
            var confirm =window.confirm('Are you sure you wish to delete this item?');

            if (confirm){
                $http.delete(Routing.generate('api.buyback_configuration.delete', { id: item.id })).then(function(data){
                    item.deleted = true;
                });
            }
        };

        $scope.closeEdit = function() {
            $scope.existing_configurations = $scope.original_existing;
            $scope.edit_id = null;
        };

    }]);
