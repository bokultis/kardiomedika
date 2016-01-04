hDashboard.Manager.createNamespace('hDashboard.Widget.Cms');

hDashboard.Widget.Cms = function(settings){
    hDashboard.Widget.Base.apply(this,arguments);
    this.chart = null;
    this.title = "CMS";
}

hDashboard.Widget.Cms.inherits(hDashboard.Widget.Base);

hDashboard.Widget.Cms.options = {
    name: 'CMS',
    icon: 'cms.png'
}

hDashboard.Widget.Cms.prototype.internalRender = function(){
    var self = this;
    this.domObject.html("Loading...");
    this.domObject.load("/" + CURR_LANG + "/cms/admin/widget");
}


