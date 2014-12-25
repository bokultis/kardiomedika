hDashboard.Manager.createNamespace('hDashboard.Widget.Base');

hDashboard.Widget.Base = function(settings, className){
    this.settings = settings;
    this.domObject = null;
    this.className = className;
    this.title = 'Base Widget';
}

hDashboard.Widget.Base.options = {
    name: 'Base',
    icon: 'base.png'
}

hDashboard.Widget.Base.prototype.getClassName = function(){
    return this.className;
}

hDashboard.Widget.Base.prototype.getSettings = function(){
    return this.settings;
}

hDashboard.Widget.Base.prototype.render = function(domObject){
    this.domObject = domObject;
    this.internalRender();
}

hDashboard.Widget.Base.prototype.internalRender = function(){
    this.domObject.html("Base Widget");
}

hDashboard.Widget.Base.prototype.configure = function(){
    alert("Configuration not supported for this widget.")
}

hDashboard.Widget.Base.prototype.getTitle = function(){
    return this.title;
}