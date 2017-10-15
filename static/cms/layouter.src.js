function InlineCMSLayoutEditor(options){

    this.options = options;

    this.pageFrame;

	this.selection = {
		element: false,
		path: false
	};

    this.cursor = {};
    this.regions = {};
    this.menus = {};
    this.deletions = [];
    this.styles = {};

    this.blockTags = [
        'ARTICLE', 'ASIDE', 'DIV', 'FIELDSET', 'FIGURE', 'FOOTER',
        'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'HEADER', 'HGROUP', 'UL', 'LI',
        'OUTPUT', 'P', 'PRE', 'PROGRESS', 'SECTION', 'SPAN'
    ];

    this.createPathGenerator = function(){
		$.fn.getPath = function () {
			if (this.length != 1) return false;

			var path, node = this;
			while (node.length) {
				var realNode = node[0], name = realNode.localName;
				if (!name) break;

				name = name.toLowerCase();

				if (realNode.id) {
				    return name + '#' + realNode.id + (path ? '>' + path : '');
				} else if (realNode.className) {
				    name += '.' + realNode.className.split(/\s+/)[0];
				}

				var parent = node.parent(), siblings = parent.children(name.replace( /(:|\[|\]|,|\(|\))/g, "\\$1" ));

				if (siblings.length > 1) name += ':eq(' + parent.children().index(node) + ')';
				path = name + (path ? '>' + path : '');

				node = parent;
			}

			return path;
		};
	};

    this.restorePanelState = function(){

		var panelState = {};

		if (!localStorage.getItem("inlinecms-layouter-panel")){
			panelState = {
				position: {left: 50, top: 150},
				tab: '#tab-elements',
                expanded: true
			};
		} else {
			panelState = JSON.parse(localStorage.getItem("inlinecms-layouter-panel"))
		}

		this.panel.css(panelState.position);

        if (!panelState.expanded){
            $('.body', this.panel).hide();
            $('.title .tb-collapse i', this.panel).toggleClass('fa-caret-up').toggleClass('fa-caret-down');
        }

	};

	this.savePanelState = function(){
		localStorage.setItem("inlinecms-layouter-panel", JSON.stringify({
			position: this.panel.position(),
            expanded: $('.body:visible', this.panel).length
		}));
	};

    this.done = function(){

        var url = this.options.layouterUrl + '?' + $.param({done: 1});

        if (!this.hasChanges()){
            window.location.href = url;
            return;
        }

        this.showConfirmationDialog(this.lang('layoutSaveConfirm'), function(){
            cms.save(function(){
                window.location.href = url;
            });
        }, function(){
            window.location.href = url;
        });

    };

    this.save = function(callback){

        var selector = $('#layout-selector', this.panel);
        var button = $('#b-save-layout', this.panel);

        selector.prop('disabled', true);
        button.prop('disabled', true).find('i').removeClass('fa-check').addClass('fa-spinner').addClass('fa-spin');

        var jqueryVersion = 0;

        if ('$' in this.getFrameWindow()){
            if ('fn' in this.getFrameWindow().$){
                if ('jquery' in this.getFrameWindow().$.fn){
                    jqueryVersion = this.getFrameWindow().$.fn.jquery;
                }
            }
        }

        this.runModule('layouter', 'saveLayout', {
            layout: this.options.layout,
            regions: JSON.stringify(this.regions),
            menus: JSON.stringify(this.menus),
            deletions: JSON.stringify(this.deletions),
            styles: JSON.stringify(this.styles),
            jquery: jqueryVersion
        }, function(result){

            selector.prop('disabled', false);
            button.prop('disabled', false).find('i').removeClass('fa-spinner').addClass('fa-check').removeClass('fa-spin');

            cms.noChanges();

            if (typeof(callback) === 'function'){
                callback(result);
                return;
            }

            if (!cms.options.isWizard){ return; }

            var selectedLayoutIndex = $('option:selected', selector).index();
            var layoutsCount = $('option', selector).length;

            if (selectedLayoutIndex < layoutsCount-1){
                var nextLayout = $('option', selector).eq(selectedLayoutIndex+1).val();
                cms.goToLayout(nextLayout, true);
            } else {
                cms.done();
            }

        });

    };

    this.buildBlockToolbar = function(type, element, title, editCallback, deleteCallback){

        var label = $('<div/>').addClass('inline-label').addClass('inlinecms').html(title);


        if (element.data('global')){
            label.html(title + ' <i class="fa fa-chain"></i>');
        }

        var toolbar = $('<div/>').addClass('inline-toolbar').addClass('inlinecms');

        var buttons = {
            "options": {
                icon: "fa-pencil",
                title: this.lang("settings"),
                click: function(id){
                    editCallback(id);
                }
            },
            "style": {
                icon: "fa-expand",
                title: this.lang("sizes"),
                click: function(id, dom){
                    cms.cancelSelection();
                    cms.editSelectionStyle(dom);
                }
            },
            "delete": {
                icon: "fa-trash",
                title: this.lang("delete"),
                click: function(id){
                    deleteCallback(id);
                }
            }
        };

        if (type === 'menu'){
            delete buttons.style;
        }

        $.map(buttons, function(button, buttonId){

			var buttonDom = $('<div></div>').addClass('button').addClass('b-'+buttonId);
			buttonDom.attr('title', button.title);
			buttonDom.html('<i class="fa '+button.icon+'"></i>');

			toolbar.append(buttonDom);

			if (typeof(button.click) === 'function'){
				buttonDom.click(function(){
                    var dom = $(this).parents('.inlinecms-'+type).eq(0);
					var id = dom.data(type);
					button.click(id, dom);
				});
			}

			return button;

		});

        $('.inline-label', element).remove();
        $('.inline-toolbar', element).remove();

		element.append(label);
		element.append(toolbar);

    };

    this.buildRegionToolbar = function (id){

        var regionDom = $('[data-region="'+id+'"]', this.pageFrame);

        this.buildBlockToolbar('region', regionDom, id, function(){
            cms.editRegion(id);
        }, function(regionId){
            cms.deleteRegion(id);
        });

    };

    this.buildMenuToolbar = function (id){

        var menuDom = $('*[data-menu="'+id+'"]', this.pageFrame);

        this.buildBlockToolbar('menu', menuDom, id, function(){
            cms.editMenu(id);
        }, function(menuId){
            cms.deleteMenu(id);
        });

    };

    this.getElementStyle = function(element){

        if (!element){
            element = this.selection.element;
        }

        var styles = [
            'width', 'height',
            'paddingTop', 'paddingBottom', 'paddingLeft', 'paddingRight',
            'marginTop', 'marginBottom', 'marginLeft', 'marginRight'
        ];

        var style = {};

        var node = element.get(0);

        $.each(styles, function(index, styleName){
            style[styleName] = node.style[styleName].replace('px', '');
        });

        return style;

    };

	this.editSelectionStyle = function(element){

        if (!element){
            element = this.selection.element;
        }

        var originalValues = this.getElementStyle(element);

        this.openForm({
			id: 'style-edit',
            title: this.lang("styleSettings"),
			values: originalValues,
			source: {
				module: 'layouter',
				action: 'loadStyleForm'
			},
			buttons: {
				ok: cms.lang("save"),
			},
            onShow: function(form){
                $.each(originalValues, function(styleName, value){
                    var input = $('input[name='+styleName+']', form);
                    input.attr('placeholder', element.css(styleName));
                });
            },
			onSubmit: function(values){

                $.each(values, function(styleName, value){
                    if (value) {
                        if ($.isNumeric(value)){
                            value = Number(value);
                        }
                        element.css(styleName, value);
                    } else {
                        element.css(styleName, '');
                    }
                });

                if (element == cms.selection.element){
                    cms.selectElement(element);
                }

                var styleAttr = element.attr('style');
                var path = element.getPath();

                if (styleAttr){
                    cms.styles[path] = styleAttr;
                }

                if (!styleAttr && cms.styles[path]){
                    delete cms.styles[path];
                }

                cms.setChanges();

			}
		});

	};

	this.addMenu = function(){

        if (!this.isMenuSelected()) { return; }

        this.selectMenu();

        this.openForm({
			id: 'menu-add',
            title: cms.lang("menuCreate"),
			values: {},
			source: {
				module: 'layouter',
				action: 'loadMenuForm',
                data: {
                    mode: 'add',
                }
			},
			buttons: {
				ok: cms.lang("create"),
			},
            onCreate: function(form){

                var field = $('.f-menu-id', form);
                var select = $('select', field);
                var input = $('input', field);

                $('button', field).click(function(e){
                    e.preventDefault();
                    select.toggle();
                    input.toggle();
                    $('i', field).toggleClass('fa-navicon').toggleClass('fa-pencil');
                    if (input.is(':visible')){
                        input.val('').focus();
                    } else {
                        input.val(select.val());
                    }
                });

                select.change(function(){
                    input.val($(this).val());
                });

            },
            onShow: function(form){

                var field = $('.f-menu-id', form);
                var select = $('select', field);
                var input = $('input', field);

                if ($('option', select).length > 0) {
                    input.hide().val(select.val());
                    select.show();
                }

                $('i', field).removeClass('fa-navicon').addClass('fa-pencil');

                $('.f-path input', form).val(cms.selection.path);
                $('.f-items select', form).html('');
                cms.selection.element.children().each(function(){
                    var child = $(this);
                    var link = child.prop('tagName')==='A' ? child : child.children('a').eq(0);
                    var option = $('<option/>').val(child.index()).html(link.html());
                    $('.f-items select', form).append(option);
                });
            },
			onValidate: function(values, returnCallback){

                if (typeof(cms.menus[values.id]) !== 'undefined'){
                    returnCallback({
                        success: false,
                        error: cms.lang('menuIdUniqError')
                    });
                    return;
                }

				cms.runModule('layouter', 'validateMenu', values, function(result){
					returnCallback(result);
				});

			},
			onSubmit: function(values, form){

                cms.selection.element.addClass('inlinecms-menu');
                cms.selection.element.attr('data-menu', values.id);

                var menu = {
                    id: values.id,
                    active_item_index: values.active_item_index,
                    path: cms.selection.path
                };

                var select = $('.f-menu-id select', form);

                if ($('option[value="'+menu.id+'"]', select).length == 0){
                    $('<option/>').attr('value', menu.id).html(menu.id).prependTo(select);
                }

                cms.menus[menu.id] = menu;
                cms.buildMenuToolbar(menu.id);
                cms.cancelSelection();

                cms.setChanges();

			},
            onCancel: function(form){
                cms.cancelSelection();
            }
		});

	};

	this.editMenu = function(menuId){

        var menu = this.menus[menuId];

        if (!menu) { return; }

        var menuDom = $('*[data-menu="'+menu.id+'"]', this.pageFrame);

        this.openForm({
			id: 'menu-edit',
            title: cms.lang("menuSettings"),
			values: menu,
			source: {
				module: 'layouter',
				action: 'loadMenuForm',
                data: {
                    mode: 'edit',
                }
			},
			buttons: {
				ok: cms.lang("save"),
			},
            onCreate: function(form){
                var field = $('.f-menu-id', form);
                var select = $('select', field);
                var input = $('input', field);

                $('button', field).click(function(e){
                    e.preventDefault();
                    select.toggle();
                    input.toggle();
                    $('i', field).toggleClass('fa-navicon').toggleClass('fa-pencil');
                    if (input.is(':visible')){
                        input.focus();
                    } else {
                        select.val(input.val());
                    }
                });

                select.change(function(){
                    $('.f-menu-id input', form).val($(this).val());
                });

            },
            onBeforeShow: function(form){
                $('.f-items select', form).html('');
                menuDom.children().not('.inlinecms').each(function(){
                    var child = $(this);
                    var link = child.prop('tagName')==='A' ? child : child.children('a').eq(0);
                    var option = $('<option/>').val(child.index()).html(link.html());
                    $('.f-items select', form).append(option);
                });
            },
            onShow: function (form,values){
                $('.f-menu-id select', form).val(values.id);
            },
			onValidate: function(values, returnCallback){

                if (menu.id != values.id){
                    if (typeof(cms.menus[values.id]) !== 'undefined'){
                        returnCallback({
                            success: false,
                            error: cms.lang('menuIdUniqError')
                        });
                        return;
                    }
                }

				cms.runModule('layouter', 'validateMenu', values, function(result){
					returnCallback(result);
				});

			},
			onSubmit: function(values){

                menuDom.attr('data-menu', values.id);
                $('.inlinecms.inline-label', menuDom).html(values.id);

                if (values.id != menu.id){
                    delete cms.menus[menu.id];
                }

                cms.menus[values.id] = {
                    id: values.id,
                    active_item_index: values.active_item_index,
                    path: menu.path,
                };

                cms.buildMenuToolbar(values.id);

                cms.setChanges();

			}
		});

	};

    this.deleteMenu = function(menuId){

        var menu = this.menus[menuId];
        if (!menu) { return; }

        var menuDom = $('*[data-menu="'+menuId+'"]', this.pageFrame);

        this.showConfirmationDialog(this.lang('menuDeleteConfirm', {menu: menu.id}), function(){

            menuDom.removeAttr('data-menu');
            menuDom.removeClass('inlinecms-menu');

            $('.inlinecms', menuDom).remove();

            delete cms.menus[menuId];

            cms.setChanges();

        });

    };

	this.addRegion = function(){

        this.openForm({
			id: 'region-add',
            title: cms.lang("regionCreate"),
			values: {
                id: this.selection.element.attr('id'),
            },
			source: {
				module: 'layouter',
				action: 'loadRegionForm',
                data: {
                    mode: 'add',
                }
			},
			buttons: {
				ok: cms.lang("create"),
			},
            onShow: function(form){
                $('.f-path input', form).val(cms.selection.path);
            },
			onValidate: function(values, returnCallback){

                if (typeof(cms.regions[values.id]) !== 'undefined'){
                    returnCallback({
                        success: false,
                        error: cms.lang('regionIdUniqError')
                    });
                    return;
                }

				cms.runModule('layouter', 'validateRegion', values, function(result){
					returnCallback(result);
				});

			},
			onSubmit: function(values){
                var region = {
                    id: values.id,
                    path: cms.selection.path,
                    is_fixed: (values.type === 'fixed'),
                    is_collection: (values.type === 'collection'),
                    is_global: (values.global === 'yes'),
                    is_scan: values.is_scan,
                    type: values.type,
                    global: values.global
                };
                cms.selection.element.addClass('inlinecms-region');
                cms.selection.element.attr('data-region', values.id);
                if (region.is_fixed){
                    cms.selection.element.addClass('inlinecms-region-fixed');
                    cms.selection.element.attr('data-fixed', 'yes');
                }
                if (region.is_global){
                    cms.selection.element.attr('data-global', 'yes');
                }
                if (region.is_collection){
                    cms.selection.element.addClass('inlinecms-region-collection');
                }
                cms.regions[region.id] = region;
                cms.buildRegionToolbar(region.id);
                cms.cancelSelection();
                cms.setChanges();
			}
		});

	};

	this.editRegion = function(regionId){

        var region = this.regions[regionId];

        if (!region) { return; }

        var regionDom = $('*[data-region="'+region.id+'"]', this.pageFrame);

        this.openForm({
			id: 'region-edit',
            title: cms.lang("regionSettings"),
			values: region,
			source: {
				module: 'layouter',
				action: 'loadRegionForm',
                data: {
                    mode: 'edit',
                }
			},
			buttons: {
				ok: cms.lang("save"),
			},
			onValidate: function(values, returnCallback){

                if (values.id != region.id){
                    if (typeof(cms.regions[values.id]) !== 'undefined'){
                        returnCallback({
                            success: false,
                            error: cms.lang('regionIdUniqError')
                        });
                        return;
                    }
                }

				cms.runModule('layouter', 'validateRegion', values, function(result){
					returnCallback(result);
				});

			},
			onSubmit: function(values){
                var is_fixed = (values.type === 'fixed');
                var is_collection = (values.type === 'collection');
                var is_global = (values.global === 'yes');
                cms.regions[values.id] = {
                    id: values.id,
                    path: region.path,
                    is_fixed: is_fixed,
                    is_collection: is_collection,
                    is_global: is_global,
                    is_scan: values.is_scan,
                    type: values.type,
                    global: values.global
                };
                regionDom.attr('data-region', values.id);
                if (is_fixed) {
                    regionDom.data('fixed', true).addClass('inlinecms-region-fixed');
                } else {
                    regionDom.removeAttr('data-fixed').removeClass('inlinecms-region-fixed');
                }
                if (is_collection) {
                    regionDom.data('collection', true).addClass('inlinecms-region-collection');
                } else {
                    regionDom.removeAttr('data-collection').removeClass('inlinecms-region-collection');
                }
                if (is_global) {
                    regionDom.data('global', true);
                } else {
                    regionDom.removeAttr('data-global').data('global', false);
                }
                if (values.id != region.id){
                    delete cms.regions[region.id];
                }
                cms.buildRegionToolbar(values.id);
                cms.setChanges();
			}
		});

	};

    this.deleteRegion = function(regionId){

        var region = this.regions[regionId];
        if (!region) { return; }

        var regionDom = $('*[data-region="'+regionId+'"]', this.pageFrame);

        this.showConfirmationDialog(this.lang('regionDeleteConfirm', {region: region.id}), function(){

            regionDom.removeAttr('data-region').removeAttr('data-fixed').removeAttr('data-global');
            regionDom.removeClass('inlinecms-region').removeClass('inlinecms-region-fixed').removeClass('inlinecms-region-collection');

            $('.inlinecms', regionDom).remove();

            delete cms.regions[regionId];

            cms.setChanges();

        });

    };

    this.selectElement = function(element, cursorClass, isCheckBlock){

        if (!this.isLegitSelection(element)) { return; }

        if (typeof(isCheckBlock) === 'undefined'){
            isCheckBlock = true;
        }

        var tagName = element.prop('tagName');
        var parent = element.parent();

        if (this.blockTags.indexOf(tagName) < 0 && tagName !== 'A' && isCheckBlock){
            if (parent.prop('tagName') == 'BODY') { return; }
            this.selectElement(parent);
            return;
        }

        if (tagName == 'A'){
            if (!this.isMenuSelected(element)){
                this.selectElement(parent);
                return;
            }
            cursorClass = 'picker-menu';
        }

        this.selection = {
            element: element,
            path: element.getPath()
        };

        this.cursor.attr('class', 'inlinecms picker-cursor');

        if (typeof(cursorClass) === 'string'){
            this.cursor.addClass(cursorClass);
        }

        this.cursor.css({
            width: element.outerWidth(),
            height: element.outerHeight(),
            left: element.offset().left,
            top: element.offset().top
        }).show();

        $('#b-zoom-out', this.panel).prop('disabled', false);
        $('#b-zoom-in', this.panel).prop('disabled', false);
        $('#b-cancel', this.panel).prop('disabled', false);

        $('.path-hint', this.cursor).removeClass('bottom').html(this.selection.path);

        if ($('.path-hint', this.cursor).offset().top < 5){
            $('.path-hint', this.cursor).addClass('bottom');
        }

        this.toggleButtons();

    };

    this.selectMenu = function(element){

       this.selectElement(element, 'picker-menu');

    };

    this.onViewportResize = function(){

        if (!this.selection.element) { return; }

        this.selectElement(this.selection.element);

    };

    this.onDomClick = function(element){

        this.selectElement(element);

    };

    this.selectMenu = function(){

        var element = this.selection.element;

        if (element.parent().prop('tagName') === 'LI'){
            this.selectElement(element.parents('ul').eq(0), 'picker-menu', false);
        }

        if (element.siblings('a').length >= 1) {
            this.selectElement(element.parent(), 'picker-menu', false);
        }

    };

    this.isMenuSelected = function(element){

        if (typeof(element) == 'undefined'){
            element = this.selection.element;
        }

        if (!element) { return false; }

        if (element.prop('tagName') !== 'A') { return false; }

        if (element.parent().prop('tagName') === 'LI'){
            return true;
        }

        if (element.siblings('a').length >= 1) {
            return true;
        }

        return false;

    };

    this.isLegitSelection = function(element){

        var isLegit = true;

        if (element.is('.inlinecms')) { isLegit = false; }
        if (element.is('.inlinecms-region')) { isLegit = false; }
        if (element.is('.inlinecms-menu')) { isLegit = false; }
        if (element.parents('.inlinecms').length > 0) { isLegit = false; }
        if (element.parents('.inlinecms-region').length > 0) { isLegit = false; }
        if (element.find('.inlinecms-region').length > 0){ isLegit = false; }
        if (element.parents('.inlinecms-menu').length > 0){ isLegit = false; }
        if (element.find('.inlinecms-menu').length > 0){ isLegit = false; }

        return isLegit;

    };

    this.cancelSelection = function(){

        $('.pane-menu', this.panel).show();
        $('.pane-selection', this.panel).hide();

        this.selection.element = false;
        this.selection.path = false;

        this.cursor.hide();

        this.toggleButtons();

    };

    this.deleteSelection = function(){

        this.showConfirmationDialog(this.lang('deleteSelectionConfirm'), function(){

            var element = cms.selection.element;

            cms.deletions.push({
                path: element.getPath()
            });

            cms.cancelSelection();

            element.remove();

            cms.setChanges();

        });

    };

    this.goToLayout = function (layout, isWizard){

        var params = {layout: layout};

        if (typeof(isWizard) !== 'undefined') { params.wizard = 1; }

        var url = this.options.layouterUrl + '?' + $.param(params);

        if (!this.hasChanges()){
            window.location.href = url;
            return;
        }

        this.showConfirmationDialog(this.lang('layoutSaveConfirm'), function(){
            cms.save(function(){
                window.location.href = url;
            });
        }, function(){
            window.location.href = url;
        });

    };

    this.updateLayout = function(){

        cms.showLoadingIndicator();

        this.runModule('layouter', 'updateLayout', {
            layout: this.options.layout,
            regions: JSON.stringify(this.regions),
            menus: JSON.stringify(this.menus),
            deletions: JSON.stringify(this.deletions),
            styles: JSON.stringify(this.styles),
            jquery: typeof(this.getFrameWindow().$.fn.jquery)==='string' ? this.getFrameWindow().$.fn.jquery : 0
        }, function(result){

            cms.goToLayout(cms.options.layout);

            cms.hideLoadingIndicator();

        });

    };

    this.zoomOut = function(){

        if (!this.selection.element) { return; }

        if (!this.selection.element.parent().length) { return; }

        this.selectElement(this.selection.element.parent().eq(0));

    };

    this.zoomIn = function(){

        if (!this.selection.element) { return; }

        var nextElement;

        if (this.selection.element.children().length) {
            nextElement = this.selection.element.children().eq(0);
        } else if (this.selection.element.siblings().length) {
            nextElement = this.selection.element.siblings().eq(0);
        } else {
            return;
        }

        this.selectElement(nextElement);

    };

    this.toggleButtons = function(){

        this.contextMenu.hide();

        var isSelection = this.selection.element !== false;
        var isBlock = isSelection && this.selection.element.css('display') != 'inline';
        var isMenu = this.isMenuSelected();

        $('.i-add-region', this.contextMenu).toggleClass('disabled', !isBlock);
        $('.i-add-menu', this.contextMenu).toggleClass('disabled', !isMenu);

        $('.i-zoom-out', this.contextMenu).toggleClass('disabled', !isSelection);
        $('.i-zoom-in', this.contextMenu).toggleClass('disabled', !isSelection);
        $('.i-delete', this.contextMenu).toggleClass('disabled', !isSelection);
        $('.i-cancel', this.contextMenu).toggleClass('disabled', !isSelection);

    };

    this.bindControls = function(){

        $('body', this.pageFrame).on('keyup', function(event){
            if (event.which === 27){
                cms.cancelSelection();
                cms.contextMenu.hide();
            }
        });

        $('li i', this.contextMenu).on('click', function(event){
            $(this).parent('li').click();
        });

        $('li', this.contextMenu).on('click', function(event){
            console.log('click');
            if ($(this).is('.disabled')){
                event.stopImmediatePropagation();
                return false;
            }
        });

        $('.i-add-region', this.contextMenu).on('click', function(){
            cms.addRegion();
            cms.contextMenu.hide();
        });

        $('.i-add-menu', this.contextMenu).on('click', function(){
            cms.addMenu();
            cms.contextMenu.hide();
        });

        $('.i-zoom-out', this.contextMenu).on('click', function(){
            cms.zoomOut();
            cms.contextMenu.hide();
        });

        $('.i-zoom-in', this.contextMenu).on('click', function(){
            cms.zoomIn();
            cms.contextMenu.hide();
        });

        $('.i-style', this.contextMenu).on('click', function(){
            cms.editSelectionStyle();
            cms.contextMenu.hide();
        });

        $('.i-delete', this.contextMenu).on('click', function(){
            cms.deleteSelection();
            cms.contextMenu.hide();
        });

        $('.i-cancel', this.contextMenu).on('click', function(){
            cms.cancelSelection();
            cms.contextMenu.hide();
        });

        $('#b-save-layout', this.panel).on('click', function(){
            cms.save();
        });

        $('#b-done', this.panel).on('click', function(){
            cms.done();
        });

        $('#layout-selector', this.panel).on('change', function(){
            var newLayout = $(this).val();
            cms.goToLayout(newLayout);
        });

        $('#update-layout', this.panel).on('click', function(){

            cms.showConfirmationDialog(cms.lang('layoutUpdateConfirm', {layout: cms.options.layout}), function(){
                cms.updateLayout();
            });

        });

    };

    this.offSelfEvents = function(){
        $('.inlinecms *').off('click').off('mouseenter').off('mouseleave');
        $('.inlinecms').off('click').off('mouseenter').off('mouseleave');
    };

    this.initStructure = function(){

        $('*[data-region]', this.pageFrame).each(function(){

            var regionDom = $(this);

            var region = {
                id: regionDom.data('region'),
                path: regionDom.getPath(),
                is_fixed: regionDom.attr('data-fixed') !== undefined,
                is_global: regionDom.attr('data-global') !== undefined,
                is_collection: regionDom.attr('data-collection') !== undefined,
                type: 'content',
                global: 'no'
            };

            if (region.is_fixed) { region.type = 'fixed'; }
            if (region.is_collection) { region.type = 'collection'; }
            if (region.is_global) { region.global = 'yes'; }

            cms.regions[region.id] = region;

            regionDom.addClass('inlinecms-region');

            if (region.is_fixed){
                regionDom.addClass('inlinecms-region-fixed');
            }

            if (region.is_collection){
                regionDom.addClass('inlinecms-region-collection');
            }

            cms.buildRegionToolbar(region.id);

        });

        $('*[data-menu]', this.pageFrame).each(function(){

            var menuDom = $(this);

            var active_item_index = 0;

            for (var index=0; index < menuDom.children().length; index++){
                var child = menuDom.children().eq(index);
                if (child.attr('data-selected') !== undefined){
                    active_item_index = index;
                    break;
                }
            }

            var menu = {
                id: menuDom.data('menu'),
                active_item_index: active_item_index,
                path: menuDom.getPath(),
            };

            cms.menus[menu.id] = menu;

            menuDom.addClass('inlinecms-menu');
            cms.buildMenuToolbar(menu.id);

        });

    };

    this.initUI = function(){

        $('body *', this.pageFrame).off('click');

        this.cursor = $('<div/>').addClass('inlinecms').addClass('picker-cursor').hide();
        this.cursor.append( $('<div/>').addClass('path-hint').addClass('inlinecms') );
        $('body', this.pageFrame).append(this.cursor);

        this.contextMenu = $('#inlinecms-context-menu', this.pageFrame);

        $('body *', this.pageFrame).on('click', function(event){
            if (event.ctrlKey) { return; }
            event.preventDefault();
            event.stopPropagation();
            cms.onDomClick($(event.target));
            cms.contextMenu.hide();
        }).on('selectstart', function(){
            return false;
        });

        this.offSelfEvents();

        this.cursor.on('contextmenu', function(event){

            var offset = 10;

            var pageWidth = $('body').width();
            var pageHeight = $('body').height();

            var x = event.pageX;
            var y = event.pageY;

            if (x + cms.contextMenu.width() > pageWidth-offset){
                x = x - cms.contextMenu.width() - 10;
            }

            if (y + cms.contextMenu.height() > pageHeight-offset){
                y = y - cms.contextMenu.height() - 10;
            }

            cms.contextMenu.css({
                left: x,
                top: y
            }).show().mousemove();
            return false;
        });

        this.cursor.on('mouseup', function(event){
            event.preventDefault();
            event.stopPropagation();
            if (event.which === 1){
                cms.contextMenu.hide();
                cms.zoomOut();
            }
            return false;
        });

        this.buildPanel();
        this.restorePanelState();

        this.bindControls();

        this.panel.show();

        $(window).on('resize', function(){
            cms.onViewportResize();
        });

    };

    $(function(){

        cmsAddCore(cms);

        cms.showLoadingIndicator();

        cms.createPathGenerator();

        cms.loadLang(['shared', 'client'], function(){

            $('#page-frame').attr('src', cms.options.editorUrl).load(function(){

                cms.pageFrame = $('#page-frame').contents();

                cms.initUI();
                cms.initStructure();
                cms.hideLoadingIndicator();

            });

        });

    });

}

