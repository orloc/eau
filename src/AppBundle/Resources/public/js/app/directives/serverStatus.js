'use strict';

angular.module('eveTool')
    .directive('serverStatus', [ '$http', '$interval', function($http, $interval){
        return {
            restrict: 'A',
            link : function(scope, element, attributes) {
                $http.get(Routing.generate('api.server.status')).then(function(data){
                    scope.server_status = data.data;
                });

                var working = true;
                $interval(function(){
                    if (working) {
                        $http.get(Routing.generate('api.server.status')).then(function(data){
                            scope.server_status = data.data;
                        }, function(data){
                            if (data.data.code === 403){
                                window.location.replace(Routing.generate('eve.login.redirect',{}, true));
                            }
                        });
                    }
                }, 1000*60);
            },
            templateUrl: Routing.generate('template.serverstatus')
        };
    }]);
