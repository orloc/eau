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
                    updateSVG();
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
            var margins = {
                top: 10,
                right: 5,
                bottom: 20,
                left: 5
            };

            var height = 100 - margins.top ;
            var width = $('.graphs')[0].clientWidth - margins.left;

            var color = d3.scale.category10();

            var xScale = d3.time.scale().range([0,  width - margins.right]);
            var yScale = d3.scale.linear().range([ height - 10, 0]);

            var xAxis = d3.svg.axis()
                .scale(xScale)
                .ticks(d3.time.hour, 3)
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
                      .key(function(d) { return d.division; })
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

                svgWallets.append("text")
                    .datum(function(d){
                        return { name: d.name, value:d.values[d.values.length -1]};
                    }).attr("transform", function(d){ return "translate("+ xScale(d.value.date) +","+ yScale(d.value.y)+")"; })
                    .attr("x", -6)
                    .attr("dy", ".35em")
                    .text(function(d){ return d.name});

                vis.append("g")
                    .attr("class", "x-axis")
                    .attr("transform", "translate(0,"+height+")")
                    .call(xAxis);


            });

            function stackedArea(data){
            }
        }

    }]);
