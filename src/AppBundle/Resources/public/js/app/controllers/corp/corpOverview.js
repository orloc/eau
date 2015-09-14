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


            $('svg').remove();

            var wallets, balances;
            var margins = {
                top: 10,
                right: 50,
                bottom: 20,
                left: 50
            };
            var height = 900 - margins.top - margins.bottom;


            var vis = d3.select('.graphs').append('svg')
                .attr('width', "100%")
                .attr('height', height+margins.top+margins.bottom)
                .append("g")
                .attr("transform", "translate("+ margins.bottom+"," + margins.top +")");


            d3.json(Routing.generate('api.corporation.account_data', { id: $scope.selected_corp.id }), function(data){
                var parse = d3.time.format("%Y-%m-%dT%H:%M:%LZ").parse;
                var color = d3.scale.category10();

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

                var g = vis.selectAll("g")
                    .data(wallets)
                    .enter().append("g")
                    .attr("class", "wallet");

                lines();

                function lines(){
                    // begin lines
                    var width = $('.graphs')[0].clientWidth - margins.left;
                    var xScale = d3.time.scale().range([0,  width - margins.right]);
                    var yScale = d3.scale.linear().range([ height / 7 - 30, 0]);

                    var line = d3.svg.line()
                        .interpolate('basis')
                        .x(function(d){ return xScale(d.date); })
                        .y(function(d){ return yScale(d.balance); });

                    xScale.domain([
                        d3.min(wallets, function(d){ return d.values[0].date; }),
                        d3.max(wallets, function(d){ return d.values[d.values.length - 1].date; })
                    ]);

                    var g = vis.selectAll('.wallet')
                        .attr("transform", function(d, i){
                            return "translate(0,"+(i * height/7+10)+")";
                        });

                    g.each(function(d){
                        var e = d3.select(this);

                        e.append("path").attr("class", "line");

                        e.append("circle")
                            .attr("r", 10)
                            .style("fill", function(d) { return color(d.key); })
                            .style("stroke", "#000")
                            .style("stroke-width", "2px");

                        e.append("text")
                            .attr("x", 12)
                            .attr("dy", ".31em")
                            .text(d.key);
                    });

                    function draw(k){
                        g.each(function(d){
                            var e = d3.select(this);
                            yScale.domain([0, d.maxPrice]);

                            e.select("path")
                                .attr("d", function(d) { return line(d.values.slice(0, k+1)); });

                            e.selectAll("circle, text")
                                .data(function(d){
                                    return [d.values[k], d.values[k]];

                                }).attr("transform", function(d){
                                    return "translate(" + xScale(d.date) +"," + yScale(d.balance)+")";
                                });


                        });

                    }

                    var k = 1, n  = wallets[0].values.length;

                    d3.timer(function(){
                        draw(k);
                        if ((k +=2) >= n - 1){
                            draw(n-1);
                            return true;
                        }
                    });
                }
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
