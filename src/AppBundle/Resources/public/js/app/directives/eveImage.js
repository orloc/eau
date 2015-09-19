'use strict';

angular.module('eveTool')
    .directive('eveImage', [ function(){
        return {
            restrict: 'E',
            scope: {
                imageType: "=imageType",
                object_id: "=objectId",
                imgWidth: "=imgWidth"
            },
            link : function(scope, element, attributes) {
                var baseUrl = 'https://imageserver.eveonline.com';
                var path = scope.object_id+'_'+scope.imgWidth+'.png';

                scope.url = [baseUrl,scope.imageType, path].join('/');
            },

            template: "<img class='img img-responsive' height='50px' width='50px' ng-if='url' src='{{ url }}' ng-cloak/>"
        };
    }]);
