'use strict';

angular.module('eveTool')
    .controller('marketOrderController', ['$scope', '$http','selectedCorpManager', function($scope, $http, selectedCorpManager){
        $scope.orders = [];
        $scope.image_width = 32;

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val) {
            if (typeof val === 'undefined' || typeof val.id === 'undefined') {
                return;
            }


            $scope.selected_corp = val;
            $scope.loading = true;
            $scope.total_escrow = 0;
            $scope.total_sales = 0;
            $scope.orders = [];

            $http.get(Routing.generate('api.corporation.marketorders', { id: val.id})).then(function(data){
                return data.data;
            }).then(function(items){
                $scope.orders = mapFillRatio(items.items);

                console.log($scope.orders);
                $scope.total_escrow = items.total_escrow;
                $scope.total_sales = items.total_on_market;
                $scope.loading = false;
            });

            $http.get(Routing.generate('api.corporation.apiupdate', { id: val.id, type: 2 })).then(function(data){
                var data = data.data;

                $scope.updated_at = moment(data.created_at).format('x');
                $scope.update_succeeded = data.succeeded;
                $scope.next_update = moment(data.created_at).add(10, 'hours').format('x');
            });
        });

        function mapFillRatio(items){
            var getFillRatio = function(item){
                return (item.volume_remaining / item.total_volume) * 100;
            };

            for(var i = 0; i <= items.length -1 ; i ++){
                items[i].ratio = getFillRatio(items[i]);
            }
            return items;
        }

        $scope.percentFinished = function(item){
            return  100 - ((item.volume_remaining / item.total_volume)  * 100);
        };

    }]);
