'use strict';

angular.module('eveTool')
    .controller('corpMembersController', ['$scope', '$http', 'selectedCorpManager', function($scope, $http, selectedCorpManager){
        $scope.selected_corp = null;
        $scope.image_width = 32;
        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val) {
            if (typeof val === 'undefined' || typeof val.id === 'undefined') {
                return;
            }
            $scope.selected_corp = val;

            var now = moment();

            $scope.getTimeWith = function(char){
                var time = moment(char.start_time);

                return  now.format('DDD')- time.format('DDD');

            };

            $scope.getAssocChars = function(m){
                var c = m.associated_chars;
                if (c.length === 0){
                    return 'None';
                }

                var str = [];
                angular.forEach(c, function(char){
                    str.push(char.name);
                });

                return str.join(', ');
            };

            $scope.hasApiKey = function(char){
                return typeof char.api_key !== 'undefined';
            };

            $http.get(Routing.generate('api.corporation.members', { id: val.id})).then(function(data){

                $scope.members = _.filter(data.data, function(d){
                    return typeof d.disbanded_at === 'undefined';
                });
            });
        });
    }]);
