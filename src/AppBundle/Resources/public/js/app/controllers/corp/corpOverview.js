'use strict';

angular.module('eveTool')
    .controller('corpOverviewController', ['$scope', '$http', function($scope, $http){


        $scope.selected_corp = null;
        $scope.selected_account = null;
        $scope.buy_orders = [];
        $scope.totalBalance = 0;
        $scope.grossProfit = 0;
        $scope.sell_orders = [];
        $scope.loading = false;
        $scope.show_graphs = true;
        $scope.page = 'buy';

        $scope.current_date = moment().format('MM/DD/YY');

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
        });

        $scope.$watch('selected_corp', function(val){
            if (val === null || val === undefined){
                return;
            }

            updateAccountBalances(val).then(function(){
                $scope.selectAccount($scope.accounts[0]);
            });


            $('svg').remove();

        });

        $scope.switchPage = function(page){
            $scope.page = page;
        };

        $scope.back = function(){
            $scope.loading = true;
            $scope.buy_orders = [];
            $scope.sell_orders = [];
            $scope.current_date = moment($scope.current_date).subtract(1,'day').format('MM/DD/YY');
            updateData();
            updateSVG();
        };

        $scope.forward = function(){
            $scope.loading = true;
            $scope.buy_orders = [];
            $scope.sell_orders = [];
            $scope.current_date = moment($scope.current_date).add(1,'day').format('MM/DD/YY');
            updateData();
            updateSVG();
        };

        $scope.selectAccount = function(acc){

            if ($scope.selected_account === null
                || $scope.selected_account.id !== acc.id){
                $scope.loading = true;
                $scope.buy_orders = [];
                $scope.sell_orders = [];
                $scope.selected_account = acc;

                updateData();
                updateSVG();

            }
        };

        $scope.sumOrders = function(orders){
            var sum = 0;

            angular.forEach(orders, function(o){
                sum+= o.price * o.quantity;
            });

            return sum;
        };

        $scope.findGross = function(){
            var buy = $scope.sumOrders($scope.buy_orders);
            var sell = $scope.sumOrders($scope.sell_orders);

            return sell - buy;
        };

        function updateData(acc){

            var date = moment($scope.current_date).format('X');

            $http.get(Routing.generate('api.corporation.account.markettransactions', { id: $scope.selected_corp.id, acc_id: $scope.selected_account.id, date: date})).then(function(data){
                $scope.buy_orders = data.data;

                $http.get(Routing.generate('api.corporation.account.markettransactions', { id: $scope.selected_corp.id, acc_id: $scope.selected_account.id, date: date, type: 'sell'})).then(function(data){
                    $scope.sell_orders = data.data;
                    $scope.loading = false;
                });
            });

        }

        function updateAccountBalances(val){
            return $http.get(Routing.generate('api.corporation.account', { id: val.id , date: $scope.current_date})).then(function(data){
                $scope.accounts = data.data;

                var total = 0;
                var lastDay = 0;
                angular.forEach($scope.accounts, function(a){
                    total += parseFloat(a.current_balance);
                    lastDay += parseFloat(a.last_day_balance);
                });

                $scope.totalBalance = total;
                $scope.percentChangeBalance = { percent: ((total - lastDay) / lastDay) * 100, diff: total - lastDay }
            });
        }

        function updateSVG(){
            $('svg').remove();
            var wallets, balances;

            var margins = {
                top: 5,
                right: 20,
                bottom: 10,
                left: 10
            };

            var height = 500 - margins.top - margins.bottom;


            var color = d3.scale.category10();
            var width = $('.graphs')[0].clientWidth - margins.left;
            var xScale = d3.time.scale().range([0,  width - margins.right]);
            var yScale = d3.scale.linear().range([ height - 10, 0]);

            var xAxis = d3.svg.axis()
                .scale(xScale)
                .orient("bottom");

            var yAxis = d3.svg.axis()
                .scale(yScale).ticks(4).orient('right');

            var line = d3.svg.line()
                .interpolate('basis')
                .x(function(d){ return xScale(d.date); })
                .y(function(d){ return yScale(d.balance); });

            var area = d3.svg.area()
                .interpolate("basis")
                .x(function (d) { return xScale(d.date); })
                .y0(function (d) { return yScale(d.balance); })
                .y1(function (d) { return yScale(d.balance0 + d.balance); });

            var stack = d3.layout.stack()
                .values(function(d){ return d.values; });

            var parse = d3.time.format("%Y-%m-%dT%H:%M:%LZ").parse;

            var vis = d3.select('.graphs').append('svg')
                .attr('width', "100%")
                .attr('height', height+margins.top+margins.bottom)
                .append("g")
                .attr("transform", "translate("+ margins.bottom+"," + margins.top +")");

            d3.json(Routing.generate('api.corporation.account_data', { id: $scope.selected_corp.id , date: moment($scope.current_date).format('X') }), function(data){

                color.domain(d3.keys(data[0]).filter(function(key){
                    return key !== 'date';
                }));

                data.forEach(function(w){
                    w.date = parse(w.date);
                });

                stackedArea(data);

            });

            function stackedArea(data){
                var wallets = stack(color.domain().map(function(name){
                    return {
                        name: name,
                        values: data.map(function(d){
                            return { date: d.date, y: d[name] / 100};
                        })
                    };
                }));

                xScale.domain(d3.extent(data, function(d){
                    return d.date;
                }));
            }
        }

    }]);
