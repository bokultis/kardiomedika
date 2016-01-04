hDashboard.Manager.createNamespace('hDashboard.Widget.Cms.GoogleDashboardCountries');

hDashboard.Widget.Cms.GoogleDashboardCountries = function(settings){
    hDashboard.Widget.Base.apply(this,arguments);
    this.chart = null;
    this.title = "GA-Countries";
}

hDashboard.Widget.Cms.GoogleDashboardCountries.inherits(hDashboard.Widget.Base);

hDashboard.Widget.Cms.GoogleDashboardCountries.options = {
    name: 'GA-Countries',
    icon: 'GoogleDashboard.png'
}

hDashboard.Widget.Cms.GoogleDashboardCountries.prototype.internalRender = function(){
    var self = this;
    this.domObject.html("Loading...");
    jQuery.get("/" + CURR_LANG + "/cms/google-dashboard-countries/widget",function(result){
        self.domObject.html(result);
        
    });
}


