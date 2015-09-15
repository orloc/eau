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

        function updateSVG(){
            $('svg').remove();
            var wallets, balances;

            var margins = {
                top: 5,
                right: 20,
                bottom: 10,
                left: 10
            };

            var height = 100 - margins.top - margins.bottom;

            var vis = d3.select('.graphs').append('svg')
                .attr('width', "100%")
                .attr('height', height+margins.top+margins.bottom)
                .append("g")
                .attr("transform", "translate("+ margins.bottom+"," + margins.top +")");

            var color = d3.scale.category10();
            var width = $('.graphs')[0].clientWidth - margins.left;
            var xScale = d3.time.scale().range([0,  width - margins.right]);
            var yScale = d3.scale.linear().range([ height / 7 - 10, 0]);

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
                .offset("zero")
                .values(function(d){ return d.values; })
                .x(function(d){ return d.date; })
                .y(function(d){ return d.balance; });

            var parse = d3.time.format("%Y-%m-%dT%H:%M:%LZ").parse;

            d3.json(Routing.generate('api.corporation.account_data', { id: $scope.selected_corp.id , date: moment($scope.current_date).format('X') }), function(data){

                wallets = d3.nest()
                    .key(function(d){ return d.division })
                    .entries(balances = data);


                wallets.forEach(function(w){
                    w.values.forEach(function(b){
                        b.date = parse(b.date);
                    });
                    /*
                    w.maxPrice = d3.max(w.values, function(b){ return b.balance; });
                    w.minPrice = d3.min(w.values, function(b){ return b.balance; });
                    */
                    w.values.sort(function(a, b){
                        return a.date - b.date;
                    });
                });

                stackedArea();

            });

            function stackedArea(){

                stack(wallets);

                yScale.domain([
                    0,
                    d3.max(wallets, function(b){
                        return d3.max(b.values, function(d){ return d.balance0 + d.balance; })
                    })
                ]).range([height, 0]);

                line.y(function(d){ return yScale(d.balance0); });

                area.y0(function(d){
                    return yScale(d.balance0);
                }).y1(function(d){
                    return yScale(d.balance0 + d.balance);
                });

                var g = vis.selectAll(".wallet")
                    .data(wallets)
                    .enter().append('g')
                    .attr('class', 'wallet');



                g.append("path")
                    .attr("class", "streamPath")
                    .attr("d", function (d) { console.log(area(d.values)); return area(d.values); })
                    .style("fill", function (d) { return color(d.key); })
                    .style("stroke", "grey");

            }

            function lines(){
                // begin lines

                xScale.domain([
                    d3.min(wallets, function(d){ return d.values[0].date; }),
                    d3.max(wallets, function(d){ return d.values[d.values.length - 1].date; })
                ]);

                var g = vis.selectAll('.wallet')
                    .attr("transform", function(d, i){
                        return "translate(0,"+(i * height / wallets.length +10)+")";
                    });

                /*
                 vis.append("g")
                 .attr("class", "axis")
                 .attr("transform", "translate(0," + (height - margins.bottom) +")")
                 .call(xAxis);
                 */

                g.each(function(d){
                    var e = d3.select(this);

                    e.append("path").attr("class", "line");

                    e.append("circle")
                        .attr("r", 5)
                        .style("fill", function(d) { return color(d.key); })
                        .style("stroke", "#000")
                        .style("stroke-width", "2px");

                    e.append("text")
                        .attr("x", 10)
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

                if (typeof wallets[0] == "object" && typeof wallets[0].values != 'undefined' && wallets[0].values.length > 5){
                    var k = 1, n  = wallets[0].values.length;

                    d3.timer(function(){
                        draw(k);
                        if ((k +=2) >= n - 1){
                            draw(n-1);
                            return true;
                        }
                    });
                }
            }
        }

    }]);
