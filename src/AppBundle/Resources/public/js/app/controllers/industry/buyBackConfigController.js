'use strict';

angular.module('eveTool')
    .controller('buyBackConfigController', ['$scope', '$http', function($scope, $http){

        $scope.configuration = {
            corporation: null,
            base_markdown: null,
            base_regions: [],
            overrides: []
        };

        $scope.search = {
            market_group : null
        };

        $scope.existing_configurations = [];

        $http.get(Routing.generate('api.regions')).then(function(data){
            $scope.regions = data.data;
        });

        $http.get(Routing.generate('api.corps')).then(function(data){
            $scope.corporations = data.data;
        });

        $http.get(Routing.generate('api.marketgroups')).then(function(data){
            $scope.market_groups = data.data;
        });

        $scope.addEmptyOverride = function(){
            $scope.configuration.overrides.push(getOverride());
        };

        var getOverride = function(){
            return {
                itemID: null,
                price: null,
                override: null,
                difference: null
            };
        };
    }]);
