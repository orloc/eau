'use strict';

angular.module('eveTool')
    .directive('slideButton', [ function(){
        return {
            restrict: 'E',
            scope: {
                imageType: "=imageType",
                imgWidth: "=imgWidth"
            },
            link : function(scope, element, attributes) {

                scope.url = [baseUrl,scope.imageType, path].join('/');
            },
            template: "<img class='img img-responsive' height='50px' width='50px' src='{{ url }}' ng-cloak/>"
        };
    }]);