cms.registerWidgetHandler('form', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'form';
	};

	this.getIcon = function(){
		return "fa-envelope-o";
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

                $('.fields-list', form).sortable({
                    handle: '.drag-handle'
                });

                $('.f-email-type select', form).on('change', function(){
                    $('.f-email', form).toggle( $(this).val() == 'custom' );
                }).change();

                form.on('click', '.actions .b-mandatory', function(e){
                    e.preventDefault();
                    $(this).toggleClass('active');
                });

                form.on('click', '.actions .b-delete', function(e){
                    e.preventDefault();
                    $(this).parents('.form-field').remove();
                });

                $('.f-add button', form).click(function(e){
                    e.preventDefault();
                    var list = $('.fields-list', form);
                    $('.field-template', form).clone().removeClass('field-template').addClass('form-field').appendTo(list);
                });

            },
            onShow: function(form, options){

                $('.f-email-type select', form).change();

                var list = $('.fields-list', form);
                $('.form-field', list).remove();

                if (!options || !options.fields) { return; }

                $.each(options.fields, function(index, field){

                    var item = $('.field-template', form).clone().removeClass('field-template').addClass('form-field').appendTo(list);

                    $('.field-title', item).val(field.title);
                    $('.field-type', item).val(field.type);

                    if (field.isMandatory){
                        $('.b-mandatory', item).addClass('active');
                    }

                    list.append(item);

                });

            }
        };

    };

	this.onCreateWidget = function(widget, regionId){

        this.openOptionsForm(regionId, widget.id);

		return widget;

	};

    this.onInitWidget = function(widget, regionId){
        var handler = this;
    };

    this.applyOptions = function(widget, options, form){

        cms.showLoadingIndicator();

        var dom = this.dom(widget);
        
        options.fields = [];

        $('.fields-list .form-field', form).each(function(index){

            var field = $(this);
            var title = $('input.field-title', field).val();

            if (!title) { return; }

            var type = $('select.field-type', field).val();
            var isMandatory = $('.b-mandatory', field).hasClass('active');

            options.fields.push({
                type: type,
                title: title,
                isMandatory: isMandatory
            });

        });

        this.runBackend('buildForm', {id: widget.domId, options: JSON.stringify(options)}, function(result){
            dom.empty().append(result.html);
            cms.hideLoadingIndicator();
        });

        return options;

    };

});
