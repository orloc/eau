'use strict';

angular.module('eveTool')
    .controller('buyBackConfigController', ['$scope', '$http', function($scope, $http){

        $scope.configuration = getConfig();
        $scope.existing_configurations = [];
        $scope.regions = [];
        $scope.corporations = [];
        $scope.item_list = [];
        $scope.edit_id = null;

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
                if (ids.length > 1){
                    angular.forEach(ids, function(id){
                        names += getRegion(id).regionName+", ";
                    });
                } else {
                    names = getRegion(ids[0]).regionName;
                }
            }

            return names;
        };


        $scope.getType = function(type){
            return parseInt(type) === 1
             ? 'Global'
             : 'Specific';
        };

        $scope.getDetail = function(item){

            if ($scope.item_list.length > 0){
                if (item.type == 2){
                    var foundItem = _.find($scope.item_list, function(d){
                        return parseInt(d.typeID) === item.single_item;
                    });

                    return  foundItem.typeName;
                } else {
                    return item.base_markdown;
                }
            }
        };

        $scope.toggleEdit = function(item){
            $scope.edit_id = item.id;

        };

        $scope.update = function(item){
            $http.patch(Routing.generate('api.buyback_configuration.patch', { id: item.id }), item).then(function(data){
                updateBuyback();
            });
        };

        $scope.closeEdit = function() {
            $scope.edit_id = null;
            updateBuyback();
        };

    }]);
