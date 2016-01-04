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
            return this.addArticle(0, actions);
        },
        
        appendEndElement: function(actions){
            return this.addArticle(-1, actions);
        },
        
        addArticle: function(ind, actions){
            ind = ind || 0;
            
            var self = this;
            var tpl = this.$collapslist.children('.tpl.tpl-article');
            var articleId = false;
            
            if (tpl){
                var elm = $(tpl).clone();
                var idx = this.$collapslist.children().length - 1;
                var $heading = $(elm).children('.heading');
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
                        .append('<a href="#" class="dropdown-button" data-toggle="dropdown"><i class="hui-icon hui-popup-menu"></i></a>')
                        .append($actionList);
                
                }
                
                // set the header
                if ($heading.doesExist()){
                    $heading.bind('click', function(e){
                        e && e.stopPropagation();
                        self.toggle(e, articleId);
                    });
                }
                
                // set the article...
                $(elm)
                    .removeClass('tpl tpl-article')
                    .addClass('article collapsed')
                    .attr({
                        'id': articleId,
                        'aria-expanded': false
                    });
                
                // and add it to the end or top of list
                if (ind < 0){
                    $(elm).appendTo(this.$collapslist);
                } else {
                    $(elm).prependTo(this.$collapslist);
                }
                
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
        
        clearList: function(){
            this.$collapslist.children(':not(.tpl)').each(function(){
                $(this).remove();
            });
        }
    };
    
})(jQuery, this);

