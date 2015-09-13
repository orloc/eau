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
        $scope.page = 'buy';

        $scope.current_date = moment().format('MM/DD/YY');

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
        });

        $scope.$watch('selected_corp', function(val){
            if (val === null || val === undefined){
                return;
            }

            $http.get(Routing.generate('api.corporation.account', { id: val.id })).then(function(data){
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


            var wallets, balances;

            var vis = d3.select('#account-graphs')
                    .attr('width', '100%')
                    .attr('height', '300px'),
                margins = {
                    top: 20,
                    right: 20,
                    bottom: 20,
                    left: 20
                };


            d3.json('http://eve.dev/app_dev.php/api/corporation/4/account/data', function(data){
                var parse = d3.time.format("%Y-%m-%dT%H:%M:%LZ").parse;


                wallets = d3.nest()
                    .key(function(d){ return d.division })
                    .entries(balances = data);

                wallets.forEach(function(w){
                    w.values.forEach(function(b){
                        b.date = parse(b.date);
                    });
                    w.maxPrice = d3.max(w.values, function(b){ return b.balance; });
                    w.minPrice = d3.min(w.values, function(b){ return b.balance; });

                    w.values.sort(function(a, b){
                        return a.date - b.date;
                    });
                });

                var getColor = function(w){
                    var colors = {
                        1000: "blue",
                        1001: "red",
                        1002: 'green',
                        1003: 'purple',
                        1004: 'brown',
                        1005: 'black',
                        1006: 'orange',
                        1007: 'pink'
                    };

                    return colors[w.key];
                };

                var width = $('#account-graphs')[0].clientWidth;
                var height = $('#account-graphs')[0].clientHeight;
                var xScale = d3.time.scale().range([0,  width - margins.right]);
                var yScale = d3.scale.linear().range([ height - margins.top, margins.bottom]);

                var line = d3.svg.line()
                    .interpolate('basis')
                    .x(function(d){ return xScale(d.date); })
                    .y(function(d){ return yScale(d.balance); });

                xScale.domain([
                    d3.min(wallets, function(d){ return d.values[0].date; }),
                    d3.max(wallets, function(d){ return d.values[d.values.length - 1].date; })
                ]);

                yScale.domain([
                    d3.min(wallets, function(w){ return w.minPrice; }),
                    d3.max(wallets, function(w){ return w.maxPrice; })
                ]);

                wallets.forEach(function(w){
                    vis.append('svg:path')
                        .attr('d', line(w.values))
                        .attr('stroke-width', 2)
                        .attr('stroke', getColor(w))
                        .attr('fill', 'none');

                });
            });
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
        };

        $scope.forward = function(){
            $scope.loading = true;
            $scope.buy_orders = [];
            $scope.sell_orders = [];
            $scope.current_date = moment($scope.current_date).add(1,'day').format('MM/DD/YY');
            updateData();
        };

        $scope.selectAccount = function(acc){

            if ($scope.selected_account === null
                || $scope.selected_account.id !== acc.id){
                $scope.loading = true;
                $scope.buy_orders = [];
                $scope.sell_orders = [];
                $scope.selected_account = acc;

                updateData();
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

    }]);
