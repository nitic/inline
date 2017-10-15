function cmsAddCore(cms){

    cms.isCodeEditorLoaded = false;
    cms.hasUnsavedChanges = false;

    cms.formsLoaded = {};

    cms.langPhrases = {};

    cms.setChanges = function(){
        this.hasUnsavedChanges = true;
        $('.btn-save', this.panel).addClass('glow').find('i').removeClass('fa-check').addClass('fa-exclamation-circle');
    };

    cms.hasChanges = function(){
        return this.hasUnsavedChanges;
    };

    cms.noChanges = function(){
        this.hasUnsavedChanges = false;
        $('.btn-save', this.panel).removeClass('glow').find('i').removeClass('fa-exclamation-circle').addClass('fa-check');
    };

    cms.loadLang = function(set, callback){

        this.runModule('langs', 'loadLang', {
            lang: this.options.lang,
            set: set
        }, function(phrases){

            cms.langPhrases = phrases;

            if (typeof(callback)==='function'){
				callback();
			}

        });

    };

    cms.loadWidgetLang = function(handlerId, callback){

        this.runModule('langs', 'loadWidgetLang', {
            lang: this.options.lang,
            handler: handlerId
        }, function(phrases){

            if (typeof(callback)==='function'){
				callback(phrases);
			}

        });

    };

    cms.lang = function(phraseId, replacements){

        if (typeof(this.langPhrases[phraseId]) === 'undefined'){
            return phraseId;
        }

        var phrase = this.langPhrases[phraseId];

        if (typeof(replacements) !== 'undefined'){
            for (var id in replacements){
                phrase = phrase.replace(new RegExp('\{'+id+'\}', 'g'), replacements[id]);
            }
        }

        return phrase;

	};

    cms.getFrameWindow = function(){

        return window.frames[0];

    };

    cms.getLanguage = function(){
        return this.options.lang;
    };

    cms.getDefaultPageLanguage = function(){
        return this.options.defaultLang;
    };

	cms.runModule = function(module, action, params, callback){

        if (typeof(params) === 'undefined') {
            params = {};
        }

		params._module = module;
		params._action = action;

		$.post(this.options.backendUrl, params, function(result){

            if (result.success === false && result.error){
                cms.showMessageDialog(result.error, cms.lang('error'));
                return;
            }

            if (result.success === false && result.no_auth){
                cms.showConfirmationDialog(cms.lang('reauthConfirm'), function(){
                    location.href = '?edit';
                }, function(){
                    location.reload();
                });
                return;
            }

			if (typeof(callback) === 'function'){
				callback(result);
			}

		}, 'json');

	};

	cms.getModuleUrl = function(module, action, params){

        if (typeof(params) === 'undefined') { params = {}; }

        var query = $.extend({
            _module: module,
            _action: action
        }, params);

		return this.options.backendUrl + '?' + $.param(query);

	};

    cms.injectScript = function(url, callback){

        var doc = this.pageFrame.get(0);

        var head = doc.getElementsByTagName('body')[0];
		var script = doc.createElement('script');
		script.type = 'text/javascript';
		script.src = url;
		script.onreadystatechange = callback;
		script.onload = callback;
		head.appendChild(script);

    };

	cms.loadScript = function(url, callback){

        $.getScript(url, callback);

	};

    cms.loadScripts = function(urls, callback, noReverse){

        if (typeof(noReverse) === 'undefined' || noReverse === false){
            urls.reverse();
        }

        if (urls.length === 1){
            this.loadScript(urls.pop(), callback);
        } else {
            this.loadScript(urls.pop(), function(){
               cms.loadScripts(urls, callback, true);
            });
        }

    };

    cms.loadStylesheet = function(url, callback){
		var head = document.getElementsByTagName('head')[0];
		var link = document.createElement('link');
		link.rel = 'stylesheet';
		link.type = 'text/css';
		link.href = url;
		link.onreadystatechange = callback;
		link.onload = callback;
		head.appendChild(link);
    };

    cms.moveItemInArray = function (array, old_index, new_index) {
        if (new_index >= array.length) {
            var k = new_index - array.length;
            while ((k--) + 1) {
                array.push(undefined);
            }
        }
        array.splice(new_index, 0, array.splice(old_index, 1)[0]);
    };

    cms.buildPanel = function(){

        this.panel = $('#inlinecms-panel');

		this.panel.draggable({
            handle: ".title",
            iframeFix: true,
            stop: function(){
				cms.savePanelState();
            }
        });

        $('.title .tb-collapse', this.panel).on('click', function(e){

			e.preventDefault();
			$('.body', cms.panel).slideToggle(250, function(){
                cms.savePanelState();
            });
			$('i', this).toggleClass('fa-caret-up').toggleClass('fa-caret-down');
			return false;

        });

        $('.title', this.panel).on('dblclick', function(e){
            $('.tb-collapse', $(this)).click();
        });

    };

    cms.showLoadingIndicator = function(){

        if ($('.inlinecms-loading-indicator').length){
            $('.inlinecms-loading-indicator').show();
            return;
        }

        var indicator = $('<div></div>').addClass('inlinecms-loading-indicator');

        indicator.append('<i class="fa fa-spinner fa-pulse"></i>').show();

        $('body').append(indicator);

    };

    cms.hideLoadingIndicator = function(){

        $('.inlinecms-loading-indicator').hide();

    };

    cms.showMessageDialog = function(message, title){

        var messageHtml = message;

        if (typeof(message) === 'object'){
            messageHtml = $('<ul></ul>').addClass('messages-list');
            for (var i in message){
                var itemDom = $('<li></li>').html(message[i]);
                messageHtml.append(itemDom);
            }
        }

        var buttons = {};

        buttons[this.lang("ok")] = function(){
            $(this).dialog('close');
        };

		$('<div class="message-text inlinecms"></div>').append(messageHtml).dialog({
			title: title,
			modal: true,
			resizable: false,
            width: 350,
			buttons: buttons
		});

	};

    cms.showPromptDialog = function(message, title, onSubmit, defaultValue){

        var form = $('<div/>').addClass('message-prompt inlinecms');
        var prompt = $('<div/>').addClass('prompt').html(message);
        var input = $('<input/>').attr('type', 'text').val(defaultValue);

        var buttons = {};

        buttons[this.lang("ok")] = function(){
            onSubmit(input.val());
            $(this).dialog('close');
        };

        buttons[this.lang("cancel")] = function(){
            $(this).dialog('close');
        };

        form.append(prompt).append(input).dialog({
			title: title,
			modal: true,
			resizable: false,
            width: 350,
			buttons: buttons,
            open: function(){
                setTimeout(function(){
                    input.focus();
                }, 10);
            }

		});

    };

    cms.showConfirmationDialog = function(message, onConfirm, onCancel){

        var buttons = {};

        buttons[this.lang("yes")] = function(){
            if (typeof(onConfirm)==='function') { onConfirm(); }
            $(this).dialog('close');
        };

        buttons[this.lang("no")] = function(){
            if (typeof(onCancel)==='function') { onCancel(); }
            $(this).dialog('close');
        };

		$('<div class="message-text inlinecms"></div>').append(message).dialog({
			title: this.lang("confirmation"),
			modal: true,
			resizable: false,
            width:350,
			buttons: buttons,
            open:function () {
                $(this).closest('.ui-dialog').find(".ui-dialog-buttonset .ui-button:first").addClass("green");
                $(this).closest('.ui-dialog').find(".ui-dialog-buttonset .ui-button:last").addClass("red");
            }
		});

	};

    cms.openForm = function(options){

		var isFormDomLoaded = typeof(this.formsLoaded[options.id]) !== 'undefined';

		if (!isFormDomLoaded || options.cache === false){

            if (typeof(options.onCreate) !== 'function'){
                options.onCreate = false;
            }

			this.loadForm(options, function(){
				cms.showForm(options);
			});

			return;

		}

        this.hideLoadingIndicator();
		this.showForm(options);

	};

	cms.loadForm = function(options, loadCallback){

        this.showLoadingIndicator();

        var data = {};

        if (typeof(options.source.data) !== 'undefined'){ data = options.source.data; }

		this.runModule(options.source.module, options.source.action, data, function(response){

            cms.hideLoadingIndicator();

            $('#inlinecms-form-'+options.id).remove();
			var formDom = $('<div></div>').attr('id', 'inlinecms-form-'+options.id);
			formDom.addClass('inlinecms-options-form').addClass('inlinecms').hide().append(response.html);

            if (formDom.find('.tabs').length === 1){
                $('.tabs', formDom).tabs({
                    activate: function(){
                        if (typeof(options.onTabChange) === 'function'){
                            options.onTabChange(formDom);
                        }
                    }
                });
            }

			if (typeof(options.onCreate) === 'function'){
				options.onCreate(formDom);
			}

			$('body').append(formDom);
			cms.formsLoaded[options.id] = true;

			if (typeof(loadCallback) === 'function'){
				loadCallback();
			}

		});

	};

	cms.showForm = function(options){

		var form = $('#inlinecms-form-'+options.id);

        if (typeof(options.onBeforeShow) === 'function'){
            options.onBeforeShow(form);
        }

        if (form.find('.tabs').length === 1){
            $('.tabs ul li a', form).eq(0).click();
        }

        if (options.values !== false){

            $('*[name]', form).val('');
            $('select option:first-child', form).attr("selected", "selected");

            for(var key in options.values){
                var value = options.values[key];
                var input = $('*[name='+key+']', form);
                if (input.attr('type') != 'checkbox'){
                    input.val(value);
                } else {
                    input.prop('checked', value);
                }
            }

        }

		var buttons = {};

		buttons[options.buttons.ok] = function(){

			var dialogDom = $(this);
			var form = $('form', $(this).dialog());

            if (typeof(options.onBeforeSubmit) === 'function'){
                options.onBeforeSubmit(form);
            }

			var values = {};

			$('.field *[name]', form).each(function(){

                var input = $(this);
                var value;

                if (input.attr('type') != 'checkbox'){
                    value = input.val();
                } else {
                    value = input.prop('checked');
                }

                values[input.attr('name')] = value;

			});

			if (typeof(options.onValidate) === 'function'){
				options.onValidate(values, function(result){

					if (result.success === true){
						cms.submitForm(values, form, dialogDom, options);
						return;
					}

					cms.showMessageDialog(result.error, cms.lang("error"));

				});
			} else {
				cms.submitForm(values, form, dialogDom, options);
			}

		};

		buttons[cms.lang("cancel")] = function(){
			$(this).dialog('close');
            if (typeof(options.onCancel) === 'function'){
               options.onCancel(form);
            }
		};

        if (typeof(options.onShow) === 'function'){
            options.onShow(form, options.values);
        }

		form.dialog({
			title: options.title,
			modal: true,
			buttons: buttons,
			width: typeof(options.width) === 'undefined' ? 450 : options.width,
            minWidth: 300,
            position: {
                my: "center top",
                at: "center top+50",
                of: window
            },
            open:function () {

                $(this).closest('.ui-dialog').find(".ui-dialog-buttonset .ui-button:first").addClass("green");

                if (typeof(options.onAfterShow) === 'function'){
                    options.onAfterShow(form);
                }

            }
		});

	};

	cms.submitForm = function(values, form, dialogDom, options){

		if (typeof(options.onSubmit) === 'function'){
			options.onSubmit(values, form);
		}

		dialogDom.dialog('close');

	};

    cms.clearFormCache = function(id){

        delete this.formsLoaded[id];
        $('#inlinecms-form-'+id).remove();

    };

    cms.loadCodeEditor = function(callback){

        if (this.isCodeEditorLoaded) { callback(); return; }

        this.loadStylesheet(this.options.rootUrl + '/static/codemirror/lib/codemirror.css', function(){
            cms.loadScripts([
                cms.options.rootUrl + '/static/codemirror/lib/codemirror.js',
                cms.options.rootUrl + '/static/codemirror/mode/javascript/javascript.js',
                cms.options.rootUrl + '/static/codemirror/mode/css/css.js',
                cms.options.rootUrl + '/static/codemirror/mode/xml/xml.js',
            ], function(){

                cms.isCodeEditorLoaded = true;

                if (typeof(callback) === 'function') { callback(); }

            });
        });

    };

}