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
                var path = scope.object_id+'_'+scope.imgWidth;
                var ending = scope.imageType == 'Character' ? '.jpg' : '.png';

                scope.url = [baseUrl,scope.imageType, path+ending].join('/');
            },

            template: "<img class='img img-responsive text-center img-circle' src='{{ url }}' ng-cloak/>"
        };
    }]);
