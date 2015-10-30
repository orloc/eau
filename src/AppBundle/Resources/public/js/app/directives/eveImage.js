'use strict';

angular.module('eveTool')
    .directive('eveImage', [ function(){
        return {
            restrict: 'E',
            scope: {
                imageType: "=imageType",
                object_id: "=objectId",
                imgWidth: "=imgWidth",
                overrideWidth: "=overrideWidth"
            },
            link : function(scope, element, attributes) {
                var baseUrl = 'https://imageserver.eveonline.com';
                var path = scope.object_id+'_'+scope.imgWidth;
                var ending = scope.imageType == 'Character' ? '.jpg' : '.png';
                scope.overrideWidth = typeof scope.overrideWidth === 'undefined'
                    ? '64px'
                    : scope.overrideWidth+'px';
                console.log(scope.overrideWidth);

                scope.url = [baseUrl,scope.imageType, path+ending].join('/');
            },

            template: "<img width='{{ overrideWidth }}' class='img img-responsive text-center img-circle' src='{{ url }}' ng-cloak/>"
        };
    }]);
