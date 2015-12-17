'use strict';

angular.module('eveTool')
    .controller('inventoryCategoryViewController', ['$scope', 'corporationDataManager', 'selectedCorpManager', function($scope, corporationDataManager, selectedCorpManager){
        $scope.category_tree = [];
    }]);
