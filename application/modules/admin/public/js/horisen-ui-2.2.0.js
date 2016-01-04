/**
* HORISEN User Interface.
* @author   HORISEN
* @date     04.11.2013.
* @version  2.0.1
* @note     UI includes - widgets: naviBar, resTabs
*                       - plugins: collapsList, dropDown, Alert
*                       - common: doesExist, isAlphanumeric, hasVerticalScroll
*                       
* @date     09.06.2014.
* @version  2.1.0
* @note     Included a new widget: collapsTabs
*                       
* @date     24.10.2014.
* @version  2.2.0
* @note     Included a new widget: treeView
*/

/**
* TreeView Widget.
* @date     24.10.2014.
* @version  1.0.0
* @note     UI widget of Tree View.
*/
(function($, window) {
    $.widget("hui.treeview", {
        version: "1.0.0",

        options: {
            preloader: false,
            context_obj: false,
            tranistion: false
        },
        
        $treeView: false,
        treeViewId: "",
        treeId: "",
        
        _create: function() {
            var self = this;
            
            // if already created, return...
            if ($(this).attr("aria-describedby")){
                return false;
            }
            
            // set the element...
            this.$treeView = $(this.element);
            this.treeViewId = this.$treeView.attr("id") || this.widgetFullName + "-" + this.uuid;
            
            this.$treeView.attr({
                "id": this.treeViewId,
                "aria-describedby": this.treeViewId,
                "aria-orientation": this.orientation
            });
            
            if (this.options.height && this.options.height > 0){
                this.$treeView.height(this.options.height);
            }
            
            if (this.options.preloader){
                var $preloader = this.setPreloader(false);
                this.$treeView.append($preloader);
            }

            // set tree items...
            this.treeId = this.treeViewId + "-items";
            this.$treeItems = this.$treeView.children("ul");
            
            if (this.$treeItems.doesExist())
                this._setTreeItems(this.$treeItems, true);
            
            // set transition class for effects
            if (this.options.tranistion){
                this.$treeView.addClass("transit");
                this.$treeItems.addClass("transit");
            }
            
            $(this).ready(function() {
                self._load();
            });
        },
        
        _setTreeItems: function($elm, ind){
            var self = this;
            var parentId = $elm.parent().attr("id");
            
            // set items by tree list...
            $elm
                .attr({
                    "id": parentId + "-items",
                    "aria-labelledby": parentId,
                    "role": (ind) ? "tree" : "group"
                })
                .children("li")
                .each(function(index){
                    var $treeItem = $(this);
                    
                    $treeItem
                        .attr({
                            "id": parentId + "-items-" + index,
                            "aria-labelledby": parentId + "-items",
                            "role": "treeitem"
                        });
                        
                    // if is item disabled, save and remove href from tag "a"
                    if ($treeItem.hasClass("disabled")){
                        var url = $treeItem.children("a").attr("href");
                        $treeItem.data("url", url);
                        $treeItem.children("a").removeAttr("href");
                    } else {
                        // set tree item...
                        $treeItem.bind('click', function(e) {
                            e.stopPropagation();
                            self._select(e, this);
                        });
                        
                        // check for sub navigation...
                        var $subTree = $treeItem.children("ul");

                        if ($subTree.length > 0){
                            // set an icon to toggle sub items
                            var toggleItemsIcon = $('<span class="toggle-items hui-icon small hui-right-full transit"></span>');
                            
                            $(toggleItemsIcon).bind('click', function(e) {
                                e.stopPropagation();
                                self._toggleItems(e, $(this).parent());
                            });
                            
                            // check for active children in the subTree
                            // if there is no active children, the parent item will be collapsed;
                            // otherwise, it will be expanded so active child will be visible
                            var hasActiveChildren = $subTree.find(".active");
                            if (hasActiveChildren.length)
                                $treeItem.addClass("expanded");
                            
                            $treeItem
                                .addClass("has-items")
                                .prepend(toggleItemsIcon);

                            self._setTreeItems($subTree, false);
                        }
                    }
                });
        },
        
        _load: function(e){
            this.$treeView.trigger("load");
            
            // callback...
            if (this.options.load){
                this.options.load($.Event("load"), {
                    id: this.treeId,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        _toggleTreeItems: function(e){
            if (this.$treeItems.height() > 0){
                this.$treeItems
                    .css('maxHeight', 0);
            
                // callback...
                if (this.options.collapsed){
                    this.options.collapsed(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
            } else {
                this.$treeItems
                    .css('maxHeight', this.maxHeight);
                    
                // callback...
                if (this.options.expanded){
                    this.options.expanded(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
            }
        },
        
        _select: function(e, elm){
            var $elm = $(elm);
            var id = $elm.attr("id");
            
            this._unselect(e);
            
            $elm.addClass("active")
                .trigger("nodeSelected");
                
            
            // callback...
            if (this.options.nodeSelected){
                this.options.nodeSelected(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        _unselect: function(e){
            var $elm = this.$treeView.find(".active").eq(0);
            var id = $elm.attr("id");
            
            $elm.removeClass("active")
                .trigger("nodeUnselected");
            
            // callback...
            if (this.options.nodeUnselected){
                this.options.nodeUnselected(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        _toggleItems: function(e, elm){
            if (!elm || typeof elm !== "object")
                return;
            
            var $elm = $(elm);
            
            if ($elm.hasClass("expanded")){
                this._collapse(e, $elm);
            } else {
                this._expand(e, $elm);
            }
        },
        
        _expand: function(e, $elm){
            var id = $elm.attr("id");
            
            $elm
                .addClass("expanded")
                .trigger("nodeExpanded");
            
            // callback...
            if (this.options.nodeExpanded){
                this.options.nodeExpanded(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
                
        _collapse: function(e, $elm){
            var id = $elm.attr("id");
            
            $elm
                .removeClass("expanded")
                .trigger("nodeCollapsed");
            
            // callback...
            if (this.options.nodeCollapsed){
                this.options.nodeCollapsed(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        getActiveId: function(){
            var treeItem = this.$treeView.find(".active").eq(0);
            return $(treeItem).attr("id");
        },
        
        addItems: function(id, items){
            if (!id || typeof id !== "string" || !items || typeof items !== "object"){
                return;
            }
            
            var self = this;
            var $parent = $("#" + id);
            
            var $treeList = $parent.children("ul");
            var treeListId = id + "-items";
            
            if (!$treeList.doesExist()){
                // it indicates is tree list belongs to the main container or a tree item
                var ind = ($parent.attr("aria-describedby") === id) ? true : false;
                
                $treeList = $('<ul></ul>');
                $treeList
                    .attr({
                        "id": treeListId,
                        "aria-labelledby": id,
                        "role": (ind) ? "tree" : "group"
                    });
                
                $parent.append($treeList);
            }
            
            
/*
 *                         // check for sub navigation...
                        var $subTree = $treeItem.children("ul");

                        if ($subTree.length > 0){
                            // set an icon to toggle sub items
                            var toggleItemsIcon = $('<span class="toggle-items hui-icon small hui-right-full transit"></span>');
                            
                            $(toggleItemsIcon).bind('click', function(e) {
                                e.stopPropagation();
                                self._toggleItems(e, $(this).parent());
                            });
                            
                            // check for active children in the subTree
                            // if there is no active children, the parent item will be collapsed;
                            // otherwise, it will be expanded so active child will be visible
                            var hasActiveChildren = $subTree.find(".active");
                            if (hasActiveChildren.length)
                                $treeItem.addClass("expanded");
                            
                            $treeItem
                                .addClass("has-items")
                                .prepend(toggleItemsIcon);

                            self._setTreeItems($subTree, false);
                        }

 */            
            var $treeItem = false;
            var $toggleItemsIcon = false;
            var $preloader = false;

            // add tree items...
            $.each(items, function(index, item){
                $treeItem = $('<li><a class="item-label">' + item.label + '</a></li>');
                $treeItem
                    .attr({
                        'id': treeListId + "-" + index,
                        'aria-labelledby': treeListId,
                        'role': 'treeitem'
                    })
                    .bind('click', function(e){
                        e && e.stopPropagation();
                        self._select(e, this);
                    });
                    
                // render AJAX params if exist...
                if (item.ajax && typeof item.ajax === 'string' && item.ajax !== ''){
                    $toggleItemsIcon = self.setToggleItemsIcon();
                    $preloader = self.setPreloader(item.ajax);
                    
                    $treeItem
                        .addClass('has-items')
                        .prepend($toggleItemsIcon)
                        .append($preloader);
                }
                
                // render DATA params if exist...
                if (item.data && typeof item.data === 'object'){
                    $.each(item.data, function(key, val){
                        $treeItem.data(key, val);
                    });
                }
                
                $treeList.append($treeItem);
// OK                console.log(">>> " + item.label);
            });
            
            // remove preloader of the parent, if exist...
            $preloader = $parent.children('.preloader');
            if ($preloader.doesExist())
                $preloader.remove();
        },
        
        setToggleItemsIcon: function(){
            var self = this;
            var $toggleItemsIcon = $('<span class="toggle-items hui-icon small hui-right-full"></span>');

            $toggleItemsIcon.bind('click', function(e) {
                e.stopPropagation();
                self._toggleItems(e, $(this).parent());
            });
            
            return $toggleItemsIcon;
        },
        
        setPreloader: function(ajax){
            var $preloader = $('<div class="preloader"></div>');

            $preloader.data('ajax', ajax);
            
            return $preloader;
        },
        
        getPreloader: function(id){
            if (!id || typeof id !== "string")
                return;
            
            var $preloader = $("#" + id).children(".preloader");
            return ($preloader.doesExist() ? $preloader : false);
        },
        
        renameItem: function(id, txt){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist() || !txt || typeof txt != "string")
                return;
            
            $("#" + id).children("a").text(txt);
        },
        
        selectItem: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            this.$treeItems.find(".active").removeClass("active");
            $("#" + id)
                .addClass("active")
                .parents(".has-items.collapsed").each(function(){
                    $(this).removeClass("collapsed");
                });
        },
        
        unselectActiveItem: function(){
            var $elm = this.$treeItems.children(".active");
            $elm.removeClass("active");
            return $elm.attr("id");
        },
        
        removeItem: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            $("#" + id).remove();
        },
        
        removeSubItems: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            $("#" + id).children("span").remove();
            $("#" + id).children("ul").remove();
        },
        
        openSubItems: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var elm = $("#" + id);
            $(elm).removeClass("collapsed");
        },
        
        closeSubItems: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var elm = $("#" + id);
            $(elm).addClass("collapsed");
        },
        
        openAll: function(){
            this.$treeItems.find("li:has(ul)").removeClass("collapsed");
        },
        
        closeAll: function(){
            this.$treeItems.find("li:has(ul)").addClass("collapsed");
        },
                
        enableItem: function(id, url){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var self = this,
                $treeItem = $("#" + id);
            
            url = url || $treeItem.data("href");
            
            // 1st unselect active item
            this.$treeItems.find(".active").removeClass("active");
            
            // then enable the item...
            $treeItem
                .removeClass("disabled")
                .bind('click', function(e) {
                    e.stopPropagation();
                    self.selectItem(id);
                })
                .children("a")
                .addClass("active")
                .attr({
                    "href": url || "javascript:void(0)"
                })
                .end()
                .children("span")
                .bind('click', function(e) {
                    e.stopPropagation();
                    self._toggleSubNavi(this);
                });
        },
        
        disableItem: function(id, url){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var $treeItem = $("#" + id),
                url = $treeItem.children("a").attr("href");
            
            // disable the item...
            $treeItem
                .addClass("disabled")
                .data("url", url)
                .unbind('click')
                .children("a")
                .removeClass("active")
                .removeAttr("href")
                .end()
                .children("span")
                .unbind("click");
        
            if ($treeItem.has("ul")){
                $treeItem
                    .addClass("collapsed");
            }
        },
        
        _setOptions: function(options) {
    
        },

        _setOption: function(key, value) {
    
        }
    });

})(jQuery, this);


/**
* NaviBar Widget.
* @date     23.09.2013.
* @version  2.0.0
* @note     UI widget of Navigation Bar.
*/
(function($, window) {
    $.widget("hui.navibar", {
        version: "2.0.0",

        options: {
            context_obj: false,
            toggleSideBar: false,
            mediaWidth: 768,
            tranistion: false
        },
        
        $naviBar: false,
        naviBarId: "",
        treeId: "",
        maxHeight: 0,
        scrollWidth: 17,
        orientation: "vertical",
        
        _create: function() {
            var self = this;
            
            // if is it already created, go out...
            if ($(this).attr("aria-describedby")){
                return false;
            }
            
            if (($(window).width() + ($(window).hasVerticalScroll() ? this.scrollWidth : 0)) < this.options.mediaWidth){
                this.orientation = "horizontal";
            }
            
            // set the element...
            this.$naviBar = $(this.element);
            this.naviBarId = this.$naviBar.attr("id") || this.widgetFullName + "-" + this.uuid;
            
            this.$naviBar.attr({
                "id": this.naviBarId,
                "aria-describedby": this.naviBarId,
                "aria-orientation": this.orientation
            });
            
            $("body").attr({
                "aria-orientation": this.orientation,
                "aria-expanded": false
            });
            
            // set navi items...
            this.treeId = this.naviBarId + "-items";
            this.$treeItems = this.$naviBar.children("ul");
            this._setTreeItems(this.$treeItems, true);
            
            // calculate maxHeight due to responsive mode
            var $items = this.$treeItems.find("li");
            this.maxHeight = $items.length * $items.eq(0).outerHeight();
            
            // set transition class for effects
            if (this.options.tranistion){
                this.$naviBar.addClass("transit");
                this.$treeItems.addClass("transit");
            }
            
            // set icon bar when naviBar is shown on top
            var iconNaviBar = $("<span class='icon-navibar' data-icon='&#xe01f;'></span>");
            
            $(iconNaviBar).bind("click", function (e) {
                e.stopPropagation();
                self._toggleTreeItems(e);
            });

            this.$naviBar.prepend(iconNaviBar);
            
            // set toggle icon... 
            if (this.options.toggleSideBar){
                this.$toggleSideBar = $("<span class='toggle-sidebar' data-icon='&#xe02f;'></span>");
                this.$toggleSideBar.bind("click", function (e) {
                    e.stopPropagation();
                    self._toggleSideBar(e);
                });
                
                this.$naviBar.prepend(this.$toggleSideBar);
            }
            
            $(this).ready(function() {
                self._load();
            });
            
            $(window).bind("resize", function(e){
                e.stopPropagation();
                self._resize();
            });
        },
        
        _setTreeItems: function($elm, ind){
            var self = this;
            var parentId = $elm.parent().attr("id");
            
            // set items by navi list...
            $elm
                .attr({
                    "id": parentId + "-items",
                    "aria-labelledby": parentId,
                    "role": (ind) ? "tree" : "group"
                })
                .children("li")
                .each(function(index){
                    var $treeItem = $(this);
                    
                    $treeItem
                        .attr({
                            "id": parentId + "-items-" + index,
                            "aria-labelledby": parentId + "-items",
                            "role": "treeitem"
                        });
                        
                    // if is item disabled, save and remove href from tag "a"
                    if ($treeItem.hasClass("disabled")){
                        var url = $treeItem.children("a").attr("href");
                        $treeItem.data("url", url);
                        $treeItem.children("a").removeAttr("href");
                    } else {
                        // set tree item...
                        $treeItem.bind('click', function(e) {
                            e.stopPropagation();
                            self._select(e, this);
                        });
                        
                        // check for sub navigation...
                        var $subTree = $treeItem.children("ul");

                        if ($subTree.length > 0){
                            // set an icon to toggle sub items
                            var toggleItemsIcon = $('<span class="toggle-items transit" data-icon="&#xe00b"></span>');
                            
                            $(toggleItemsIcon).bind('click', function(e) {
                                e.stopPropagation();
                                self._toggleItems(e, $(this).parent());
                            });
                            
                            // check for active children in the subTree
                            // if there is no active children, the parent item will be collapsed;
                            // otherwise, it will be expanded so active child will be visible
                            var hasActiveChildren = $subTree.find(".active");
                            if (!hasActiveChildren.length)
                                $treeItem.addClass("collapsed");
                            
                            $treeItem
                                .addClass("has-items")
                                .prepend(toggleItemsIcon);

                            self._setTreeItems($subTree, false);
                        }
                    }
                });
        },
        
        _load: function(e){
            this.$naviBar.trigger("load");
            
            // callback...
            if (this.options.load){
                this.options.load($.Event("load"), {
                    id: this.treeId,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        _resize: function(e){
            if (!this.$naviBar.hasClass("collapsible"))
                return false;
            
            // check orientation and width of the screen
            this.orientation = this.$naviBar.attr("aria-orientation");
            
            if (($(window).width() + ($(window).hasVerticalScroll() ? this.scrollWidth : 0)) < this.options.mediaWidth){
                if (this.orientation != "horizontal"){
                    this.orientation = "horizontal";
                    this.$treeItems.css('maxHeight', 0);
                    this.$naviBar.attr("aria-orientation", this.orientation);
                    
                    $("body").attr({
                        "aria-orientation": this.orientation
                    });
                }
            } else {
                if (this.orientation != "vertical"){
                    this.orientation = "vertical";
                    this.$treeItems.css('maxHeight', this.maxHeight);
                    this.$naviBar.attr("aria-orientation", this.orientation);
                    
                    $("body").attr({
                        "aria-orientation": this.orientation
                    });
                
                    if (!this.$naviBar.hasClass("collapsed"))
                        this.$naviBar.trigger("expanded");
                }
            }
        },
                
        _toggleTreeItems: function(e){
            if (this.$treeItems.height() > 0){
                this.$treeItems
                    .css('maxHeight', 0);
            
                // callback...
                if (this.options.collapsed){
                    this.options.collapsed(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
            } else {
                this.$treeItems
                    .css('maxHeight', this.maxHeight);
                    
                // callback...
                if (this.options.expanded){
                    this.options.expanded(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
            }
        },
        
        _toggleSideBar: function(e){
            if (!this.$naviBar.hasClass("collapsible"))
                return false;
            
            if (this.$naviBar.hasClass("collapsed")){
                this.$naviBar
                    .removeClass("collapsed")
                    .trigger("expanded");
            
                $("body").attr({
                    "aria-expanded": false
                });
                    
                // callback...
                if (this.options.expanded){
                    this.options.expanded(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
            } else {
                this.$naviBar
                    .addClass("collapsed")
                    .trigger("collapsed");
            
                $("body").attr({
                    "aria-expanded": true
                });
                
                // callback...
                if (this.options.collapsed){
                    this.options.collapsed(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
            }
        },
        
        _select: function(e, elm){
            var $elm = $(elm),
                id = $elm.attr("id");
            
            this._unselect(e);
            
            $elm
                .trigger("nodeSelected")
                .addClass("active");
            
            // callback...
            if (this.options.nodeSelected){
                this.options.nodeSelected(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        _unselect: function(e){
            var $elm = this.$treeItems.find(".active").eq(0),
                id = $elm.parent().attr("id");
            
            $elm.removeClass("active");
            
            $elm
                .parent()
                .trigger("nodeUnselected");
            
            // callback...
            if (this.options.nodeUnselected){
                this.options.nodeUnselected(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        _toggleItems: function(e, elm){
            if (!elm || typeof elm != "object")
                return;
            
            var $elm = $(elm);
            
            if ($elm.hasClass("collapsed")){
                this._expand(e, $elm);
            } else {
                this._collapse(e, $elm);
            }
        },
        
        _expand: function(e, $elm){
            var id = $elm.attr("id");
            
            $elm
                .removeClass("collapsed")
                .trigger("nodeExpanded");
            
            // callback...
            if (this.options.nodeExpanded){
                this.options.nodeExpanded(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
                
        _collapse: function(e, $elm){
            var id = $elm.attr("id");
            
            $elm
                .addClass("collapsed")
                .trigger("nodeCollapsed");
            
            // callback...
            if (this.options.nodeCollapsed){
                this.options.nodeCollapsed(e, {
                    id: id,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        getActiveId: function(){
            var naviItem = this.$treeItems.find(".active").eq(0);
            return $(naviItem).attr("id");
        },
        
        renameItem: function(id, txt){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist() || !txt || typeof txt != "string")
                return;
            
            $("#" + id).children("a").text(txt);
        },
        
        selectItem: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            this.$treeItems.find(".active").removeClass("active");
            $("#" + id)
                .addClass("active")
                .parents(".has-items.collapsed").each(function(){
                    $(this).removeClass("collapsed");
                });
        },
        
        unselectActiveItem: function(){
            var $elm = this.$treeItems.children(".active");
            $elm.removeClass("active");
            return $elm.attr("id");
        },
        
        removeItem: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            $("#" + id).remove();
        },
        
        removeSubItems: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            $("#" + id).children("span").remove();
            $("#" + id).children("ul").remove();
        },
        
        openSubItems: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var elm = $("#" + id);
            $(elm).removeClass("collapsed");
        },
        
        closeSubItems: function(id){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var elm = $("#" + id);
            $(elm).addClass("collapsed");
        },
        
        openAll: function(){
            this.$treeItems.find("li:has(ul)").removeClass("collapsed");
        },
        
        closeAll: function(){
            this.$treeItems.find("li:has(ul)").addClass("collapsed");
        },
                
        enableItem: function(id, url){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var self = this,
                $treeItem = $("#" + id);
            
            url = url || $treeItem.data("href");
            
            // 1st unselect active item
            this.$treeItems.find(".active").removeClass("active");
            
            // then enable the item...
            $treeItem
                .removeClass("disabled")
                .bind('click', function(e) {
                    e.stopPropagation();
                    self.selectItem(id);
                })
                .children("a")
                .addClass("active")
                .attr({
                    "href": url || "javascript:void(0)"
                })
                .end()
                .children("span")
                .bind('click', function(e) {
                    e.stopPropagation();
                    self._toggleSubNavi(this);
                });
        },
        
        disableItem: function(id, url){
            if (!id || typeof id != "string" || !$(this).isAlphanumeric(id) || !$("#" + id).doesExist())
                return;
            
            var $treeItem = $("#" + id),
                url = $treeItem.children("a").attr("href");
            
            // disable the item...
            $treeItem
                .addClass("disabled")
                .data("url", url)
                .unbind('click')
                .children("a")
                .removeClass("active")
                .removeAttr("href")
                .end()
                .children("span")
                .unbind("click");
        
            if ($treeItem.has("ul")){
                $treeItem
                    .addClass("collapsed");
            }
        },
        
        _setOptions: function(options) {
    
        },

        _setOption: function(key, value) {
    
        }
    });

})(jQuery, this);

/**
* ResTabs Widget.
* @date     23.10.2013.
* @version  2.0.1
* @note     UI widget of Responsive Tabs.
*/
(function($, window) {
    $.widget("hui.restabs", {
        version: "2.0.1",

        options: {
            context_obj: false,
            tabPlus: false,
            responsiveMode: true,
            resizeTimeOut: 500,
            showRemove: false
        },
        
        tabIndex: -1,
        tabPlusId: "",
        $tabPlus: false,
        establishStructure: false,
        
        _create: function() {
            var self = this;

            // if is it already created, go out...
            if ($(this).attr("aria-describedby")){
                return false;
            }
            
            // set the element...
            this.$resTabs = $(this.element);
            this.resTabsId = this.$resTabs.attr("id") || this.widgetFullName + "-" + this.uuid;
            
            this.$resTabs.attr({
                "id": this.resTabsId,
                "aria-describedby": this.resTabsId
            });
            
            // set tab elements...
            this.$tabList = this.$resTabs.children(".tab-list").first();
            if (this.$tabList.doesExist()){
                this.establishStructure = true;
            } else {
                this.$tabList = $('<ul class="tab-list" role="tablist"></ul>');
            }
            
            this.$tabListHolder = this.$resTabs.children(".tab-list-holder");
            if (!this.$tabListHolder.doesExist()){
                this.$tabListHolder = $('<div class="tab-list-holder"></div>');
            }
            
            if (this.establishStructure){
                this.$tabList.appendTo(this.$tabListHolder);
            } else {
                this.$tabListHolder.append(this.$tabList);
            }
            
            if (this.options.showRemove){
                this.$tabList.addClass("show-remove");
            }
            
            // if set, add tabPlus icon at the end of tabList
            if (this.options.tabPlus){
                // add a plus tab...
                this.tabPlusId = this.resTabsId + '-tab-plus';
                this.$tabPlus = $('<div><span data-icon="&#xe015;"></span></div>');
                this.$tabPlus
                    .addClass('tab-plus')
                    .attr({
                        'id': this.tabPlusId
                    })
                    .bind('click', function(e){
                        e && e.stopPropagation();
                        self._addNew();
                    });
                    
                this.$tabListHolder.append(this.$tabPlus);
            }
          
            this.$resTabs.append(this.$tabListHolder);
        
            if (this.options.responsiveMode){
                this.$tabDropdown = $('<div class="dropdown"></div>');
                this.$resTabs.append(this.$tabDropdown);
            }
            
            this.$tabArticles = this.$resTabs.children(".articles");
            if (this.$tabArticles.doesExist()){
                this.$tabArticles
                    .attr({
                        'aria-expanded': true
                    });
            } else {
                this.$tabArticles = $('<div class="articles"></div>');
            }
            
            this.$resTabs
                .append(this.$tabArticles)
                .show();
                
            if (this.options.responsiveMode){
                // dropdown settings
                this.$tabDropdown.append('<a id="' + this.resTabsId + '-dropdown-button" href="javascript:void(0);" class="dropdown-button" data-toggle="dropdown"><i class="hui-icon hui-popup-menu"></i></a>');
                this.$tabDropdown.append('<ul id="' + this.resTabsId + '-dropdown-menu" class="dropdown-menu"></ul>');

                this.$tabDropdownButton = $('#' + this.resTabsId + '-dropdown-button');
                this.$tabDropdownMenu = $('#' + this.resTabsId + '-dropdown-menu');

                this.$tabDropdownButton.dropDown();
            }
            
            if (this.establishStructure) {
                this._establishStructure();
            }
            
            $(this).ready(function() {
                self._load();
            });
            
            $(window).bind("resize", function(e){
                e.stopPropagation();
                setTimeout(function(){
                    self.checkBreakpoint();
                }, self.options.resizeTimeOut);
          });
        },
                
        _establishStructure: function() {
            var self = this;
            self.hasActive = false;
            
            this.$tabList.children("li").each(function(index){
                var tabId = self.resTabsId + '-tab-' + index;
                var articleId = self.resTabsId + '-article-' + index;
                var $article = self.$tabArticles.children().eq(index);
                
                if ($article.doesExist()){
                    if ($(this).hasClass("active")){
                        self.hasActive = true;
                    }
                    
                    $(this)
                        .attr({
                            'id': tabId,
                            'role': 'tab',
                            'aria-controls': articleId
                        })
                        .bind('click', function(e){
                            e && e.stopPropagation();
                            self._open(e, this);
                        });
                        
                    $article
                        .addClass('article')
                        .attr({
                            'id': articleId,
                            'aria-labelledby': tabId
                        });
                        
                    self.tabIndex++;
                }
                
                if ($(this).hasClass("removable")){
                    var $removeIcon = $(this).children(".hui-close");
                    
                    $removeIcon
                        .bind('click', function(e){
                            e && e.stopPropagation();
                            self._remove(e, this);
                        });
                }
            });

            if (!self.hasActive){
                this.$tabList.children().eq(0).addClass("active");
                this.$tabArticles.children().eq(0).addClass("active");
            }
            
            // set dropdown into certain tabs...
            this.$resTabs.find("[data-toggle=tab-dropdown]").addClass("tab-title");
            var activeItem = this.$tabList.children(".active.dropdown");
            if (activeItem){
                $(activeItem).children("[data-toggle=tab-dropdown]").dropDown();
            }
            
            // check breakpoint (after few milliseconds!)
            setTimeout(function(){
                self.checkBreakpoint();
            }, 10);
         },
                
        _load: function(){
            this.$resTabs.trigger("load");
            
            // callback...
            if (this.options.load){
                this.options.load($.Event("load"), {
                    id: this.resTabsId,
                    context_obj: this.options.context_obj
                });
            }
        },
                
        _addNew: function(){
            this.$tabPlus.trigger("addNew");
            
            // callback...
            if (this.options.addNew){
                this.options.addNew($.Event("addNew"), {
                    id: this.tabPlusId,
                    context_obj: this.options.context_obj
                });
            }
        },
                
        _open: function (e, elm) {
            var $tab = $(elm);
            var tabId = $tab.attr('id');
            var articleId = $tab.attr('aria-controls');
            
            // important!
            $('html').trigger('click');
            
            this._close();
            
            this.$tabArticles
                .children('#' + articleId)
                .addClass('active');
            
            $tab
                .removeClass('collapsed')
                .addClass('active')
                .trigger("open");
        
            // if set, do 'open' event
            if (this.options.open){
                this.options.open(e, {
                    id: tabId,
                    context_obj: this.options.context_obj
                });
            }
            
            // set dropdown menu if it is defined into active tab
            if ($tab.hasClass("dropdown"))
                $tab.children("[data-toggle=tab-dropdown]").dropDown();                            
        },
        
        _close: function(){
            var $tab = this.$tabList.children('.active');
            var tabId = $tab.attr('id');
            
            if (tabId && typeof tabId == 'string'){
                
                // if set, do 'close' event
                if (this.options.close){
                    this.options.close($.Event('close'), {
                        id: tabId,
                        context_obj: this.options.context_obj
                    });
                }
                
                if ($tab.hasClass("dropdown")){
                    $tab.children("[data-toggle=tab-dropdown]")
                        .unbind("click")
                        .removeAttr("id aria-labelledby role");
                }
                
                // close active elements...
                this.$tabArticles.children("[aria-labelledby=" + tabId + "]").removeClass('active');
                $tab
                    .removeClass('active')
                    .trigger("close");
            }
        },
                
        _remove: function (e, elm) {
            var $tab = $(elm).parent();
            var tabId = $tab.attr('id');                        
            
            // if set, do 'remove' event
            if (this.options.remove){
                this.options.remove($.Event('remove'), {
                    id: tabId,
                    context_obj: this.options.context_obj
                });
            }
            
            this.removeTab(tabId);
        },
        
        addTopTab: function(actions){
            return this.addTab('_top', actions);
        },
        
        addEndTab: function(actions){
            return this.addTab('_bottom', actions);
        },
        
        /**
         * @param pos string|object It indicates a position of new tab _top | _bottom | {before: ID} | {after: ID}
         */
        addTab: function(pos){
            if (!pos) return false;
            
            // prepare referenced article in the middle of list
            var $indexedTab = false;
            var $indexedArticle = false;
            
            if (typeof pos == "object") {
                if (typeof pos.before == "string" && pos.before != "") {
                    $indexedTab = this.$tabList.children("#" + pos.before);
                } else if (typeof pos.after == "string" && pos.after != "") {
                    $indexedTab = this.$tabList.children("#" + pos.after);
                }
                
                if (!$indexedTab.doesExist())
                    return false;
                
                $indexedArticle = this.$tabArticles.children('#' + $indexedTab.attr('aria-controls'));
                
            } else if (pos != "_top" && pos != "_bottom") {
                return false; // only _top and _bottom are allowed like a string commands
            }
            
            var self = this;
            
            // close active tab/panel
            this._close();
            
            this.tabIndex++;
            var tabId = this.resTabsId + '-tab-' + this.tabIndex;
            var articleId = this.resTabsId + '-article-' + this.tabIndex;
            
            // add a new elements...
            var $tab = $('<li></li>');
            $tab
                .addClass('active')
                .attr({
                    'id': tabId,
                    'role': 'tab',
                    'aria-controls': articleId
                })
                .bind('click', function(e){
                    e && e.stopPropagation();
                    self._open(e, this);
                });
                
            var $article = $('<div></div>');
            $article
                .addClass('article active')
                .attr({
                    'id': articleId,
                    'aria-labelledby': tabId
                });
            
            // add them to the end or top of list or before or after desired element
            if (pos == "_bottom"){
                this.$tabList.append($tab);
                this.$tabArticles.append($article);
            } else if (pos == "_top") {
                this.$tabList.prepend($tab);
                this.$tabArticles.prepend($article);
            } else if (typeof pos.before == "string" && $indexedTab && $indexedArticle) {
                $indexedTab.before($tab);
                $indexedArticle.before($article);
            } else if (typeof pos.after == "string" && $indexedTab && $indexedArticle) {
                $indexedTab.after($tab);
                $indexedArticle.after($article);
            }
            
            this.$tabArticles.attr({
                    'aria-expanded': true
                });
                
            return {tabId: tabId, articleId: articleId};
        },
        
        /**
         * @param tabId object It contains id of tab
         */
        activateTab: function (ids) {
            if (typeof ids != "object" || !ids.tabId)
                return false;
            
            var $tab = this.$tabList.children("#" + ids.tabId);
            var articleId = $tab.attr('aria-controls');
            
            // if tab does not exist, go out...
            if (!articleId)
                return false;
            
            this._close();
            
            this.$tabArticles
                .children('#' + articleId)
                .addClass('active');
            
            $tab
                .removeClass('collapsed')
                .addClass('active')
                .trigger("open");
        
            // set dropdown menu if it is defined into active tab
            if ($tab.hasClass("dropdown")){
                $tab.children("[data-toggle=tab-dropdown]").dropDown();
            }
            
        },
        
        /**
         * @param obj object It contains the id of desired tab, title and content
         *                   It is recommended to set the title of tab by this function because of breakPoint checking!
         */
        setTab: function(ids, cont){
            if (typeof ids != "object" || !ids.tabId)
                return false;
            
            var self = this;
            var $tab = this.$tabList.children("#" + ids.tabId);
            var articleId = $tab.attr('aria-controls');
            
            // if tab does not exist, go out...
            if (!articleId)
               return false;
            
            if (cont.title){
                var $titleChild = $tab.children("[data-toggle=tab-dropdown]");
                
                if ($titleChild.doesExist()){
                    var $ddIcon = $titleChild.children("i");
                    $titleChild.html(cont.title);
                    $titleChild.append($ddIcon);
                } else {
                    $tab.html(cont.title);
                }
                
                // set dropdown icon of the tab
                if (cont.dropdown && typeof cont.dropdown == "object" && cont.dropdown.target && typeof cont.dropdown.target == "string"){
                    var ddButton = '<span class="tab-title" data-toggle="tab-dropdown"';
                    ddButton += 'data-target="' + cont.dropdown.target + '">';
                    ddButton += cont.title;
                    ddButton += '<i class="hui-icon hui-';
                    ddButton += (cont.dropdown.icon && typeof cont.dropdown.icon == "string") ? cont.dropdown.icon : 'down';
                    ddButton += '"></i></span>';
                    
                    $tab
                        .addClass("dropdown")
                        .html(ddButton);
                
                }
                
                // set remove (close) icon of the tab
                if (cont.removable && typeof cont.removable == "boolean"){
                    var $removeIcon = $('<i class="hui-icon hui-close after-text"></i>');
                    
                    $removeIcon
                        .bind('click', function(e){
                            e && e.stopPropagation();
                            self._remove(e, this);
                        });
                           
                    $tab
                        .addClass("removable")
                        .append($removeIcon);
                }
                
                // set the tab icon
                if (cont.icon && typeof cont.icon == "string"){
                    var $tabIcon = $('<i class="hui-icon hui-' + cont.icon + ' tab-icon"></i>');
                    
                    $tab
                        .addClass("has-icon")
                        .prepend($tabIcon);
                }
                
                this.checkBreakpoint(); // !important bcs of responsive style
            }
            
            if (cont.content){
                this.$tabArticles.children("#" + articleId).html(cont.content);
            }
        },
                
        /**
         * @param obj object It contains the id of desired tab, title and content
         *                   It is recommended to set the title of tab by this function because of breakPoint checking!
         */
        setTabStyle: function(tabId, style){
            if (!tabId || typeof tabId != "string")
                return false;
            
            var $tab = this.$tabList.children("#" + tabId);
            
            if ($tab.doesExist()){
                $tab.removeClass("styled-new styled-action styled-completed styled-apps");
                
                if (typeof style == "string" && style != ""){
                    $tab.addClass("styled-" + style);
                }
            }
        },
                
        /**
         * @param obj object It contains the id of desired tab
         */
        removeTab: function(tabId){
            if (!tabId || typeof tabId != 'string')
                return false;
            
            // if there is only one tab, don't delete it
            if (this.$tabList.children('li').length < 2)
                return false;
            
            // delete the tab...
            var $indexedTab = this.$tabList.children("#" + tabId);
            var tabIndex = $indexedTab.index();
            var activeIndex = this.getActiveIndex();
            var success = false;
            
            if ($indexedTab && $indexedTab.length) {
                var $indexedArticle = this.$resTabs.children('.articles').children('#' + $indexedTab.attr('aria-controls'));
            
                if ($indexedArticle && $indexedArticle.length){
                    $indexedTab.remove();
                    $indexedArticle.remove();
                    
                    this.removeDropdownItem(tabId);
                    
                    success = true;
                    
                    // if deleted tab was active, set a new active tab...
                    if (tabIndex == activeIndex){
                        if (activeIndex > 0){
                            activeIndex--;
                        }

                        this.$tabList.children().eq(activeIndex).addClass('active').removeClass('collapsed');
                        this.$tabArticles.children().eq(activeIndex).addClass('active');
                    }
                    
                    this.checkBreakpoint();
                }
            }
            
            return success;
        },
                
        removeClosedTabs: function(){
            this.$tabList.children(':not(.active)').each(function(){
                $($(this).attr('aria-controls')).remove();
                $(this).remove();
            });
            
            this.$tabDropdownMenu.empty();
            this.$resTabs.removeClass('tabs-in-dropdown');
            
            return true;
        },
        
        getActiveIndex: function(){
            return this.$tabList.children('.active').index();
        },
                
        getActiveId: function(){
            var activeIndex = this.getActiveIndex();
            
            if (activeIndex < 0)
                return false;

            return (this.$tabList.children('li').eq(activeIndex).attr('id'));
        },
                
        getTabPlusId: function(){
            return this.tabPlusId;
        },
                
        getArticleId: function(tabId){
            if (!tabId || typeof tabId != 'string')
                return false;
            
            return (this.$tabList.children('#' + tabId).attr('aria-controls'));
        },
                
        checkBreakpoint: function () {
            var $tabs = this.$tabList.not('.collapsed').not('.tab-plus').children();
            
            // if there is less than 2 tabs, exit...
            if ($tabs.length < 2)
                return false;
            
            var tabItems = this.$tabList.children();
            var activeIndex = this.getActiveIndex();
            var respWidth = this.$resTabs.outerWidth(true); // width of the main container
            var tabsWidth = $(tabItems).eq(activeIndex).outerWidth(true) + this.$tabDropdownButton.outerWidth(true); // it has min width of the active tab
            tabsWidth += (this.$tabPlus) ? this.$tabPlus.outerWidth() : 0;
            
            for (var i = 0; i < tabItems.length; i++){
                var $tabItem = $(tabItems).eq(i);
                
                // calculate width of no-active tabs only...
                if (!($tabItem.hasClass('active') || $tabItem.hasClass('tab-plus'))){
                    tabsWidth += $tabItem.outerWidth(true);
                    
                    if (tabsWidth > respWidth){
                        // "move" tab into dropdown menu...
                        if (!$tabItem.hasClass('collapsed')){
                            $tabItem.addClass('collapsed');
                        }
                    } else {
                        // "remove" tab from dropdown list...
                        if ($tabItem.hasClass('collapsed')){
                            $tabItem.removeClass('collapsed');
                        }
                    }
                }
            }
            
            // popultate dropdown list with collapsed tabs
            this.populateDropdown();
        },
        
        populateDropdown: function(){
            var self = this;
            var $cTabs = this.$tabList.children('.collapsed');
            
            this.$tabDropdownMenu.empty();
            
            $cTabs.each(function(){
                var tabId = $(this).attr('id');
                var tabLabel = $(this).html();
                var $ddItem = $('<li><a href="javascript:void(0);">' + tabLabel + '</a></li>');
                
                $ddItem
                    .attr({
                        'id': tabId + '-item',
                        'aria-labelledby': tabId
                    })
                    .bind('click', function(e){
                        e && e.stopPropagation();
                        self.removeDropdownItem(tabId);
                        self.openDropdownItem(e, this);
                    });
                    
                self.$tabDropdownMenu.append($ddItem);
            });
            
            // set the main container
            if (this.$tabDropdownMenu.children('li').length > 0){
                this.$resTabs.addClass('tabs-in-dropdown');
            } else {
                this.$resTabs.removeClass('tabs-in-dropdown');
            }
        },
        
        removeDropdownItem: function(id) {
            var $ddItem = $('#' + id + '-item');
            $ddItem.remove();

            if (this.$tabDropdownMenu.children().length < 1){
                this.$resTabs.removeClass('tabs-in-dropdown');
            }
        },
                
        openDropdownItem: function(e, elm){
            var tab = $('#' + $(elm).attr('aria-labelledby'));
            this._open(e, tab);
            this.checkBreakpoint();
        },
                
        setTabIndicator: function(data) {
            this.$tabList.children().each(function(k, v) {
                var tab_indicator = $(v).find('span.tab-indicator');
                tab_indicator.remove();
            });
            for (k in data) {
                var unread_message = data[k]['unread_message_num'];
                $('[data-dialogue-id="' + k + '"] span i').before("<span class='tab-indicator'>[" + unread_message + "]</span>");
            }
        }
    });

})(jQuery, this);


/**
* CollapsTabs Widget.
* @date     09.06.2014.
* @version  1.0.0
* @note     UI widget of Collapsible Tabs.
*/
(function($, window) {
    $.widget("hui.collapstabs", {
        version: "1.0.0",

        options: {
            context_obj: false,
            tabPlus: false,
            showRemove: false
        },
        
        tabIndex: -1,
        tabPlusId: "",
        $tabPlus: false,
        
        _create: function() {
            var self = this;

            // if is it already created, go out...
            if ($(this).attr("aria-describedby")){
                return false;
            }
            
            // set the element...
            this.$collapsTabs = $(this.element);
            this.collapsTabsId = this.$collapsTabs.attr("id") || this.widgetFullName + "-" + this.uuid;
            
            this.$collapsTabs.attr({
                "id": this.collapsTabsId,
                "aria-describedby": this.collapsTabsId
            });
            
            if (this.$collapsTabs.children(".tab").doesExist()){
                this._establishStructure();
            }
            
            $(this).ready(function() {
                self._load();
            });
            
        },
                
        _establishStructure: function() {
            var $exTabs = this.$collapsTabs.children(".tab");
            var self = this;
            self.hasActive = false;
            
            $exTabs.each(function(index){
                var tabId = self.collapsTabsId + '-tab-' + index;
                var titleId = self.collapsTabsId + '-title-' + index;
                var contentId = self.collapsTabsId + '-content-' + index;
                
                var $tabTitle = $(this).children('.title');
                var $tabContent = $(this).children('.content');
                var $tabContentHolder = $(this).children(".content-holder");
                
                if (!$tabContentHolder.doesExist() && $tabContent.doesExist()){
                    $tabContentHolder = $('<div class="content-holder"></div>');
                    $tabContent.appendTo($tabContentHolder);
                    $(this).append($tabContentHolder);
                }
                
                if ($tabTitle.doesExist() && $tabContentHolder.doesExist()){
                    if ($(this).hasClass("active")){
                        self.hasActive = true;
                    }

                    $(this)
                        .attr({
                            'id': tabId
                        })
                        .bind('click', function(e){
                            e && e.stopPropagation();
                            self._setActive(e, this);
                        });
                        
                    $tabTitle
                        .attr({
                            'id': titleId,
                            'aria-labelledby': tabId
                        })
                        .bind('click', function(e){
                            e && e.stopPropagation();
                            self._toggle(e, this);
                        });
                        
                    $tabContentHolder
                        .attr({
                            'id': contentId,
                            'aria-labelledby': tabId
                        });
                        
                    self.tabIndex++;
                }
            });

            if (!self.hasActive){
                $exTabs.eq(0).addClass("active");
            }
         },
                
        _load: function(){
            this.$collapsTabs.trigger("load");
            
            // callback...
            if (this.options.load){
                this.options.load($.Event("load"), {
                    id: this.collapsTabsId,
                    context_obj: this.options.context_obj
                });
            }
        },
                
        _setActive: function(e, elm){
            this.$collapsTabs
                .children(".tab")
                .removeClass("active");
        
            $(elm).addClass("active");
        },
                
        _toggle: function (e, elm) {
            var $tab = $(elm).parent();
            var tabId = $tab.attr('id');
            
            // important!
            $('html').trigger('click');
            
            $tab.toggleClass("collapsed");
            
            // if the article is collapsed...
            if ($tab.hasClass("collapsed")){
                $tab
                    .removeClass("active")
                    .trigger("close");
                
                // if set, do 'close' event
                if (this.options.close){
                    this.options.close($.Event('close'), {
                        id: tabId,
                        context_obj: this.options.context_obj
                    });
                }
            } else {
                // when the article is opened...
                $tab.trigger("open");
                this.activateTab(tabId);
                
                // if set, do 'close' event
                if (this.options.open){
                    this.options.open($.Event('close'), {
                        id: tabId,
                        context_obj: this.options.context_obj
                    });
                }
            }
        },
        
        _remove: function (e, elm) {
            var $tab = $(elm).parent();
            var tabId = $tab.attr('id');
            
            this.removeTab(tabId);
            
            // if set, do 'remove' event
            if (this.options.remove){
                this.options.remove($.Event('remove'), {
                    id: tabId,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        addTopTab: function(){
            return this.addTab('_top');
        },
        
        addBottomTab: function(){
            return this.addTab('_bottom');
        },
        
        /**
         * @param pos string|object It indicates a position of new tab: _top | _bottom | {before: ID} | {after: ID}
         * @return object|boolean It returns the IDs of added tab or false
         */
        addTab: function(pos){
            if (!pos) return false;
            
            // prepare referenced article in the middle of list
            var $indexedTab = false;
            
            if (typeof pos === "object") {
                if (typeof pos.before === "string" && pos.before !== "") {
                    $indexedTab = this.$collapsTabs.children("#" + pos.before);
                } else if (typeof pos.after === "string" && pos.after !== "") {
                    $indexedTab = this.$collapsTabs.children("#" + pos.after);
                }
                
                if (!$indexedTab.doesExist())
                    return false;
                
            } else if (pos !== "_top" && pos !== "_bottom") {
                return false; // only _top and _bottom are allowed like a string commands
            }
            
            var self = this;
            
            this.tabIndex++;
            var tabId = this.collapsTabsId + '-tab-' + this.tabIndex;
            var titleId = this.collapsTabsId + '-title-' + this.tabIndex;
            var contentId = this.collapsTabsId + '-content-' + this.tabIndex;
            
            // add a new tab with title and content elements...
            var $tab = $('<div></div>');
            $tab
                .addClass('tab')
                .attr({
                    'id': tabId
                })
                .bind('click', function(e){
                    e && e.stopPropagation();
                    self._setActive(e, this);
                });
            
            var $title = $('<div></div>');
            $title
                .addClass('title')
                .attr({
                    'id': titleId,
                    'aria-labelledby': tabId
                })
                .bind('click', function(e){
                    e && e.stopPropagation();
                    self._toggle(e, this);
                });

            var $content = $('<div><div class="content"></div></div>');
            $content
                .addClass('content-holder')
                .attr({
                    'id': contentId,
                    'aria-labelledby': tabId
                });
                
            $tab.append($title);
            $tab.append($content);
            
            // add the tab on certain place
            if (pos === "_bottom"){
                this.$collapsTabs.append($tab);
            } else if (pos === "_top") {
                this.$collapsTabs.prepend($tab);
            } else if (typeof pos.before === "string" && $indexedTab) {
                $indexedTab.before($tab);
            } else if (typeof pos.after === "string" && $indexedTab) {
                $indexedTab.after($tab);
            }
            
            return {tabId: tabId, titleId: titleId, contentId: contentId};
        },
        
        /**
         * @param tabId string ID of tab
         * @return true|false
         */
        activateTab: function (tabId) {
            if (!tabId || typeof tabId !== 'string')
                return false;
            
            if (this.$collapsTabs.children("#" + tabId).doesExist()){
                this.$collapsTabs
                    .children(".tab")
                    .removeClass("active")
                    .end()
                    .children("#" + tabId)
                    .addClass("active")
                    .trigger("open");
            
                return true;
            } else {
                return false;
            }
        },
        
        /**
         * @return integer Index of the active tab
         */
        getActiveIndex: function(){
            return this.$collapsTabs.children(".tab.active").index();
        },
                
        /**
         * @param index integer Index of the tab, optionaly
         * @return object It returns IDs of all tab objects
         */
        getTabId: function(index){
            var $tabs = this.$collapsTabs.children('.tab');
            
            if (typeof index !== 'number' || index < 0 || index >= $tabs.length)
                return false;
            
            var $tab = $tabs.eq(index);
            var tabId = $tab.attr('id');
            var titleId = $tab.children('.title').attr('id');
            var contentId = $tab.children('.content-holder').attr('id');
            
            return {tabId: tabId, titleId: titleId, contentId: contentId};
        },
                
        /**
         * @param tabId string ID of the tab
         * @return object It returns tab elements: title and content
         */
        getTab: function(tabId){
            if (!tabId || typeof tabId !== 'string')
                return false;
            
            var $tab = this.$collapsTabs.children('#' + tabId);
            if (!$tab.doesExist())
                return false;
            
            var $title = $tab.children('.title');
            var $content = $tab.find('.content').first();
            
            return {title: $title, content: $content};
        },
        
        /**
         * @param tabId string Tab ID
         * @param tabObj object It contains both title and content of desired tab
         * @return true|false
         */
        setTab: function(tabId, tabObj){
            if (!tabId || typeof tabId !== 'string')
                return false;
            
            var $tab = this.$collapsTabs.children("#" + tabId);
            if (!$tab.doesExist())
                return false;
            
            var $title = $tab.children(".title");
            var $content = $tab.find(".content").first();
            
            if (tabObj.title && typeof tabObj.title === "string")
                $title.html(tabObj.title);

            if (tabObj.content && typeof tabObj.content === "string")
                $content.html(tabObj.content);
            
            if (tabObj.width && typeof tabObj.width === "number" && tabObj.width > 0 && tabObj.width < 5000)
                $content.width(tabObj.width);
            
            return true;
        },
                
        /**
         * @param tabId string Tab ID
         * @return true|false
         */
        removeTab: function(tabId){
            if (!tabId || typeof tabId !== 'string')
                return false;
            
            // remove the tab...
            var $tab = this.$collapsTabs.children("#" + tabId);
            if (!$tab.doesExist())
                return false;
            
            $tab.remove();
            
            return true;
        }
    });

})(jQuery, this);


/* Collapsible List */
(function($, window){
    var namespace = 'collapslist',
        namespaceIndex = -1,
        $window = $(window);
        
    $.fn.collapsList = function(opt){
        // if is not already created...
        if (!$(this).attr('aria-labelledby')) {
            var _collapslist = new CollapsList(this, opt);
            _collapslist.create();
            
            return _collapslist;
        } else
            return false;
    };

    var CollapsList = function(elm, opt) {
        // options...
        this.options = $.extend({
            defaultState:'collapsed',
            ajaxURL:'',
            context_obj: false
            }, opt);
            
        // variables...
        this.vars = {
            moreIndex: -1
        };
        
        // set the element...
        this.$collapslist = $(elm);
        this.namespaceId = this.$collapslist.attr('id') || namespace + '-' + (++namespaceIndex);
        
        this.$collapslist.attr({
            'id': this.namespaceId,
            'aria-labelledby': this.namespaceId
        });
    };
    
    CollapsList.prototype = {
        create: function(){
            var self = this;
            
            // set collapslist articles
            this.$collapslist
                .children(':not(.tpl)')
                .each(function(index){
                    var $article = $(this);
                    var articleId = self.namespaceId + '-article-' + index;

                    // if is it "more" article...
                    if ($article.hasClass('more')){
                        self.vars.moreIndex = index;

                        $article
                            .attr({
                                'id': articleId
                            })
                            .bind('click', function(e){
                                e && e.stopPropagation();
                                self.moreArticles();
                            });
                    } else {
                        // otherwise, set the article by certain data
                        $article
                            .addClass($(this).hasClass('expanded') ? '' : self.options.defaultState)
                            .attr({
                                'id': articleId,
                                'aria-expanded': $(this).hasClass('collapsed') ? false : true
                            });
                        
                        var $heading = $article.children('.heading');

                        if ($heading.doesExist()){
                            $heading.bind('click', function(e){
                                e && e.stopPropagation();
                                self.toggle(e, articleId);
                            });
                        }
                    }
                });
            
            this.$collapslist.show();
        },
        
        appendTopElement: function(actions){
            return this.addArticle("_top", actions);
        },
        
        appendEndElement: function(actions){
            return this.addArticle("_bottom", actions);
        },

        /**
         * @param actions Assocc array with list of actions in drop down menu of article
         * @param opt string|object When string opt is _top | _bottom
         *                          When object then {above: ID, below: ID, position: _top|_bottom, prevent_on_click: true|false}
         *                               elements above, bellow and position are mutualy exclusive
         */
        addArticle: function(opt, actions) {            
            if (!opt) return false;
            
            var ind; // position where to add article: above | bellow | _top | _bottom
            var attachOnClick = true;
            
            var self = this;
            var tpl = this.$collapslist.children('.tpl.tpl-article');
            var articleId = false;

            if (!tpl) {
                return false;
            }

            // prepare referenced article in the middle of list
            var $indexedArticle;
            if (typeof opt == "object") {
                if (typeof opt.above == "string") {
                    $indexedArticle = this.$collapslist.children("#" + opt.above);
                    if (!$indexedArticle  || !$indexedArticle.length) return false;
                    ind = "above";
                } else if (typeof opt.below == "string") {
                    $indexedArticle = this.$collapslist.children("#" + opt.below);
                    if (!$indexedArticle  || !$indexedArticle.length) return false;
                    ind = "bellow";
                } else if (typeof opt.position == "string") {
                    if (opt.position == "_top" || opt.position == "_bottom") {
                        ind = opt.position;
                    }
                }

                // check is we have othe optioins in opt object
                if (typeof opt.prevent_on_click == "boolean") {
                    attachOnClick = !opt.prevent_on_click;
                }
            } else if (opt == "_top" || opt == "_bottom") {
                ind = opt;
            }
            
            if (!ind) {
                // unknown or wrong new article positoin - we have to exit
                return false; 
            }
                        
            var elm = $(tpl).clone();
            var $elm = $(elm);
            var idx = this.$collapslist.children().length - 1;
            var $heading = $elm.children('.heading');
            var $actions = $heading.children('.actions');

            articleId = this.namespaceId + '-article-' + idx;

            // set actions of this article...
            if ($actions.doesExist() && actions){
                var $actionList = $('<ul class="dropdown-menu"></ul>');

                $.each(actions, function(key, val) {
                    var $action = $('<li><a href="javascript:void(0);">' + val.name + '</a></li>');
                    $action.bind('click', function(e){
                        val.value($.Event("runAction"), {
                            id: articleId,
                            actionKey: key
                        });
                    });

                    $actionList.append($action);
                });

                $actions
                    .addClass("dropdown")
                    .append('<a href="#" class="dropdown-button" data-toggle="dropdown"><i class="f-icon"></i></a>')
                    .append($actionList);

            }

            // set the header
            if ($heading.doesExist() && attachOnClick){
                $heading.bind('click', function(e){
                    e && e.stopPropagation();
                    self.toggle(e, articleId);
                });
            }

            // set the article...
            $elm
                .removeClass('tpl tpl-article')
                .addClass('article collapsed')
                .attr({
                    'id': articleId,
                    'aria-expanded': false
                });

            // and add it to the end or top of list or before or after desired element
            if (ind == "_bottom"){
                $elm.appendTo(this.$collapslist);
            } else if (ind == "_top") {
                $elm.prependTo(this.$collapslist);
            } else if (ind == "above") {
                $indexedArticle.before($elm);
            } else if (ind == "below") {
                $indexedArticle.after($elm);
            }

            if (articleId) {
                // after adding the article, call DropDown to set actions...
                if ($actions.doesExist() && actions){
                    $("[data-toggle=dropdown]").dropDown();
                }
            }
            
            return articleId;
        },
        
        toggle: function (e, id) {
            var $article = this.$collapslist.children('#' + id);
            
            // important!
            $('html').trigger('click');
            
            if ($article.hasClass('collapsed')){
                // expand the article...
                $article
                    .removeClass('collapsed')
                    .attr('aria-expanded', true);
                    
                if (this.options.expand){
                    this.options.expand(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
                
            } else {
                // collapse the article...
                $article
                    .addClass('collapsed')
                    .attr('aria-expanded', false);
            
                var $content = $article.children('.content');
                
                if ($content.hasClass('remove'))
                    $content.html('');
                    
                if (this.options.collapse){
                    this.options.collapse(e, {
                        id: id,
                        context_obj: this.options.context_obj
                    });
                }
            }
        },
        
        toggleState: function (id) {
            var $article = this.$collapslist.children('#' + id);
            
            if ($article.hasClass('collapsed')){
                // expand the article...
                $article
                    .removeClass('collapsed')
                    .attr('aria-expanded', true);
            } else {
                // collapse the article...
                $article
                    .addClass('collapsed')
                    .attr('aria-expanded', false);
            
                var $content = $article.children('.content');
                
                if ($content.hasClass('remove'))
                    $content.html('');
            }
        },
        
        currentState: function(id){
            var $article = this.$collapslist.children('#' + id);
            return (($article.attr('aria-expanded') === 'true') ? 'expanded' : 'collapsed');

        },
                
        showMore: function(){
            var self = this;
            var tpl = this.$collapslist.children('.tpl.tpl-more');
            var articleId = false;
            
            if (tpl){
                var elm = $(tpl).clone();
                var idx = this.$collapslist.children().length - 1;
                articleId = this.namespaceId + '-article-' + idx;
                
                $(elm)
                    .removeClass('tpl tpl-more')
                    .addClass('article more')
                    .attr({
                        'id': articleId
                    })
                    .bind('click', function(e){
                        e && e.stopPropagation();
                        self.doMore(e, articleId);
                    });
                
                $(elm).appendTo(this.$collapslist);
            }
            
            return articleId;
        },
        
        doMore: function (e, id) {
            if (this.options.showMore)
                this.options.showMore(e, {'id':id});
        },
        
        removeMore: function(){
            this.$collapslist.children('.article.more').each(function(){
                $(this).remove();
            });
        },

        // remove article with certain id, return true|false
        clearArticle: function(id) {
            var $article = this.$collapslist.children("#" + id);

            if (!$article  || !$article.length) {
                return false; // ops, the ID of referenced article is wrong or index object is wrong
            }

            $article.remove();

            return true;
        },
        
        clearList: function(){
            this.$collapslist.children(':not(.tpl)').each(function(){
                $(this).remove();
            });
        }
    };
    
})(jQuery, this);


/* DropDown */
(function($, window){
    var settings,       // global settings
        namespace = 'dropdown',
        namespaceIndex = -1,
        $window = $(window);
    
    $.fn.dropDown = function(options){
        settings = $.extend({
            }, options);
            
        // for each button, create a dropdown instance...
        $(this).each(function(){
            // if is not already created...
            if (!$(this).attr('aria-labelledby')) {
                new DropDown(this);
            }
        });
    };
    
    var DropDown = function (elm) {
        var that = this;
        
        namespaceIndex++;
        
        // set the button
        this.$button = $(elm);
        this.contextMenu = (this.$button.attr('data-contextmenu') === 'true') ? true : false;
        
        if (!this.$button.attr('id')){
            this.$button.attr('id', namespace + '-button-' + namespaceIndex);
        }
        
        this.$button
            .attr({
                'aria-labelledby': this.$button.attr('id'),
                'role': 'button'
            });
        
        if (this.contextMenu){
            this.$button.bind($('html').hasClass('touch') ? 'taphold' : 'contextmenu', function (e) {
                e.stopPropagation();
                e.preventDefault();
                that.toggle(e);
            });
        } else {
            this.$button.on('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                that.toggle(e);
            });
        }
        
        // set the dropdown by target attribut...
        this.$menu = $('#' + this.$button.attr('data-target'));
        
        // or by getting certain node
        if (!this.$menu.doesExist()){
            this.$menu = this.$button.parent().children('.dropdown-menu');
        }

        if (!this.$menu.attr('id')){
            this.$menu.attr('id', namespace + '-menu-' + namespaceIndex);
        }
        
        this.$menu
            .attr({
                'role': 'menu',
                'aria-expanded': false,
                'aria-labelledby': ''
            })
            .appendTo('body');
    };
    
    DropDown.prototype = {
        constructor: DropDown,
                
        toggle: function (e) {
            var that = this;
            var posX = this.$button.offset().left,
                posY = this.$button.offset().top,
                menuX = posX,
                menuY = posY + this.$button.outerHeight(),
                menuWidth = this.$menu.outerWidth(true),
                menuHeight = this.$menu.outerHeight(true),
                isOpened = (this.$menu.attr('aria-expanded') === 'true') ? true : false,
                sameButton = (this.$menu.attr('aria-labelledby') === this.$button.attr('id'));
            
            // close all opened dropdowns
            this.closeAll();
            
            if (this.contextMenu){
                menuX = ((e.pageX - $window.scrollLeft()) + menuWidth < $window.width()) ? e.pageX : e.pageX - menuWidth;
                menuY = ((e.pageY - $window.scrollTop()) + menuHeight < $window.height()) ? e.pageY : e.pageY - menuHeight;
            }
            
            // check for the right orientation depending of screen size
            if (menuX + menuWidth > $window.width()){
                menuX = posX - menuWidth + this.$button.outerWidth();
                
                this.$menu.find('li').each(function(){
                    if ($(this).children('ul').doesExist())
                        $(this).addClass('to-left');
                });
            }
            
            if (menuY + menuHeight > $window.height() + $window.scrollTop())
                menuY = posY - menuHeight;

            
            // open this dropdown
            if (!isOpened || !sameButton){
                var onExpand = this.$button.attr("onExpand");
                if (onExpand){
                    var f = new Function (onExpand);
                    f();
                }
                
                this.$button.addClass('active');
                
                this.$menu
                    .css({
                        top: menuY, 
                        left: menuX
                    })
                    .attr({
                        'aria-expanded': true,
                        'aria-labelledby': this.$button.attr('id')
                    });
                    
                // event to close that dropdown
                $('html').bind('click', function (e) {
                    e.stopPropagation();
                    that.close();
                });
            }
        },
        
        close: function () {
            $('html').unbind('click');
            this.$menu.attr('aria-expanded', false);
            this.$button.removeClass('active');
        },
        
        closeAll: function () {
            // close all opened dropdowns
            $('.dropdown-menu[aria-expanded="true"]').each(function(){
                $(this)
                    .attr('aria-expanded', false)
                    .find('li.to-left')
                    .each(function(){
                        $(this).removeClass('to-left');
                    });
            });
            
            // deactive dropdown buttons
            $('[data-toggle=dropdown]').each(function(){
                $(this).removeClass('active');
            });
        }

    };
    
    $(window).on('resize', function(e){
        e.stopPropagation();
        DropDown.prototype.closeAll();
    });
    
})(jQuery, this);


/* Alert */
(function($, window){
    var settings,
        _alert;
        
    $.fn.alert = function(options){
        settings = $.extend({
            }, options);
            
        $(this).each(function(){
            _alert = new Alert(this);
        });
    };
    
    var Alert = function (elm) {
        $(elm).on('click', '.close', this.close);
    };
    
    Alert.prototype = {
        close: function (e) {
            var $this = $(this),
                $parent = $this.parent();
            
            e && e.preventDefault();
            
            $parent
                .trigger(e = $.Event('close'))
                .remove();
        }
    };
    
})(jQuery, this);

/* Common */
(function($){
    $.fn.doesExist = function(){
        return $(this).length > 0;
    };
    
})(jQuery, this);

(function($){
    $.fn.isAlphanumeric = function(str) {
        return /^[a-zA-Z0-9\+\-\_]+$/.test(str);
    };
})(jQuery, this);

(function($) {
    $.fn.hasVerticalScroll = function() {
        return (document.documentElement.scrollHeight !== document.documentElement.clientHeight);
    };
})(jQuery, this);
