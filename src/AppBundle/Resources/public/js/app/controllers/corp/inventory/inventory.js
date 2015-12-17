'use strict';

angular.module('eveTool')
    .controller('inventoryController', ['$scope', 'corporationDataManager', 'selectedCorpManager', 'dataDispatcher', function($scope, corporationDataManager, selectedCorpManager, dataDispatcher){
        $scope.view_type = null;
        $scope.image_width = 32;
        $scope.total_price = 0;

        $scope.translateView = function(view){
            if (view === 0){
                return;
            }
            return view === 1 ? 'location' : 'category';
        };

        $scope.switchView = function (view){
            $scope.view_type = view;
            $scope.$broadcast('view_changed', $scope.view_type);
        };

        $scope.$on('total_update', function(event, payload){
            console.log(payload);
            $scope.total_price = payload;
        });

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }
            $scope.selected_corp = val;

            corporationDataManager.getLastUpdate(val, 2).then(function(data){
                $scope.updated_at = moment(data.created_at).format('x');
                $scope.update_succeeded = data.succeeded;
                $scope.next_update = moment(data.created_at).add(10, 'hours').format('x');
            }).then(function(){
                $scope.switchView('all');
            });
        });

        $scope.sumItems = function(){
            if (!$scope.price_reference.length){
                return 0;
            }
            var total = 0;
            angular.forEach($scope.assets, function(item){
                var price = $scope.getPrice(item);

                if (typeof price != 'undefined'){
                    total += price.average_price * item.quantity;
                }
            });
            return total;
        };
    }]);
