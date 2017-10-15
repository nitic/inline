cms.registerWidgetHandler('gallery', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'gallery';
	};

	this.getIcon = function(){
		return "fa-th-large";
	};

    this.getToolbarButtons = function(){

        var handler = this;

		return {
			options: {
				icon: 'fa-th-large',
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
                var button = $('.file-button', form);

                form.on('click', '.actions .b-rename', function(e){
                    e.preventDefault();
                    var img = $(this).parents('.item').find('img');
                    cms.showPromptDialog(handler.lang('galleryImageEnterTitle'), handler.lang('galleryImageTitle'), function(newTitle){
                        img.attr('alt', newTitle);
                    }, img.attr('alt'));
                });

                form.on('click', '.actions .b-delete', function(e){
                    e.preventDefault();
                    $(this).parents('.item').remove();
                });

                $('input', field).fileupload({
                    sequentialUploads: true, 
                    dataType: 'json',
                    add: function(e, data){

                        var thumbInputW = $('.t-width', form);
                        var thumbInputH = $('.t-height', form);
                        var thumbInputS = $('.t-square', form);

                        var params = {
                            w: thumbInputW.val()? thumbInputW.val() : 150,
                            h: thumbInputH.val()? thumbInputH.val() : 150,
                            s: thumbInputS.prop('checked')? 1 : 0,
                        };

                        data.url = cms.getModuleUrl('uploader', 'uploadAndResize', params);
                        data.submit();

                    },
                    submit: function () {

                        $('input', field).hide();

                        $('i', button).removeClass('fa-plus').addClass('fa-spinner fa-spin');
                        $('span', button).text(cms.lang('uploading'));
                        $(button).addClass('disabled').prop('disabled', true);

                    },
                    done: function(e, data){

                        handler.addImagesToList($('.images-list', form), data.result.images);

                    },
                    always: function(e, data) {

                        $('input', field).show();

                        $('i', button).removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-plus');
                        $('span', button).text($('span', button).data('title'));
                        $(button).removeClass('disabled').prop('disabled', false);

                    }
                });

            },
            onShow: function(form, options){

                var list = $('.images-list', form);

                $('.item', list).remove();

                if (options && options.images){
                    handler.addImagesToList(list, options.images);
                }

            }
        };

    };

	this.onCreateWidget = function(widget, regionId){

        this.openOptionsForm(regionId, widget.id);

		return widget;

	};

    this.addImagesToList = function(list, images){

        $.each(images, function(index, image){

            var item = $('.item-template', list).clone().show().removeClass('item-template').addClass('item');

            $('img', item).attr('src', image.thumb_url).data('url', image.url).attr('alt', image.title);

            list.append(item);

        });

        list.sortable();

    };

    this.applyOptions = function(widget, options, form){

        var dom = this.dom(widget);
        dom.empty();

        options.images = [];

        var gallery = $('<div/>').addClass('inlinecms-gallery');

        if (options.open_in == 'lightbox'){
            gallery.addClass('lightbox');
        }

        if (options.style){
            gallery.addClass(options.style);
        }

        if (options.is_center){
            gallery.addClass('centered');
        }

        $('.images-list .item', form).each(function(){

            var img = $(this).find('img');
            var url = img.data('url');
            var thumb_url = img.attr('src');
            var title = img.attr('alt');

            options.images.push({
                url: url,
                thumb_url: thumb_url,
                title: title
            });

            var wrap = $('<div/>').addClass('image');
            var wrapLink = $('<a/>').attr('href', url).attr('target', '_blank');
            var image = $('<img/>').attr('src', thumb_url);

            if (options.is_titles){
                wrapLink.attr('title', title);
                image.attr('alt', title);
            }

            wrapLink.append(image);
            wrap.append(wrapLink);

            gallery.append(wrap);

        });

        dom.append(gallery);

        return options;

    };

});
