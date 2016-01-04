/* Responsive Tabs */
(function($, window){
    var namespace = 'rtabs',
        namespaceIndex = -1,
        $window = $(window);
        
    $.fn.resTabs = function(opt){
        // if is not already created...
        if (!$(this).attr('aria-labelledby')) {
            var _restabs = new ResTabs(this, opt);
            _restabs.init();
            
            return _restabs;
        } else
            return false;
    };

    var ResTabs = function(elm, opt) {
        var self = this;
        
        // options...
        this.options = $.extend({
            ajaxURL:'',
            context_obj: false
            }, opt);
            
        // variables...
        this.vars = {
            tabIndex: -1
        };
        
        this.$tabList = $('<ul class="tab-list" role="tablist"></ul>');
        this.$tabDropdown = $('<div class="dropdown"></div>');
        this.$tabArticles = $('<div class="articles"></div>');
        
        // set the element...
        this.$resTabs = $(elm);
        this.namespaceId = this.$resTabs.attr('id') || namespace + '-' + (++namespaceIndex);
        
        this.$resTabs.attr({
            'id': this.namespaceId,
            'aria-labelledby': this.namespaceId
        });
        
        $window.resize(function(){
            self.checkBreakpoint();
        });
    };
    
    ResTabs.prototype = {
        constructor: ResTabs,
        
        init: function(){
            var self = this;
            
            this.$resTabs
                .append(this.$tabList)
                .append(this.$tabDropdown)
                .append(this.$tabArticles)
                .show();
        
            // dropdown settings
            this.$tabDropdown.append('<a id="' + this.namespaceId + '-dropdown-button" href="javascript:void(0);" class="dropdown-button" data-toggle="dropdown"><i class="hui-icon hui-popup-menu"></i></a>');
            this.$tabDropdown.append('<ul id="' + this.namespaceId + '-dropdown-menu" class="dropdown-menu"></ul>');
            
            this.$tabDropdownButton = $('#' + this.namespaceId + '-dropdown-button');
            this.$tabDropdownMenu = $('#' + this.namespaceId + '-dropdown-menu');
            
            this.$tabDropdownButton.dropDown();
            
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
                if (typeof pos.before == "string") {
                    $indexedTab = this.$tabList.children("#" + pos.before);
                } else if (typeof pos.after == "string") {
                    $indexedTab = this.$tabList.children("#" + pos.after);
                }

                if ($indexedTab && $indexedTab.length) {
                    $indexedArticle = this.$tabArticles.children('#' + $indexedTab.attr('aria-controls'));
                } else {
                    return false; // ops, the ID of referenced tab/article is wrong or index object is wrong
                }
                
            } else if (pos != "_top" && pos != "_bottom") {
                return false; // only _top and _bottom are allowed like a string commands
            }
            
            var self = this;
            
            // close active tab/panel
            this.$tabList.children('.active').removeClass('active');
            this.$tabArticles.children('.active').removeClass('active');
            
            this.vars.tabIndex++;
            var tabId = this.namespaceId + '-tab-' + this.vars.tabIndex;
            var articleId = this.namespaceId + '-article-' + this.vars.tabIndex;
            
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
                    self.open(e, this);
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
                    
                    // if deleted tab was actice, set a new active tab...
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
            var self = this;
            
            this.$tabList.children(':not(.active)').each(function(){
                var $tab = $(this);
                var $article = $($tab.attr('aria-controls'));
                
                $tab.remove();
                $article.remove();
            });
            
            this.$tabDropdownMenu.empty();
            this.$resTabs.removeClass('tabs-in-dropdown');
            
            return true;
        },
        
        /**
         * @param tabIds object It contains ids both of tab and article {tabId: ID, articleId: ID}
         * @param cont object   {title: text|html} | {content: text|html}
         *                      It is recommended to set the title of tab by this function because of breakPoint checking!
         */
        setTab: function(tabIds, cont){
            if (typeof cont == 'object'){
                if (cont.title){
                    $("#" + tabIds.tabId).html(cont.title);
                    
                    // !important bcs of responsive style
                    this.checkBreakpoint();
                }
                if (cont.content){
                    $("#" + tabIds.articleId).html(cont.content);
                }
            }
        },
                
        open: function (e, elm) {
            var $elm = $(elm);
            var tabId = $elm.attr('id');
            var articleId = $elm.attr('aria-controls');
            
            // important!
            $('html').trigger('click');
            
            this.close(e);
            
            this.$tabList.children('#' + tabId).addClass('active').removeClass('collapsed');
            this.$tabArticles.children('#' + articleId).addClass('active');
            
            if (this.options.open){
                this.options.open(e, {
                    id: tabId,
                    context_obj: this.options.context_obj
                });
            }
        },
        
        close: function(e){
            e = e || $.Event('close');
            
            var $tab = this.$tabList.children('.active');
            var tabId = $tab.attr('id');
            
            if (tabId && typeof tabId == 'string'){
                
                if (this.options.close){
                    this.options.close(e, {
                        id: tabId,
                        context_obj: this.options.context_obj
                    });
                }
                
                // remove active elements...
                $tab.removeClass('active');
                this.$tabArticles.children('.active').removeClass('active');
            }
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
                
        getArticleId: function(tabId){
            if (!tabId || typeof tabId != 'string')
                return false;
            
            return (this.$tabList.children('#' + tabId).attr('aria-controls'));
        },
                
        checkBreakpoint: function () {
            var $tabs = this.$tabList.not('.collapsed').children();
            
            // if there is less than 2 tabs, exit...
            if ($tabs.length < 2)
                return false;
            
            var tabItems = this.$tabList.children();
            var activeIndex = this.getActiveIndex();
            var tabsWidth = $(tabItems).eq(activeIndex).outerWidth(true) + this.$tabDropdownButton.outerWidth(true); // it has min width of the active tab
            var respWidth = this.$resTabs.outerWidth(true); // width of the main container
            
            for (var i = 0; i < tabItems.length; i++){
                var $tabItem = $(tabItems).eq(i);
                
                // calculate width of no-active tabs only...
                if (!$tabItem.hasClass('active')){
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
            this.open(e, tab);
            this.checkBreakpoint();
        }
        
    };
    
})(jQuery, this);

