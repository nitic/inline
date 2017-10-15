cms.registerWidgetHandler('code', new function() {

    InlineWidget.apply(this, arguments);

	this.getName = function(){
		return 'code';
	};

	this.getIcon = function(){
		return "fa-code";
	};

    this.getToolbarButtons = function(){

        var handler = this;

		return {
			options: {
                icon: "fa-code",
                click: function(regionId, widgetId){
					handler.openOptionsForm(regionId, widgetId);
				}
			}
		};

	};

    this.getOptionsFormSettings = function(){

        var handler = this;

        return {
            width: 650,
            onCreate: function(form){

                cms.loadCodeEditor(function(){

                    $('.f-html textarea', form).data('editor', CodeMirror.fromTextArea($('.f-html textarea', form).get(0), {
                        mode: {name: 'xml', htmlMode: true},
                        theme: 'material'
                    }));

                    $('.f-js textarea', form).data('editor', CodeMirror.fromTextArea($('.f-js textarea', form).get(0), {
                        mode: 'javascript',
                        theme: 'material'
                    }));

                    $('.f-css textarea', form).data('editor', CodeMirror.fromTextArea($('.f-css textarea', form).get(0), {
                        mode: 'css',
                        theme: 'material'
                    }));

                });

            },
            onBeforeSubmit: function(form){

                $('.f-html textarea', form).val( $('.f-html textarea', form).data('editor').getValue() );
                $('.f-js textarea', form).val( $('.f-js textarea', form).data('editor').getValue() );
                $('.f-css textarea', form).val( $('.f-css textarea', form).data('editor').getValue() );

            },
            onShow: function(form){

                if (!cms.isCodeEditorLoaded) { return; }

                $('textarea', form).each(function(){

                    var textarea = $(this);
                    var editor = $(this).data('editor');

                    if (!editor) { return; }

                    var value = textarea.val();

                    editor.setValue(value);

                });

            },
            onAfterShow: function(form){
                $('.CodeMirror:visible', form).each(function(i, el){
                    el.CodeMirror.refresh();
                });
            },
            onTabChange: function(form){
                $('.CodeMirror:visible', form).each(function(i, el){
                    el.CodeMirror.refresh();
                });
            }
        };

    };

	this.onCreateWidget = function(widget, regionId){

        this.openOptionsForm(regionId, widget.id);

		return widget;

	};

    this.applyOptions = function(widget, options, form){

        console.log('apply');

        var dom = this.dom(widget);
        dom.empty();

        if (options.html){
            $('<span/>').addClass('code-label').html('<i class="fa fa-code"></i> HTML').appendTo(dom);
        }
        if (options.js){
            $('<span/>').addClass('code-label').html('<i class="fa fa-code"></i> JS').appendTo(dom);
        }
        if (options.css){
            $('<span/>').addClass('code-label').html('<i class="fa fa-code"></i> CSS').appendTo(dom);
        }
        if (options.php){
            $('<span/>').addClass('code-label').html('<i class="fa fa-code"></i> PHP').appendTo(dom);
        }

    };

});
