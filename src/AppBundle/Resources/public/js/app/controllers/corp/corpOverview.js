'use strict';

angular.module('eveTool')
    .controller('corpOverviewController', ['$scope', '$http', '$q', 'selectedCorpManager', function($scope, $http, $q, selectedCorpManager){
        $scope.selected_account = null;
        $scope.buy_orders = [];
        $scope.totalBalance = 0;
        $scope.grossProfit = 0;
        $scope.sell_orders = [];
        $scope.loading = false;
        $scope.page = 'buy';

        $scope.current_date = moment().format('MM/DD/YY');

        var refreshView = function(val){
            if (val === null || val === undefined){
                return;
            }

            $scope.loading = true;
            $('svg').remove();

            updateAccountBalances(val).then(function(){
                updateSVG();
                $scope.selectAccount($scope.accounts[0]);
            });

            $scope.loading = false;

        };

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }

            $scope.selected_corp = val;
            refreshView(val);

            $http.get(Routing.generate('api.corporation.apiupdate', { id: val.id, type: 1 })).then(function(data){
                var data = data.data;

                $scope.updated_at = moment(data.created_at).format('x');
                $scope.next_update = moment(data.created_at).add(10, 'minutes').format('x');
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
            updateAccountBalances($scope.selected_corp);

            var start = moment($scope.svg_start_date);

            if (start.diff($scope.current_date, 'days') == 5){
                updateSVG();
            }
        };

        $scope.forward = function(){
            $scope.loading = true;
            $scope.buy_orders = [];
            $scope.sell_orders = [];
            $scope.current_date = moment($scope.current_date).add(1,'day').format('MM/DD/YY');
            updateData();
            updateAccountBalances($scope.selected_corp);

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

        function updateData(draw){

            var date = moment($scope.current_date).format('X');

            $http.get(Routing.generate('api.corporation.account.markettransactions', { id: $scope.selected_corp.id, acc_id: $scope.selected_account.id, date: date})).then(function(data){
                $scope.buy_orders = data.data;

                $http.get(Routing.generate('api.corporation.account.markettransactions', { id: $scope.selected_corp.id, acc_id: $scope.selected_account.id, date: date, type: 'sell'})).then(function(data){
                    if (typeof draw !== 'undefined' && draw == true){
                        updateSVG();
                    }
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

            $scope.svg_start_date = $scope.current_date;
            var margins = {
                top: 10,
                right: 20,
                bottom: 15,
                left: 115
            };

            var height = 100 - margins.top ;
            var width = $('.graphs')[0].clientWidth - margins.right;

            var color = d3.scale.category10();

            var xScale = d3.time.scale().range([0,  width - margins.left]);
            var yScale = d3.scale.linear().range([ height, 0]);

            var xAxis = d3.svg.axis()
                .scale(xScale)
                .ticks(d3.time.hour, 12)
                .tickSize(-height)
                .orient("bottom");

            var area = d3.svg.area()
                .interpolate("basis")
                .x(function (d) { return xScale(d.date); })
                .y0(function (d) { return yScale(d.y0); })
                .y1(function (d) { return yScale(d.y0 + d.y); });

            var stack = d3.layout.stack()
                .values(function(d){ return d.values; });

            var parse = d3.time.format("%Y-%m-%dT%H:%M:%LZ").parse;

            var vis = d3.select('.graphs').append('svg')
                .attr('width', "100%")
                .attr('height', height+margins.top+margins.bottom)
                .append("g")
                .attr("transform", "translate("+ margins.bottom+"," + margins.top +")");

            d3.json(Routing.generate('api.corporation.account_data', { id: $scope.selected_corp.id , date: moment($scope.current_date).format('X') }), function(data){

                // Nest stock values by symbol.
                var wallets  = d3.nest()
                      .key(function(d) { return d.name; })
                      .entries(data);

                var cDomain = [];

                var maxTotal = 0;
                wallets.forEach(function(w) {
                    w.values.forEach(function(d) { d.date = parse(d.date); d.balance = +d.balance; });

                    maxTotal += d3.max(w.values, function(d) { return d.balance; });
                    cDomain.push(w.key);

                    w.values.sort(function(a,b){
                        return a.date - b.date;
                    });

                });

                var yAxis = d3.svg.axis()
                    .scale(yScale)
                    .tickSize(-width)
                    .ticks((maxTotal / 100000000) / 3)
                    .tickFormat(d3.format('$s'))
                    .orient("right");

                color.domain(cDomain);

                var wStack = stack(color.domain().map(function(name){
                    return {
                        name: name,
                        values: _.find(wallets, function(w){ return w.key == name; }).values.map(function(d){
                            return {
                                date: d.date,
                                y: d.balance
                            };
                        })
                    };
                }));

                xScale.domain(d3.extent(data, function(d){
                    return d.date;
                }));


                yScale.domain(d3.extent([0, maxTotal], function(d){
                    return d;
                }));

                var svgWallets = vis.selectAll('.wallet')
                    .data(wStack)
                    .enter().append("g")
                    .attr("class", "wallet");

                svgWallets.append("path")
                    .attr("class", "area")
                    .attr("d", function(d){ return area(d.values); })
                    .style("fill", function(d){ return color(d.name); });

                vis.append("g")
                    .attr("class", "x-axis")
                    .attr("transform", "translate(0,"+height+")")
                    .call(xAxis);

                vis.append("g")
                    .attr("class", "x-axis")
                    .call(yAxis);

                vis.append("circle")

                var legend = vis.selectAll(".legend")
                    .data(color.domain().slice().reverse())
                    .enter().append("g")
                    .attr("class", "legend")
                    .attr("transform", function (d, i) {
                        return "translate(0," + i * 15 + ")";
                    });

                legend.append("rect")
                    .attr("x", width - 18)
                    .attr("width", 10)
                    .attr("height", 10)
                    .style("fill", color);

                legend.append("text")
                    .attr("x", width - 24)
                    .attr("y", 9)
                    .attr("dy", ".35em")
                    .style("text-anchor", "end")
                    .text(function (d) {
                        return d;
                    });

            });

        }

    }]);
