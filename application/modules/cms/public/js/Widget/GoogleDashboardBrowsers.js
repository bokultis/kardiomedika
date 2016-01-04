hDashboard.Manager.createNamespace('hDashboard.Widget.Cms.GoogleDashboardBrowsers');

hDashboard.Widget.Cms.GoogleDashboardBrowsers = function(settings){
    hDashboard.Widget.Base.apply(this,arguments);
    this.chart = null;
    this.title = "GA-Top Browsers";
};

hDashboard.Widget.Cms.GoogleDashboardBrowsers.inherits(hDashboard.Widget.Base);

hDashboard.Widget.Cms.GoogleDashboardBrowsers.options = {
    name: 'GA-Top Browsers',
    icon: 'GoogleDashboard.png'
};

hDashboard.Widget.Cms.GoogleDashboardBrowsers.prototype.internalRender = function(){
    var self = this;
    this.domObject.html("Loading...");
    jQuery.get("/" + CURR_LANG + "/cms/google-dashboard-browsers/widget",function(result){
        self.domObject.html(result);
    });
};
