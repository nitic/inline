cms.registerWidgetHandler('map', new function(){

	InlineWidget.apply(this, arguments);

    this.isApiLoaded = false;
    this.isApiLoadInProgress = false;
    this.callbacks = [];

    this.mapsObjects = {};

	this.defaultOptions = {
		width: '',
		height: 200,
		lat: '48.856614',
		lng: '2.3522219',
		zoom: 12
	};

	this.getName = function(){
		return 'map';
	};

	this.getIcon = function(){
		return "fa-map-o";
	};

    this.onClick = false;

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

                $('a.find-coords', form).click(function(e){

                    e.preventDefault();

                    cms.showPromptDialog(handler.lang('addressEnter'), handler.lang('address'), function(address){
                        handler.loadApi(function(google){

                            var geocoder = new google.maps.Geocoder();

                            geocoder.geocode( { 'address': address }, function(results, status) {

                                if (status !== google.maps.GeocoderStatus.OK) {
                                   cms.showMessageDialog(handler.lang('addressError')); return;
                                }

                                var lat = results[0].geometry.location.lat();
                                var lng = results[0].geometry.location.lng();

                                $('.m-lat', form).val(lat);
                                $('.m-lng', form).val(lng);

                            });

                        });
                    });

                });

            }
        };

    };

	this.onInitWidget = function(widget){

		var mapId = this.getMapId(widget);

        var handler = this;

        this.loadApi(function(google){

            handler.initWidgetMap(mapId, widget, google);

        });

	};

	this.onCreateWidget = function(widget){

		widget.options = $.extend({}, this.defaultOptions, widget.options);

		var mapId = this.getMapId(widget);
		var mapCanvas = this.getMapCanvas(widget);

		this.dom(widget).append(mapCanvas);

        var handler = this;

        this.loadApi(function(google){

            handler.initWidgetMap(mapId, widget, google);

        });

		return widget;

	};

    this.initWidgetMap = function(mapId, widget, google){

        var center = new google.maps.LatLng(widget.options.lat, widget.options.lng);

        var map = new google.maps.Map(this.dom(widget).find('#'+mapId)[0], {
            center: center,
            zoom: Number(widget.options.zoom)
        });

        map.marker =  new google.maps.Marker({
            map: map,
            position: center,
            draggable: true
        });

        google.maps.event.addListener(map, 'zoom_changed', function() {
            map.widget.options.zoom = map.getZoom();
        });

        google.maps.event.addListener(map.marker, 'dragend', function() {
            var coords = map.marker.getPosition();
            map.widget.options.lat = coords.lat();
            map.widget.options.lng = coords.lng();
            map.setCenter(coords);
        });

        map.widget = widget;

        this.mapsObjects[mapId] = map;

    };

	this.applyOptions = function(widget, options){

		var mapId = this.getMapId(widget);

		var mapCanvas = $('#'+mapId, this.dom(widget));
        var mapObject = this.mapsObjects[mapId];

		if (options.width){
			mapCanvas.css({width: options.width});
		} else {
			mapCanvas.css({width: ''});
		}

		if (options.height){
			mapCanvas.css({height: options.height});
		} else {
			mapCanvas.css({height: 200});
		}

        this.loadApi(function(google){

            var center = new google.maps.LatLng(options.lat, options.lng);

            google.maps.event.trigger(mapObject, "resize");

            mapObject.setZoom(Number(options.zoom));
            mapObject.setCenter(center);
            mapObject.marker.setPosition(center);

        });

	};

    this.loaded = function() {

        this.isApiLoaded = true;

        var google = cms.getFrameWindow().google;

        while(this.callbacks.length > 0) {
            var callback = this.callbacks.pop();
            callback(google);
        }

    };

    this.loadApi = function(callback) {

        if (this.isApiLoaded) {
            var google = cms.getFrameWindow().google;
            callback(google); return;
        }

        this.callbacks.push(callback);

        if (!this.isApiLoadInProgress){
            cms.injectScript('http://maps.googleapis.com/maps/api/js?callback=parent.cms.widgetHandlers.map.loaded&language='+cms.getLanguage());
            this.isApiLoadInProgress = true;
        }

    };

	this.getContent = function(widget){

		return this.getMapCanvas(widget)[0].outerHTML;

	};

	this.getMapId = function(widget){

		return widget.domId + '-map';

	};

	this.getMapCanvas = function(widget){

		var mapId = this.getMapId(widget);

		var mapCanvas = $('<div></div>').attr('id', mapId).addClass('map-canvas').css({
			height: widget.options.height,
			width: widget.options.width
		});

		return mapCanvas;

	};

});
