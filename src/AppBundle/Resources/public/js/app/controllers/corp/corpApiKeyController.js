'use strict';

angular.module('eveTool')
    .controller('corpApiKeyController', ['$scope', '$http','$filter', function($scope, $http, $filter){
        $scope.selected_corp = null;
        $scope.loading = true;

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
            $scope.loading = true;
            $scope.api_keys = [];
        });

        $scope.$watch('selected_corp', function(val){
            if (val === null || typeof val === 'undefined'){
                return;
            }

            $http.get(Routing.generate('api.corporation.apicredentials', { id: val.id})).then(function(data){
                return data.data;
            }).then(function(items){
                $scope.api_keys = items;
                $scope.loading = false;
            });

        });

        $scope.enable = function(api_key){
            angular.forEach($scope.api_keys, function(i){
                if (i.is_active){
                    alert('You already have an active key - please disable that key first then try and enable a new one');
                    return;
                }

                $http.patch(Routing.generate('api.corporation.apicredentials', { id: api_key.id, enable: true  })).then(function(data){
                    api_key.is_active = data.data.is_active;
                });
            });
        };

        $scope.disable = function(api_key){
            var response = window.confirm('WARNING: You are about to disable an API Key for ' + $scope.selected_corp.name + '. ARE YOU SURE YOU WISH TO PROCEED');

            if (response){
                $http.patch(Routing.generate('api.corporation.apicredentials', { id: api_key.id, delete: true  })).then(function(data){
                    api_key.is_active = data.data.is_active;
                });
            }
        };

    }]);
