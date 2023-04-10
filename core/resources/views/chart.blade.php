 <div id="container" class="chart bg-dark"></div>

    <script>Highcharts.getJSON('https://marketdata.tradermade.com/api/v1/timeseries?currency=EURUSD&api_key=FP6ocoI4miSQ3KUJmI7n&start_date=2021-01-01&end_date=2021-03-01&format=split', function (data) {

        // split the data set into ohlc and volume
        var ohlc = [],
          data = data["quotes"]["data"]
        dataLength = data.length,
          i = 0;
  
        for (i; i < dataLength; i += 1) {
          ohlc.push([
            Number(moment(data[i][0]).format('x')), // the date
            data[i][1], // open
            data[i][2], // high
            data[i][3], // low
            data[i][4] // close
          ]);
  
        }
  
        Highcharts.stockChart('container', {
          chart: {
            backgroundColor: "#212529",   // background color of entire chart
          },
          plotOptions: {
            series: {
              label: {
                connectorAllowed: false
              },
              pointStart: 2010
            }
          },
  
          yAxis: [{
  
            labels: {
  
              align: 'center',
              // rotation:30,
              overflow: 'left',
              style: {
                color: "#FFFFFF",      // color of labels of right
                cursor: "default",
                fontSize: "11px",
              }
            },
            showLastLabel: true,
            resize: {
              enabled: true
            }
          }],
          tooltip: {
            shape: 'square',
            headerShape: 'callout',
            borderWidth: 0,
            shadow: false,
            positioner: function (width, height, point) {
              var chart = this.chart,
                position;
  
              if (point.isHeader) {
                position = {
                  x: Math.max(
                    // Left side limit
                    chart.plotLeft,
                    Math.min(
                      point.plotX + chart.plotLeft - width / 2,
                      // Right side limit
                      chart.chartWidth - width - chart.marginRight
                    )
                  ),
                  y: point.plotY
                };
              } else {
                position = {
                  x: point.series.chart.plotLeft,
                  y: point.series.yAxis.top - chart.plotTop
                };
              }
  
              return position;
            },
            style: {
              color: "#FF0303",
              borderColor: "#00FF00"
            }
          },
          series: [{
            type: 'candlestick',
            id: 'EURUSD-ohlc',
            name: 'EURUSD Price',
            data: ohlc
  
            , color: "#F6465D",      // on down candle sticks
            lineColor: "#F6465D",
            fillColor: "#FFFFFF",   // on the bottom of the chart 
            negativeColor: "#FFFFFF",       // dont know what does it do
            negativeFillColor: "#FFFFFF",    // dont know what does it do
            upColor: "#0ECB81",      // on up candle sticks
            upLineColor: "#0ECB81"
          }],
          responsive: {
            rules: [{
              condition: {
                maxWidth: 800
              },
              chartOptions: {
                rangeSelector: {
                  inputEnabled: false
                }
              }
            }]
          }
        });
      });</script>
  