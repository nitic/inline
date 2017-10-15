function InlineCMS(values){

    this.options = values;

    this.pageFrame;
    this.widgetHandlers = {};
	this.page = {};
	this.menus = {};

    this.registerWidgetHandler = function(id, handler){

		handler.getTitle = function(){
			return cms.lang("widgetTitle_" + this.getName());
		};

        this.widgetHandlers[id] = handler;

    };


	this.initWidgetHandlers = function(){

		for(var handlerId in this.widgetHandlers){

			var handler = this.widgetHandlers[ handlerId ];

            handler.init();

		}

	};

	this.initWidgets = function(){

		for (var regionId in this.page.widgets){
			var region = this.page.widgets[regionId];
			for (var index in region){

				var widget = region[index];

				widget.domId = 'inlinecms-widget-' + regionId + widget.id;

                if ($('#'+widget.domId, this.pageFrame).length == 0) { continue; }

				var handler = this.widgetHandlers[widget.handler];

                handler.initWidget(widget, regionId, function(widget){
                    cms.buildWidgetToolbar($('#'+widget.domId, cms.pageFrame), cms.widgetHandlers[widget.handler]);
                });

			}
		}

	};

	this.initRegions = function(){

		$('.inlinecms-region', this.pageFrame).each(function(){

			var region = $(this);

			var dropZone = $('<div></div>').addClass('drop-helper').addClass('inlinecms');
			dropZone.html('<i class="fa fa-plus-circle"></i>');

			region.append(dropZone);

            if (region.hasClass('inlinecms-region-fixed')) { return; }

			region.droppable({
				accept: ".inlinecms-widget-element",
				over: function(){
					$('.drop-helper', this).show();
				},
				out: function(){
					$('.drop-helper', this).hide();
				},
				drop: function( event, ui ) {
					$('.drop-helper', this).hide();
					cms.addWidget(region, ui.draggable.data('id'));
				}
			});

			region.sortable({
                handle: '.b-move',
                update: function( event, ui ){
                    cms.reorderWidgets(region.data('region-id'));
                }
            });

		});

	};

    this.initCollections = function(){

        $('*[data-collection]', this.pageFrame).addClass('inlinecms-collection').each(function(){

            var collection = $(this);
            var collectionId = $(this).data('collection');

            $(this).children().each(function(itemId){

                var item = $(this);

                item.addClass('inlinecms-widget');
                item.data('item-id', itemId);

                item.html('<div class="inlinecms-content">' + item.html() + '</div>');

                cms.widgetHandlers.text.appendEditor(collectionId+'-'+itemId, $('.inlinecms-content', item));

                cms.buildCollectionToolbar(item);

            });

            collection.sortable({
                handle: '.b-move',
            });

        });

    };

	this.getMaxWidgetId = function (regionId){

		var maxId = 0;

		for (var index in this.page.widgets[regionId]){

			var widget = this.page.widgets[regionId][index];

			if (widget.id > maxId){
				maxId = widget.id;
			}

		}

		return maxId;

	};

	this.addWidget = function (regionDom, handlerId){

		var regionId = regionDom.data('region-id');
		var widgetId = this.getMaxWidgetId(regionId)+1;

		var widget = {
			id: widgetId,
			handler: handlerId,
			content: '',
			domId: 'inlinecms-widget-' + regionId + widgetId,
			options: []
		};

		var dom = $('<div></div>')
					.attr('id', widget.domId)
					.addClass('inlinecms-widget')
					.addClass('inlinecms-widget-'+handlerId)
                    .data('id', widgetId);

		dom.append('<div class="inlinecms-content"></div>');

		$('.drop-helper', regionDom).before(dom);

		var handler = this.widgetHandlers[ widget.handler ];

        handler.createWidget(regionId, widget, function(widget){
            cms.buildWidgetToolbar(dom, handler);
            cms.page.widgets[regionId].push(widget);
        });

        this.setChanges();

	};

	this.save = function(callback) {

		this.savePage(function(){
            cms.saveMenusOrdering(function(){
                cms.noChanges();
                if (typeof(callback) === 'function'){
                    callback();
                }
            });
        });

	};

    this.savePage = function(callback){

        for (var regionId in this.page.widgets){
			var region = this.page.widgets[regionId];
			for (var index in region){

				var widget = region[index];

				widget.domId = 'inlinecms-widget-' + regionId + widget.id;

				var content = $('#'+widget.domId+' .inlinecms-content', this.pageFrame).html();

				var handler = this.widgetHandlers[ widget.handler ];

				if (typeof(handler.getContent) === 'function'){
					content = handler.getContent(widget, content);
				}

				delete widget.domId;

				this.page.widgets[regionId][index].content = content;

			}
		}

        var collections = {};

        $('.inlinecms-collection', this.pageFrame).each(function(){

            var collection = $(this);
            var collectionId = collection.data('collection');

            collections[collectionId] = [];

            collection.children().each(function(){

                var itemId = $(this).data('item-id');
                var item = $(this).clone().removeClass('inlinecms-widget');

                var content = cms.widgetHandlers.text.getEditorContent(collectionId+'-'+itemId);

                item.html(content);

                collections[collectionId].push(item[0].outerHTML);

            });

        });

		this.runModule('pages', 'savePage', {
			page_uri: this.page.uri,
			lang: this.page.lang,
			widgets: JSON.stringify(this.page.widgets),
			collections: JSON.stringify(collections),
			count: this.page.widgetsCount
		}, function(result){

            if (!result.success){
                cms.showMessageDialog(result.error, cms.lang('error'));
                return;
            }

			if (typeof(callback) === 'function'){
				callback(result);
			}

		});

    };

    this.saveMenusOrdering = function(callback){

        var menusOrdering = {};

        for(var menuId in this.menus){

            var menu = this.menus[menuId];
            var ordering = [];

            for (var itemId in menu){
                ordering.push(menu[itemId].id);
                menu[itemId].id = itemId;
            }

            menusOrdering[menuId] = ordering.join(',');

        }

        this.runModule('menus', 'saveMenusOrdering', {lang: this.page.lang, ordering: menusOrdering}, function(result){
            if (typeof(callback) === 'function'){
                callback();
            }
        });

    };

    this.deleteWidget = function(regionId, widgetId){

        var widgetDomId = 'inlinecms-widget-' + regionId + widgetId;
		var widgets = this.page.widgets[regionId];

        this.showConfirmationDialog(this.lang("widgetDeleteConfirm"), function(){

            for (var index in widgets){
                if (widgets[index].id == widgetId){
                    widgets.splice(index, 1);
                    $('#'+widgetDomId, cms.pageFrame).fadeOut(500, function(){
                        $(this).remove();
                    });
                    break;
                }
            }

            cms.setChanges();

        });

    };

    this.reorderWidgets = function(regionId){

        var newOrder = [];
        var newRegion = [];
        var oldRegion = this.page.widgets[regionId];

        $('.inlinecms-region[data-region-id="'+regionId+'"] .inlinecms-widget', this.pageFrame).each(function(){
           newOrder.push($(this).data('id'));
        });

        for (var index in newOrder){

            var widgetId = newOrder[index];
            var widget;

            for (var oldIndex in oldRegion){
                widget = oldRegion[oldIndex];
                if (widget.id === widgetId){
                    break;
                }
            }

            newRegion.push(widget);

        }

        this.page.widgets[regionId] = newRegion;

        this.setChanges();

    };

    this.reorderMenu = function(menuId, oldIndex, newIndex){

        this.moveItemInArray(this.menus[menuId], oldIndex, newIndex);

        this.setChanges();

    };

	this.getWidget = function(regionId, widgetId){

		for (var index in this.page.widgets[regionId]){
            if (this.page.widgets[regionId][index].id == widgetId){
               return this.page.widgets[regionId][index];
            }
        }

		return false;

	};

	this.getWidgetOptions = function(regionId, widgetId){

		var widget = this.getWidget(regionId, widgetId);
		return widget.options;

	};

	this.setWidgetOptions = function(regionId, widgetId, options){

		for (var index in this.page.widgets[regionId]){
            if (this.page.widgets[regionId][index].id == widgetId){
               this.page.widgets[regionId][index].options = options;
            }
        }

        this.setChanges();

	};

    this.restorePanelState = function(){

		var panelState = {};

		if (!localStorage.getItem("inlinecms-panel")){
			panelState = {
				position: {left: 50, top: 150},
				tab: '#tab-elements',
                expanded: true
			};
		} else {
			panelState = JSON.parse(localStorage.getItem("inlinecms-panel"))
		}

		this.panel.css(panelState.position);

		var a = $('#tabs a[href='+panelState.tab+']', this.panel);

		$('#tabs .active', this.panel).removeClass('active');
		$('#tabs '+a.attr('href'), this.panel).show();

		a.parent('li').addClass('active');

        if (!panelState.expanded){
            $('.body', this.panel).hide();
            $('.title .tb-collapse i', this.panel).toggleClass('fa-caret-up').toggleClass('fa-caret-down');
        }

	};

	this.savePanelState = function(){

        var activeTab = $('#tabs .active', this.panel).length > 0 ?
                        $('#tabs .active a', this.panel).attr('href') :
                        $('#tabs a', this.panel).eq(0).attr('href');

		localStorage.setItem("inlinecms-panel", JSON.stringify({
			position: this.panel.position(),
			tab: activeTab,
            expanded: $('.body:visible', this.panel).length
		}));
	};

    this.getPageUrl = function(pageUri, lang){

        if (typeof(lang) === 'undefined'){
			lang = this.page.lang;
		}

        var urlParts = [];

        urlParts.push(this.options.rootUrl);

		if (lang != this.getDefaultPageLanguage()){
			urlParts.push(lang);
		}

		if (pageUri != 'index' && pageUri != '/'){
			urlParts.push(pageUri);
		}

		var url = urlParts.join("/");

		if (url == '') { url = '/'; }

        return url;

    };

    this.reloadPage = function(){

        this.goToPage(this.page.uri);

    };

	this.goToPage = function(pageUri, lang){

		if (typeof(lang) === 'undefined'){
			lang = this.page.lang;
		}

		var url = this.getPageUrl(pageUri, lang);

        if (this.hasChanges()){
            this.showConfirmationDialog(this.lang('pageSaveConfirm'), function(){
                cms.save(function(){
                    window.location.href = url;
                });
            }, function(){
                cms.noChanges();
                window.location.href = url;
            });
        } else {
            window.location.href = url;
        }

	};

	this.buildUI = function(){

		this.buildPanel();

        $('.title .lang select', this.panel).on('change', function(){

            var lang = $(this).val();
            var isNew = $('option:selected', this).data('new') === "yes";

            if (isNew){
                cms.addPage({
                    lang: lang,
                    uri: cms.page.uri,
                    mode: 'copy'
                });
                $(this).val(cms.page.lang);
                return;
            }

            cms.goToPage(cms.options.pageUri, lang);

        });

		$('#tabs > ul > li > a', this.panel).on('click', function(){
			var a = $(this);
			$('#inlinecms-panel #tabs ul li').removeClass('active');
			a.parent('li').addClass('active');
			$('#inlinecms-panel #tabs .tab').hide();
			$('#inlinecms-panel #tabs '+a.attr('href')).show();
			cms.savePanelState();
			return false;
		});

        $('.s-layouts', this.panel).on('click', function(){
			cms.editLayouts();
        });

        $('.s-code', this.panel).on('click', function(){
			cms.editGlobalCode();
        });

        $('.s-user', this.panel).on('click', function(){
			cms.editUser();
        });

        $('.s-mail', this.panel).on('click', function(){
			cms.editMail();
        });

		$('.btn-save', this.panel).on('click', function(){

			var button = $(this);

			button.addClass('saving');
			$('i', button).removeClass('fa-check').addClass('fa-spinner').addClass('fa-spin');
			button.prop('disabled', true);
			$('.btn-save-and-exit', this.panel).prop('disabled', true);

			cms.save(function(){
				button.removeClass('saving');
				$('i', button).addClass('fa-check').removeClass('fa-spinner').removeClass('fa-spin');
				button.prop('disabled', false);
				$('.btn-save-and-exit', this.panel).prop('disabled', false);
			});

		});

		$('.btn-save-and-exit', this.panel).on('click', function(){

			var button = $(this);

			button.addClass('saving');
			$('i', button).removeClass('fa-sign-out').addClass('fa-spinner').addClass('fa-spin');
			button.prop('disabled', true);
			$('.btn-save', this.panel).prop('disabled', true);

			cms.save(function(){
				window.location.href="?exit";
			});

		});

		$('.btn-exit', this.panel).on('click', function(){

			window.location.href="?exit";

		});

		this.buildWidgetsList();
		this.buildPagesList();
		this.buildMenusList();

		this.restorePanelState();

        this.panel.show();

        window.onbeforeunload = function(){
            if (!cms.hasChanges()){ return; }
            return cms.lang('pageOutConfirm');
        };

	};

	this.buildWidgetsList = function(){

		var widgetsList = $('#tab-elements .list ul' , this.panel);

		for(var i in this.options.widgetsList){

            var widgetId = this.options.widgetsList[i];

			var title = this.widgetHandlers[widgetId].getTitle();
			var icon = this.widgetHandlers[widgetId].getIcon();

			var item = $('<li></li>').attr('data-id', widgetId).addClass('inlinecms-widget-element');
			item.html('<i class="fa '+icon+'"></i>');
            item.attr('title', title);
			item.tooltip({
				track: true,
				show: false,
				hide: false
			});

			widgetsList.append(item);

		}

		$('li', widgetsList).draggable({
			helper: "clone",
            iframeFix: true
		});

	};

    this.reloadPagesList = function(callback){

        this.runModule('pages', 'getPagesTree', {lang: this.page.lang}, function(treeJson){

            $('#inlinecms-pages-tree').jstree(true).settings.core.data = treeJson;
            $('#inlinecms-pages-tree').jstree(true).refresh(true);

            if (typeof(callback) === 'function'){
                callback();
            }

		});

    };

	this.buildPagesList = function(){

		this.runModule('pages', 'getPagesTree', {lang: this.page.lang}, function(treeJson){

			$('#inlinecms-pages-tree').on('dblclick', '.jstree-anchor', function(e){

				var node = $('#inlinecms-pages-tree').jstree(true).get_node($(this));

				if (node.data.type == 'page'){
					cms.goToPage(node.data.url);
				}

			}).on('select_node.jstree', function (e, selected) {

                var isPage = selected.node.data.type === 'page';
                var isIndex = selected.node.data.url === 'index';

                $('#tab-pages .buttons .page-only', cms.panel).prop('disabled', !isPage);
                $('#tab-pages .buttons .btn-delete', cms.panel).prop('disabled', isIndex);

			}).on('state_ready.jstree', function (e, data) {

				var currentPageNodeId = 'n-' + cms.page.uri.replace(new RegExp('/', 'g'), '-');

				$('#inlinecms-pages-tree').jstree(true).deselect_all();
				$('#inlinecms-pages-tree').jstree(true).select_node(currentPageNodeId);
				$('#inlinecms-pages-tree').jstree(true).open_node(currentPageNodeId);

			}).jstree({
				plugins : ['wholerow', 'state'],
				core : {
					data : treeJson,
					multiple: false,
					expand_selected_onload: true,
                    check_callback: true
				}
			});

		});

		$('#tab-pages .btn-create', this.panel).on('click', function(){
			cms.addPage();
		});

		$('#tab-pages .btn-open', this.panel).on('click', function(){
			cms.goToPage(cms.getSelectedPageUri());
		});

		$('#tab-pages .btn-settings', this.panel).on('click', function(){
			cms.editPage();
		});

		$('#tab-pages .btn-delete', this.panel).on('click', function(){
			cms.deletePage();
		});

	};

	this.buildMenusList = function(){

        $('#tab-menus .buttons .item-only', cms.panel).prop('disabled', true);

		this.runModule('menus', 'getMenusTree', {lang: this.page.lang}, function(result){

            for (var menuId in result.menus){
                for (var itemId in result.menus[menuId]){
                    result.menus[menuId][itemId].id = itemId;
                }
            }

            cms.menus = result.menus;

			$('#inlinecms-menus-tree').on('select_node.jstree', function (e, selected) {

                var isMenu = selected.node.parent === '#';

                $('#tab-menus .buttons .item-only', cms.panel).prop('disabled', isMenu);

			}).jstree({
				plugins : ['wholerow', 'state'],
				core : {
					data : result.tree,
					multiple: false,
					expand_selected_onload: true,
                    check_callback: true
				}
			});

		});

        $('#tab-menus .btn-create', this.panel).on('click', function(){
            cms.addMenuItem();
        });

        $('#tab-menus .btn-settings', this.panel).on('click', function(){
            cms.editMenuItem();
        });

        $('#tab-menus .btn-delete', this.panel).on('click', function(){
			cms.deleteMenuItem();
		});

		$('#tab-menus .btn-move-up', this.panel).on('click', function(){

            var tree = $.jstree.reference('#inlinecms-menus-tree'),
                selectedNode = tree.get_selected(true)[0],
                parentNode = tree.get_node(tree.get_parent(selectedNode)),
                position = $('#'+selectedNode.id).index();

            if (position <= 0) { return; }

            var menuId = parentNode.data.menu;

            tree.move_node(selectedNode, parentNode.id, position - 1, function(){

                var menuDom = $('*[data-menu="'+menuId+'"]', cms.pageFrame);
                if (!menuDom) { return; }

                menuDom.children().eq(position).insertBefore(
                    menuDom.children().eq(position-1)
                );

                cms.reorderMenu(menuId, position, position-1);

            });

		});

		$('#tab-menus .btn-move-down', this.panel).on('click', function(){

            var tree = $.jstree.reference('#inlinecms-menus-tree'),
                selectedNode = tree.get_selected(true)[0],
                parentNode = tree.get_node(tree.get_parent(selectedNode)),
                position = $('#'+selectedNode.id).index(),
                maxPosition = parentNode.children.length;

            if (position === maxPosition) { return; }

            var menuId = parentNode.data.menu;

            tree.move_node(selectedNode, parentNode, position + 2, function(){

                var menuDom = $('*[data-menu="'+menuId+'"]', cms.pageFrame);
                if (!menuDom) { return; }

                menuDom.children().eq(position).insertAfter(
                    menuDom.children().eq(position+1)
                );

                cms.reorderMenu(menuId, position, position+1);

            });

		});

	};

	this.buildWidgetToolbar = function(widgetDom, handler){

		if (typeof(handler.toolbarButtons) === 'undefined') {

			var defaultToolbarButtons = {
				"options": {
					icon: "fa-wrench",
					title: this.lang("widgetOptions")
				},
				"move": {
					icon: "fa-arrows",
					title: this.lang("widgetMove")
				},
				"delete": {
					icon: "fa-trash",
					title: this.lang("widgetDelete"),
					click: function(regionId, widgetId){
						cms.deleteWidget(regionId, widgetId);
					}
				}
			};

			var buttons = {};

			if (typeof(handler.getToolbarButtons) === 'function'){
				buttons = handler.getToolbarButtons();
			}

			handler.toolbarButtons = $.extend(true, {}, defaultToolbarButtons, buttons);

		}

		var toolbar = $('<div />').addClass('inline-toolbar').addClass('inlinecms');
        var isFixedRegion = widgetDom.parents('.inlinecms-region-fixed').length > 0;

		$.map(handler.toolbarButtons, function(button, buttonId){

			if (button === false) { return button; }
			if (buttonId == 'move' && isFixedRegion) { return button; }
			if (buttonId == 'delete' && isFixedRegion) { return button; }

			var buttonDom = $('<div></div>').addClass('button').addClass('b-'+buttonId);
			buttonDom.attr('title', button.title);
			buttonDom.html('<i class="fa '+button.icon+'"></i>');

			toolbar.append(buttonDom);

			if (typeof(button.click) === 'function'){
				buttonDom.click(function(){
					var regionId = $(this).parents('.inlinecms-region').eq(0).data('region-id');
					var widgetId = $(this).parents('.inlinecms-widget').eq(0).data('id');
					button.click(regionId, widgetId);
				});
			}

			return button;

		});

		widgetDom.append(toolbar);

	};

	this.buildCollectionToolbar = function(dom){

        var buttons = {
            "move": {
                icon: "fa-arrows",
                title: this.lang("widgetMove")
            },
            "clone": {
                icon: "fa-copy",
                title: this.lang("widgetClone"),
                click: function(button){
                    var parent = button.parents('.inlinecms-widget');
                    var clone = parent.clone();
                    var id = parent.siblings().length + 1;
                    var collectionId = button.parents('.inlinecms-collection').data('collection');
                    clone.data('item-id', id);
                    cms.widgetHandlers.text.appendEditor(collectionId+'-'+id, $('.inlinecms-content', clone));
                    $('.inline-toolbar', clone).remove();
                    cms.buildCollectionToolbar(clone);
                    clone.insertAfter(parent);
                }
            },
            "delete": {
                icon: "fa-trash",
                title: this.lang("widgetDelete"),
                click: function(button){
                    cms.showConfirmationDialog(cms.lang("widgetDeleteConfirm"), function(){
                        button.parents('.inlinecms-widget').fadeOut(500, function(){
                            $(this).remove();
                        });
                    });
                }
            }
        };

		var toolbar = $('<div />').addClass('inline-toolbar').addClass('inlinecms');

		$.map(buttons, function(button, buttonId){

			var buttonDom = $('<div></div>').addClass('button').addClass('b-'+buttonId);
			buttonDom.attr('title', button.title);
			buttonDom.html('<i class="fa '+button.icon+'"></i>');

			toolbar.append(buttonDom);

			if (typeof(button.click) === 'function'){
				buttonDom.click(function(){
					button.click($(this));
				});
			}

			return button;

		});

		dom.append(toolbar);

	};

    this.getSelectedPageUri = function(){

        var selectedNode = $('#inlinecms-pages-tree').jstree(true).get_selected(true)[0];

        if (!selectedNode) { return false; }

        return selectedNode.data.url;

    };

    this.getMenuSelection = function(){

        var selection = {};

        var tree = $('#inlinecms-menus-tree').jstree(true);
        var selectedNode = tree.get_selected(true)[0];

        if (!selectedNode) { return false; }

        var parentNode = tree.get_node(tree.get_parent(selectedNode));
        var menuId = parentNode.data.menu;

        for (var id in this.menus[menuId]){
            var item = this.menus[menuId][id];
            if (item.id == selectedNode.data.id){
                selection.menuId = menuId;
                selection.item = item;
                selection.node = selectedNode;
                selection.position = $('#'+selectedNode.id).index();
                return selection;
            }
        }

        return false;

    };

    this.showSelectPageDialog = function(callback){

        var buttons = {};

        buttons[cms.lang('ok')] = function(){

            var selectedNode = $('#inlinecms-menu-item-pages-tree').jstree(true).get_selected(true)[0];

            if (selectedNode){
                if (typeof(callback) === 'function'){
                    callback(selectedNode);
                }
            }

            $(this).dialog('close');

        };

        $('#inlinecms-menu-item-pages').dialog({
            title: cms.lang('pageSelect'),
            modal: true,
            resizable: false,
            buttons: buttons
        });

    };

    this.onMenuItemFormCreate = function(form){

        $('#inlinecms-menu-item-pages', form).hide();

        $('.f-menu-id select', form).on('change', function(){
            $('.f-node-id input', form).val($('option:selected', $(this)).data('node-id'));
        });

        $('.f-type select', form).on('change', function(){
            var type = $(this).val();
            $('.f-page', form).hide();
            $('.f-url', form).hide();
            $('.f-' + type, form).show();
        });

        $('.f-page a', form).on('click', function(e){
            e.preventDefault();
            cms.showSelectPageDialog(function(node){
               var title = node.data.url == 'index' ? cms.lang('homePage') : node.data.url;
               $('.f-page a', form).html(title);
               $('.f-page input', form).val(node.data.url);
            });
        });

        $('#inlinecms-menu-item-pages-tree', form).on('state_ready.jstree', function (e, data) {

            $('#inlinecms-menu-item-pages-tree').jstree(true).open_all();

        }).jstree({
            plugins : ['wholerow', 'state'],
            core : {
                data : $('#inlinecms-pages-tree').jstree(true).get_json(),
                multiple: false,
                expand_selected_onload: true,
            }
        });

    };

    this.onMenuItemFormShow = function(form){
        $('.f-menu-id select', form).change();
        $('.f-type select', form).change();
    };

    this.addMenuItem = function(presetValues){

		var defaults = {
            page: this.page.uri,
		};

        var values = $.extend({}, defaults, presetValues);

		this.openForm({
			id: 'menu-item-add',
            title: cms.lang("menuItemCreate"),
			values: values,
			source: {
				module: 'menus',
				action: 'loadMenuItemForm',
                data: {
                    mode: 'add',
                    lang: this.page.lang
                }
			},
			buttons: {
				ok: cms.lang("create"),
			},
			onCreate: function(form){
                cms.onMenuItemFormCreate(form);
			},
            onShow: function(form){
                cms.onMenuItemFormShow(form);
                var title = values.page == 'index' ? cms.lang('homePage') : values.page;
                $('.f-page a').html(title);
                $('.f-page input').val(values.page);
            },
			onValidate: function(values, returnCallback){
                values.lang = cms.page.lang;
                values.is_new = true;
				cms.runModule('menus', 'validateMenuItem', values, function(result){
					returnCallback(result);
				});
			},
			onSubmit: function(values){
                values.lang = cms.page.lang;
                values.current_uri = cms.page.uri;
				cms.runModule('menus', 'createMenuItem', values, function(result){
                    $('*[data-menu="'+values.menu+'"]', cms.pageFrame).append(result.item_html);
                    var tree = $('#inlinecms-menus-tree').jstree(true);
                    var menuNode = tree.get_node('#n-'+values.menu_node_id);
                    tree.create_node(menuNode, result.node);
                    cms.menus[values.menu].push(result.item);
				});
			}
		});

	};

    this.editMenuItem = function(){

        var selection = this.getMenuSelection();

        if (!selection) { return; }

        var values = {
            menu: selection.menuId,
            id: selection.item.id,
            title: selection.item.title,
            type: selection.item.type,
            page: selection.item.type == 'page' ? selection.item.url : this.page.uri,
            url: selection.item.type == 'url' ? selection.item.url : '',
            target: selection.item.target
        };

        cms.openForm({
            id: 'menu-item-edit',
            title: cms.lang("menuItemSettings"),
            values: values,
            source: {
				module: 'menus',
				action: 'loadMenuItemForm',
                data: {
                    mode: 'edit',
                    lang: this.page.lang
                }
			},
            buttons: {
                ok: cms.lang("save"),
            },
            onCreate: function(form){
                cms.onMenuItemFormCreate(form);
			},
            onShow: function(form){
                cms.onMenuItemFormShow(form);
                var title = values.page == 'index' ? cms.lang('homePage') : values.page;
                $('.f-page a', form).html(title);
                $('.f-page input', form).val(values.page);
            },
			onValidate: function(values, returnCallback){
                values.lang = cms.page.lang;
                values.is_new = true;
				cms.runModule('menus', 'validateMenuItem', values, function(result){
					returnCallback(result);
				});
			},
			onSubmit: function(values){
                values.lang = cms.page.lang;
                values.current_uri = cms.page.uri;
				cms.runModule('menus', 'editMenuItem', values, function(result){

                    $('*[data-menu="'+values.menu+'"]', cms.pageFrame).children().eq(selection.position).after(result.item_html).remove();

                    var tree = $('#inlinecms-menus-tree').jstree(true);
                    var selectedNode = tree.get_selected(true)[0];

                    tree.rename_node(selection.node, values.title);
                    selection.node.data = result.node.data;

                    selection.item.title = values.title;
                    selection.item.type = values.type;
                    selection.item.url = values.type == 'page' ? values.page : values.url;
                    selection.item.target = values.target;

				});
			}
        });

    };

    this.deleteMenuItem = function(){

        var selection = this.getMenuSelection();

        if (!selection) { return; }

        var message = this.lang('menuItemDeleteConfirm', {item: selection.item.title});

        this.showConfirmationDialog(message, function(){

            cms.showLoadingIndicator();

            cms.runModule('menus', 'deleteMenuItem', {
                lang: cms.page.lang,
                menu: selection.menuId,
                id: selection.item.id
            }, function(result){

                cms.hideLoadingIndicator();

                $('*[data-menu="'+selection.menuId+'"]', cms.pageFrame).children().eq(selection.position).remove();

                var tree = $('#inlinecms-menus-tree').jstree(true);

                tree.delete_node(selection.node);

            });

        });

    };

    this.onPageFormCreate = function(form){
        $('.f-uri input', form).on('keyup', function(){
            var uri = $(this).val();
            while (uri.charAt(0) === '/'){
                uri = uri.substr(1);
            }
            $('.f-uri .uri', form).html(uri);
        });
        $('.f-lang select', form).on('change', function(){
            var lang = $(this).val();
            if (lang == cms.getDefaultPageLanguage()){
                lang = '';
            } else {
                lang += '/';
            }
            $('.f-uri .lang', form).html(lang);
        });
    };

    this.onPageFormShow = function(form){
        $('.f-uri input', form).keyup();
        $('.f-lang select', form).change();
    };

	this.addPage = function(presetValues){

		var defaults = {
			lang: this.page.lang,
			layout: this.page.layout,
            mode: 'default'
		};

        var values = $.extend({}, defaults, presetValues);

		this.openForm({
			id: 'page-add',
            title: cms.lang("pageCreate"),
			values: values,
			source: {
				module: 'pages',
				action: 'loadPageForm',
                data: {
                    mode: 'add'
                }
			},
			buttons: {
				ok: cms.lang("create"),
			},
			onCreate: function(form){
				cms.onPageFormCreate(form);
			},
            onShow: function(form){
                cms.onPageFormShow(form);
            },
			onValidate: function(values, returnCallback){
                values.is_new = true;
				cms.runModule('pages', 'validatePage', values, function(result){
					returnCallback(result);
				});
			},
			onSubmit: function(values){
                values.source_uri = cms.page.uri;
                values.source_lang = cms.page.lang;
				cms.runModule('pages', 'createPage', values, function(result){
					cms.goToPage(values.uri, values.lang);
				});
			}
		});

	};

    this.editPage = function(){

        var selectedUri = this.getSelectedPageUri();

        if (!selectedUri) { return; }

        this.showLoadingIndicator();

        this.runModule('pages', 'loadPageJson', {
			page_uri: selectedUri,
			lang: this.options.pageLang
		}, function(page){

            cms.hideLoadingIndicator();

            var values = {
                title: page.title,
                uri: page.uri === 'index' ? '/' : page.uri,
                lang: page.lang,
                layout: page.layout,
                keywords: page.meta.keywords,
                description: page.meta.description,
            };

            var currentUri = values.uri;

			cms.openForm({
                id: 'page-edit',
                title: cms.lang("pageSettings"),
                values: values,
                source: {
                    module: 'pages',
                    action: 'loadPageForm',
                    data: {
                        mode: 'edit'
                    }
                },
                buttons: {
                    ok: cms.lang("save"),
                },
                onCreate: function(form){
                    cms.onPageFormCreate(form);
                },
                onShow: function(form){
                    cms.onPageFormShow(form);
                    $('.f-uri .lang', form).html('');
                    if (values.lang != cms.getDefaultPageLanguage()){
                        $('.f-uri .lang', form).html(values.lang + '/');
                    }
                    $('.f-uri input', form).prop('disabled', page.uri === 'index');
                },
                onValidate: function(values, returnCallback){
                    values.current_uri = currentUri;
                    cms.runModule('pages', 'validatePage', values, function(result){
                        returnCallback(result);
                    });
                },
                onSubmit: function(values){

                    values.current_uri = currentUri;
                    var isUriChanged = currentUri != values.uri;
                    var isCurrentPage = cms.page.uri=='index' ? currentUri == '/' : currentUri == cms.page.uri;

                    if (isUriChanged){
                        $('#inlinecms-pages-tree').animate({opacity: 0.35}, 300);
                    }

                    cms.showLoadingIndicator();

                    cms.runModule('pages', 'editPage', values, function(result){

                        if (isCurrentPage){
                            cms.goToPage(values.uri);
                            return;
                        }

                        if (isUriChanged){
                            cms.reloadPagesList(function(){

                                var currentPageNodeId = 'n-' + values.uri.replace(new RegExp('/', 'g'), '-');

                                $('#inlinecms-pages-tree').jstree(true).deselect_all();

                                $('#inlinecms-pages-tree').animate({opacity: 1}, 300, function(){
                                    $('#inlinecms-pages-tree').jstree(true).select_node(currentPageNodeId);
                                });

                                cms.hideLoadingIndicator();

                            });
                            return;
                        }

                        cms.hideLoadingIndicator();

                    });

                }
            });

		});

    };

    this.deletePage = function(){

        var selectedNode = $('#inlinecms-pages-tree').jstree(true).get_selected(true)[0];
        var uri = selectedNode.data.url;

        if (uri === 'index') { return; }

        var fullUri = this.getPageUrl(uri);
        var isCurrentPage = uri === this.page.uri;

        var isPage = selectedNode.data.type === 'page';
        var isChildren = selectedNode.children.length > 0;

        var message;

        if (isPage && !isChildren){ message = this.lang("pageDeleteConfirm", {page: fullUri}); }
        if (isPage && isChildren){ message = this.lang("pageDeleteChildsConfirm", {page: fullUri}); }
        if (!isPage){ message = this.lang("pageDeleteFolderConfirm", {folder: fullUri}); }

        if (!message){ return; }

        this.showConfirmationDialog(message, function(){

            $('#inlinecms-pages-tree').animate({opacity: 0.35}, 300);
            cms.showLoadingIndicator();

            cms.runModule('pages', 'deletePage', {uri: uri, lang: cms.page.lang, is_page: isPage?1:0}, function(result){

                if (isCurrentPage){
                    cms.goToPage('index');
                    return;
                }

                cms.reloadPagesList(function(){
                    $('#inlinecms-pages-tree').jstree(true).deselect_all();
                    $('#inlinecms-pages-tree').animate({opacity: 1}, 300);
                    cms.hideLoadingIndicator();
                });

            });

        });

    };

    this.editLayouts = function(){

        cms.openForm({
            id: 'layouts-edit',
            title: cms.lang("settingsLayouts"),
            values: false,
            cache: false,
            source: {
                module: 'settings',
                action: 'loadLayoutsForm'
            },
            buttons: {
                ok: cms.lang("save"),
            },
            onCreate: function(form){
                $('.f-layout-name', form).hide();
                $('.f-layout-open', form).hide();
                $('.f-layout-file select', form).change(function(){
                    var file = $(this).val();
                    var name = file.replace(/([_\-]+)|(\.html)|(\.htm)/g, ' ').replace(/(\s+)/g, ' ');
                    name = name.charAt(0).toUpperCase() + name.slice(1);
                    $('.f-layout-name', form).toggle(file != '');
                    $('.f-layout-name input', form).val(name);
                    $('.f-layout-open', form).toggle(file != '');
                });
            },
            onValidate: function(values, returnCallback){
                console.log(values);
                cms.runModule('settings', 'validateLayouts', values, function(result){
                    returnCallback(result);
                });
            },
            onSubmit: function(values){
                cms.runModule('settings', 'saveLayouts', values, function(result){
                    if (result.url){
                        window.location.href = result.url;
                        return;
                    }
                    cms.clearFormCache('page-add');
                });
            }
        });

    };

    this.editGlobalCode = function(){

        this.showLoadingIndicator();

        this.runModule('settings', 'loadGlobalCode', {}, function(result){

            cms.hideLoadingIndicator();

            var html = result.code.html;

            cms.showSourceDialog({html: html}, function(html){

                cms.showLoadingIndicator();

                cms.runModule('settings', 'saveGlobalCode', {html: html}, function(){
                    cms.hideLoadingIndicator();
                });

            }, {
                title: cms.lang('settingsGlobalCode'),
                hint: cms.lang('globalCodeHint')
            });

        });

    };

    this.editUser = function(){

        cms.openForm({
            id: 'user-edit',
            title: cms.lang("settingsUser"),
            values: false,
            cache: false,
            source: {
                module: 'settings',
                action: 'loadUserForm'
            },
            buttons: {
                ok: cms.lang("save"),
            },
            onCreate: function(form){

            },
            onShow: function(form){

            },
            onValidate: function(values, returnCallback){
                console.log(values);
                cms.runModule('settings', 'validateUser', values, function(result){
                    returnCallback(result);
                });
            },
            onSubmit: function(values){
                cms.runModule('settings', 'saveUser', values);
            }
        });

    };

    this.editMail = function(){

        this.openForm({
            id: 'mail-edit',
            title: this.lang("settingsMail"),
            values: false,
            cache: false,
            source: {
                module: 'settings',
                action: 'loadMailForm'
            },
            buttons: {
                ok: cms.lang("save"),
            },
            onSubmit: function(values){
                cms.runModule('settings', 'saveMail', values);
            }
        });

    };

    this.showImageDialog = function(values, onSubmit){

        this.openForm({
            id: 'image',
            title: this.lang("imageSettings"),
            values: values,
            source: {
                module: 'editor',
                action: 'loadImageForm'
            },
            buttons: {
                ok: this.lang("apply"),
            },
            onCreate: function(form){

                var field = $('.f-image', form);
                var inputUrl = $('.f-url input', form);

                $('.t-resize', form).change(function(){
                   $('.fields-small', form).toggle($(this).prop('checked'));
                });

                $('input', field).fileupload({
                    dataType: 'json',
                    url: cms.getModuleUrl('uploader', 'uploadImage'),
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

                        inputUrl.val(data.result.url);

                    }
                });

            },
            onShow: function(form){
                $('.t-resize', form).change();
            },
            onSubmit: function(values, form){

                if (typeof(onSubmit) !== 'function'){ return; }

                var image = $('<img/>').attr('src', values.url);

                if (values.style){
                    image.addClass(values.style);
                }

                if (values.align == 'center'){
                    image.css({
                        margin: '0 auto'
                    });
                } else if (values.align) {
                    image.attr('align', values.align);
                }

                if (values.title){
                    image.attr('alt', values.title);
                }

                if (values.link_url){
                    var link = $('<a/>').attr('href', values.link_url).append(image);
                    if (values.title){ link.attr('title', values.title); }
                    onSubmit(link.get(0), values, form);
                    return;
                }

                onSubmit(image.get(0), values, form);

            }
        });

    };

    this.showSourceDialog = function(values, onSubmit, options){

        if (typeof(options)==='undefined'){ options = {}; }

        var title = (typeof(options.title)==='undefined') ? 'HTML' : options.title;
        var hint = (typeof(options.hint)==='undefined') ? false : options.hint;

        this.openForm({
            id: 'source',
            title: title,
            width: 650,
            values: values,
            source: {
                module: 'editor',
                action: 'loadSourceForm'
            },
            buttons: {
                ok: this.lang("apply"),
            },
            onCreate: function(form){

                cms.loadCodeEditor(function(){

                    $('.f-html textarea', form).data('editor', CodeMirror.fromTextArea($('.f-html textarea', form).get(0), {
                        mode: {name: 'xml', htmlMode: true},
                        theme: 'material',
                        lineWrapping: false
                    }));

                });

            },
            onShow: function(form){

                if (!hint){
                    $('.hint', form).hide();
                } else {
                    $('.hint', form).html(hint).show();
                }

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
            onBeforeSubmit: function(form){

                $('.f-html textarea', form).val( $('.f-html textarea', form).data('editor').getValue() );

            },
            onSubmit: function(values, form){

                if (typeof(onSubmit) !== 'function'){ return; }

                onSubmit(values.html);

            }
        });

    };

    $(function(){

        cmsAddCore(cms);

        cms.showLoadingIndicator();

        cms.loadLang(['shared', 'client', 'widgets'], function(){

            cms.runModule('pages', 'loadPageJson', {
                page_uri: cms.options.pageUri,
                lang: cms.options.pageLang
            }, function(result){

                cms.page = result;
                cms.buildUI();

                $('#page-frame').attr('src', cms.options.editorUrl).load(function(){

                    cms.pageFrame = $('#page-frame').contents();
                    $('a[target!=_blank]', cms.pageFrame).attr('target', '_top');

                    cms.initWidgetHandlers();
                    cms.initWidgets();
                    cms.initRegions();
                    cms.initCollections();
                    cms.hideLoadingIndicator();

                });

            });

        });

    });

}

