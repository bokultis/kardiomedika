hDashboard.Manager.createNamespace('hDashboard.Widget.Auth');

hDashboard.Widget.Auth = function(settings){
    hDashboard.Widget.Base.apply(this,arguments);
    this.chart = null;
    this.title = translations_widgets.widgetAuthTitle;
}

hDashboard.Widget.Auth.inherits(hDashboard.Widget.Base);

hDashboard.Widget.Auth.options = {
    name: 'Permissions',
    icon: 'auth.png'
}

hDashboard.Widget.Auth.prototype.internalRender = function(){
    var self = this;
    this.domObject.html("Loading...");
    this.domObject.load("/" + CURR_LANG + "/auth/admin/widget");
}


