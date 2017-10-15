cms.registerWidgetHandler('share', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'share';
	};

	this.getIcon = function(){
		return "fa-share-alt";
	};

    this.onClick = false;

    this.getToolbarButtons = function(){

        var handler = this;

		return {
			options: false
		};

	};

    this.onCreateWidget = function(widget, regionId){

        var dom = this.dom(widget);

        dom.append('<a href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2F%23&t=" title="Facebook" target="_blank" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=\' + encodeURIComponent(document.URL) + \'&t=\' + encodeURIComponent(document.URL)); return false;"><img src="'+cms.options.rootUrl+'/static/share/Facebook.png"></a>');
        dom.append('<a href="https://twitter.com/intent/tweet?source=http%3A%2F%2F%23&text=:%20http%3A%2F%2F%23" target="_blank" title="Tweet" onclick="window.open(\'https://twitter.com/intent/tweet?text=\' + encodeURIComponent(document.title) + \':%20\'  + encodeURIComponent(document.URL)); return false;"><img src="'+cms.options.rootUrl+'/static/share/Twitter.png"></a>');
        dom.append('<a href="https://plus.google.com/share?url=http%3A%2F%2F%23" target="_blank" title="Google+" onclick="window.open(\'https://plus.google.com/share?url=\' + encodeURIComponent(document.URL)); return false;"><img src="'+cms.options.rootUrl+'/static/share/Google+.png"></a>');
        dom.append('<a href="http://www.linkedin.com/shareArticle?mini=true&url=http%3A%2F%2F%23&title=&summary=&source=http%3A%2F%2F%23" target="_blank" title="LinkedIn" onclick="window.open(\'http://www.linkedin.com/shareArticle?mini=true&url=\' + encodeURIComponent(document.URL) + \'&title=\' +  encodeURIComponent(document.title)); return false;"><img src="'+cms.options.rootUrl+'/static/share/LinkedIn.png"></a>');

		return widget;

	};

});
