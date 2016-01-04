hDashboard.Manager.createNamespace('hDashboard.Widget.Cms.Statistics');

hDashboard.Widget.Cms.Statistics = function(settings){
    hDashboard.Widget.Base.apply(this,arguments);
    this.chart = null;
    this.title = "Statistics";
}

hDashboard.Widget.Cms.Statistics.inherits(hDashboard.Widget.Base);

hDashboard.Widget.Cms.Statistics.options = {
    name: 'Statistics',
    icon: 'statistics.png'
}

hDashboard.Widget.Cms.Statistics.prototype.internalRender = function(){
    var self = this;
    this.domObject.html("Loading...");
    jQuery.get("/" + CURR_LANG + "/cms/statistics/widget",function(result){
        self.domObject.html(result);

        //render sparklines
        $(".sparkLine").peity("line");

        
        var dataValues = [];
        var dataDates = [];
        if(!statsData){
            return;
        }
        for (var currDate in statsData){
            var date = new Date(parseInt(currDate) * 1000);
            dataDates.push(date.getDate() + '.' + (date.getMonth() + 1));
            dataValues.push(parseInt(statsData[currDate]['ga:visits']));
        }
        
        //render chart
        self.chart = new Highcharts.Chart({
            chart: {
                renderTo: "widgetStatsChart",
                defaultSeriesType: "area"
            },
            plotOptions: {
                series: {
                    color: "#1875d3",
                    //fillColor: "#268ccd",
                    fillOpacity: 0.1,
                    lineWidth: 2,
                    marker: {
                        radius: 4,
                        lineWidth: 1,
                        lineColor: "#FFFFFF" // inherit from series
                    }
                }
            },
            series: [{
                name: "Pageviews",
                data: dataValues
            }],
            yAxis: {
                title: {
                    text: null
                },
                labels: {
                    x: 15,
                    y: 15,
                    style: {
                        color: "#999999",
                        fontWeight: "bold",
                        fontSize: "10px"
                    }
                },
                gridLineColor: "#e7e7e7"
            },
            xAxis: {
                gridLineColor: "#e7e7e7",
                labels: {
                    x: 15,
                    y: -5,
                    style: {
                        color: "#268ccd",
                        fontSize: "10px"
                    }
                },
                categories: dataDates
            },
            tooltip: {
                formatter: function() {
                    return "Visits: " + this.y;
                },
                borderColor: "#333333"
            },
            credits: false,
            title: false,
            legend: false
        });
    });
}


