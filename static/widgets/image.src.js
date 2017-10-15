cms.registerWidgetHandler('image', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'image';
	};

	this.getIcon = function(){
		return "fa-picture-o";
	};

    this.getToolbarButtons = function(){

        var handler = this;

		return {
			options: {
				icon: 'fa-picture-o',
                click: function(regionId, widgetId){
					handler.openOptionsForm(regionId, widgetId);
				}
			}
		};

	};

    this.openOptionsForm = function(regionId, widgetId){

        var options = cms.getWidgetOptions(regionId, widgetId);
        var handler = this;

        cms.showImageDialog(options, function(image, newOptions, form){
            handler.saveOptions(regionId, widgetId, newOptions, form);
            handler.applyImage(cms.getWidget(regionId, widgetId), image);
        });

    };

	this.onCreateWidget = function(widget, regionId){

        this.openOptionsForm(regionId, widget.id);

		return widget;

	};

    this.applyImage = function(widget, image){

        var dom = this.dom(widget);

        dom.empty();

        $(image).appendTo(dom);

    };

});
