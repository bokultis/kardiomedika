hDashboard.Manager.createNamespace('hDashboard.Widget.Admin');

hDashboard.Widget.Admin = function(settings){
    hDashboard.Widget.Base.apply(this,arguments);
    this.chart = null;
    this.title = translations_widgets.adminWidgetTitle;
}

hDashboard.Widget.Admin.inherits(hDashboard.Widget.Base);

hDashboard.Widget.Admin.options = {
    name: 'Misc',
    icon: 'admin.png'
}

hDashboard.Widget.Admin.prototype.internalRender = function(){
    var self = this;
    this.domObject.html("Loading...");
    jQuery.get("/" + CURR_LANG + "/admin/index/widget",function(result){
        self.domObject.html(result);
        self.chart = new Highcharts.Chart({
            chart: {
                renderTo: 'widgetAdminChart',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Disk Usage'
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
                        }
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Disk',
                    data: [
                        {
                            name: translations_widgets.adminWidgetFreeDisk,
                            y: hostingData.free,
                            sliced: true,
                            selected: true
                        },
                        {
                            name: translations_widgets.adminWidgetInUse,
                            y: hostingData.used,
                            sliced: true,
                            selected: true
                        }
                    ]
                }],
            credits: false,
            legend: false
        });
    });
}


