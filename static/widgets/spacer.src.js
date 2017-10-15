cms.registerWidgetHandler('spacer', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'spacer';
	};

	this.getIcon = function(){
		return "fa-arrows-v";
	};

    this.getToolbarButtons = function(){

        var handler = this;

		return {
			options: {
                icon: "fa-arrows-v",
                click: function(regionId, widgetId){
					handler.openOptionsForm(regionId, widgetId);
				}
			}
		};

	};

	this.onCreateWidget = function(widget, regionId){

        var dom = this.dom(widget);

        $('<div/>').css('height', '20px').css('width', '20px').appendTo(dom);

		return widget;

	};

    this.applyOptions = function(widget, options){

        var dom = this.dom(widget);

        if (!options.size) { options.size = 20; }

        $('div', dom).css('height', Number(options.size)+'px').css('width', Number(options.size)+'px');

    };

});