function InlineWidget(){

	this.isOptionsFormLoaded = false;

    this.init = function(){
        if (typeof(this.onInit) === 'function'){
            this.onInit();
        }
    };

    this.initWidget = function(widget, regionId, callback){

        var handler = this;

        this.loadLang(function(){
            if (typeof(handler.onInitWidget) === 'function'){
                handler.onInitWidget(widget, regionId);
            }
            callback(widget);
        });

        if (typeof(this.onClick) === 'function'){
            this.dom(widget).click(function(e){
                e.stopPropagation();
                e.preventDefault();
                handler.onClick(widget, regionId);
            });
        }

    };

    this.createWidget = function(regionId, widget, callback){

        var handler = this;

        this.loadLang(function(){
            if (typeof(handler.onCreateWidget) === 'function'){
                widget = handler.onCreateWidget(widget, regionId);
            }
            callback(widget);
        });

        if (typeof(this.onClick) === 'function'){
            this.dom(widget).click(function(e){
                e.stopPropagation();
                e.preventDefault();
                handler.onClick(widget, regionId);
            });
        }

    };

    this.loadLang = function(callback){

        if (typeof(this.lang) === 'function') {
            callback();
            return;
        }

        this.lang = function(){};

        var handler = this;

        cms.loadWidgetLang(this.getName(), function(phrases){

            handler.langPhrases = phrases;

            handler.lang = function(phraseId, replacements){

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

            callback();

        });

    };

	this.openOptionsForm = function(regionId, widgetId){

        var handler = this;
        var options = cms.getWidgetOptions(regionId, widgetId);

        var formSettings = {
			id: this.getName() + '-options',
            title: cms.lang("widgetOptions") + ': ' + this.getTitle(),
			values: options,
			source: {
				module: 'widgets',
				action: 'loadOptionsForm',
                data: {
                    handler: this.getName()
                }
			},
			buttons: {
				ok: cms.lang("apply"),
			},
			onSubmit: function(options, form){
				handler.saveOptions(regionId, widgetId, options, form);
			}
		};

        if (typeof(this.getOptionsFormSettings) !== 'undefined'){
            formSettings = $.extend(formSettings, this.getOptionsFormSettings(regionId, widgetId));
        }

        cms.openForm(formSettings);

	};

	this.saveOptions = function(regionId, widgetId, newOptions, form){

        var widget = cms.getWidget(regionId, widgetId);

        widget.domId = 'inlinecms-widget-' + regionId + widget.id;

        newOptions = $.extend({}, newOptions, this.applyOptions(widget, newOptions, form));

		cms.setWidgetOptions(regionId, widgetId, newOptions);

	};

	this.applyOptions = function(widget, options, form){};

    this.dom = function(widget){

        return $('#'+widget.domId+' .inlinecms-content', cms.pageFrame);

    };

    this.runBackend = function(action, params, callback){

        if (typeof(params) === 'undefined') {
            params = {};
        }

        params._widgetId = this.getName();
		params._widgetAction = action;

        cms.runModule('widgets', 'run', params, function(result){
            if (typeof(callback) === 'function'){
				callback(result);
			}
        });

    };

    this.onClick = function(widget, regionId){

        this.openOptionsForm(regionId, widget.id);

    };

}

