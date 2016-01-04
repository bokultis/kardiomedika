hDashboard.Manager.createNamespace('hDashboard.Widget.Cms.GoogleDashboardSessions');

hDashboard.Widget.Cms.GoogleDashboardSessions = function(settings){
    hDashboard.Widget.Base.apply(this,arguments);
    this.chart = null;
    this.title = "GA-Sessions";
}

hDashboard.Widget.Cms.GoogleDashboardSessions.inherits(hDashboard.Widget.Base);

hDashboard.Widget.Cms.GoogleDashboardSessions.options = {
    name: 'GA-Sessions',
    icon: 'GoogleDashboard.png'
}

hDashboard.Widget.Cms.GoogleDashboardSessions.prototype.internalRender = function(){
    var self = this;
    this.domObject.html("Loading...");
    jQuery.get("/" + CURR_LANG + "/cms/google-dashboard-sessions/widget",function(result){
        self.domObject.html(result);
    });
}



