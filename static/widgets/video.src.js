cms.registerWidgetHandler('video', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'video';
	};

	this.getIcon = function(){
		return "fa-youtube-play";
	};

    this.getToolbarButtons = function(){

        var handler = this;

		return {
			options: {
				icon: 'fa-youtube-play',
                click: function(regionId, widgetId){
					handler.openOptionsForm(regionId, widgetId);
				}
			}
		};

	};

    this.getOptionsFormSettings = function(){

        var handler = this;

        return {
            onValidate: function(values, returnCallback){
				handler.runBackend('validate', values, function(result){
					returnCallback(result);
				});
			}
        };

    };

	this.onCreateWidget = function(widget, regionId){

        this.openOptionsForm(regionId, widget.id);

		return widget;

	};

    this.applyOptions = function(widget, options, form){

        cms.showLoadingIndicator();

        var dom = this.dom(widget);

        this.runBackend('getVideoCode', options, function(result){
            dom.empty().append(result.html);
            cms.hideLoadingIndicator();
        });

    };

});
