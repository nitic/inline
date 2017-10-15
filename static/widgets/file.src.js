cms.registerWidgetHandler('file', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'file';
	};

	this.getIcon = function(){
		return "fa-download";
	};

    this.getToolbarButtons = function(){

        var handler = this;

		return {
			options: {
                click: function(regionId, widgetId){
					handler.openOptionsForm(regionId, widgetId);
				}
			}
		};

	};

    this.getOptionsFormSettings = function(){

        var handler = this;

        return {
            onCreate: function(form){

                var field = $('.f-upload', form);
                var inputTitle = $('.f-title input', form);
                var inputUrl = $('.f-url input', form);
                var inputName = $('.f-name input', form);
                var inputSize = $('.f-size input', form);

                $('.f-uploaded .filename a', form).click(function(){
                    $('.f-uploaded', form).hide();
                    $('.f-upload', form).show();
                    inputTitle.val('');
                    inputUrl.val('');
                    inputName.val('');
                    inputSize.val('');
                    handler.runBackend('delete', {url: inputUrl.val()});
                });

                $('input', field).fileupload({
                    url: cms.getModuleUrl('uploader', 'upload'),
                    dataType: 'json',
                    submit: function () {
                        $('input', field).hide();
                        $('<div/>')
                            .addClass('inlinecms-uploading')
                            .html('<i class="fa fa-spinner fa-spin"></i> '+cms.lang('uploading'))
                            .insertAfter($('input', field));
                    },
                    always: function(e, data) {

                        $('input', field).show();
                        $('.inlinecms-uploading').remove();

                        if (!data.result.success){
                            if (data.result.error){
                                cms.showMessageDialog(data.result.error, cms.lang('error'));
                            }
                            return;
                        }

                        var uploadedName = data.result.name + ' (' + data.result.size_formatted + ')';

                        $('.f-uploaded', form).find('span').html(uploadedName).end().show();
                        $('.f-upload', form).hide();

                        if (!inputTitle.val()) { inputTitle.val(data.result.name); }
                        inputUrl.val(data.result.url);
                        inputName.val(data.result.name);
                        inputSize.val(data.result.size_formatted);

                    }
                });

            },
            onShow: function(form, options){
                if (options && options.name){
                    $('.f-uploaded', form).find('span').html(options.name).end().show();
                    $('.f-upload', form).hide();
                } else {
                    $('.f-uploaded', form).hide();
                    $('.f-upload', form).show();
                }
            }
        };

    };

	this.onCreateWidget = function(widget, regionId){

        this.openOptionsForm(regionId, widget.id);

		return widget;

	};

    this.applyOptions = function(widget, options){

        var dom = this.dom(widget);
        dom.empty();

        if (!options.url) { return; }

        $('<a/>').html(options.title).attr('href', options.url).appendTo(dom);

        if (options.is_size){
            $('<span/>').html(options.size).appendTo(dom);
        }

    };

});
