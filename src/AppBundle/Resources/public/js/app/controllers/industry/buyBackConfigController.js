'use strict';

angular.module('eveTool')
    .controller('buyBackConfigController', ['$scope', '$http', function($scope, $http){

        $scope.configuration = {
            corporation: null,
            base_regions: [],
            base_markdown: null,
            search_item: null,
            override_price: null,
            type: null
        };

        $scope.existing_configurations = [];

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
            console.log($scope.configuration);
        };


    }]);
