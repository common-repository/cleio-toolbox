jQuery( 
	function($) {
		
		// Night Style
		var styleNight = new google.maps.StyledMapType([{stylers: [{ invert_lightness: true }]}], {name: "Night"});
		// Red Style
		var styleRed = new google.maps.StyledMapType([{stylers: [{ hue: "#ff0000" }]}], {name: "Red"});
		// Chilled Style
		var styleChilled = new google.maps.StyledMapType([	{featureType: "road",elementType: "geometry",stylers: [{"visibility": "simplified"}]}, {featureType: "road.arterial",stylers: [ {hue: 149}, {saturation: -78}, {lightness: 0}]}, {featureType: "road.highway", stylers: [ {hue: -31},{saturation: -40}, {lightness: 2.8}]}, {featureType: "poi",elementType: "label",stylers: [{"visibility": "off"}]}, {featureType: "landscape",stylers: [{hue: 163},{saturation: -26},{lightness: -1.1}]}, {featureType: "transit",stylers: [{"visibility": "off"}]}, {featureType: "water",stylers: [{hue: 3},{saturation: -24.24},{lightness: -38.57}]}], {name: "Chilled Map"});
		// Retro Style
		var styleRetro = new google.maps.StyledMapType([{"featureType":"administrative","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"water","stylers":[{"visibility":"simplified"}]},{"featureType":"transit","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"visibility":"off"}]},{"featureType":"road.local","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"water","stylers":[{"color":"#84afa3"},{"lightness":52}]},{"stylers":[{"saturation":-17},{"gamma":0.36}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#3f518c"}]}], {name: "Retro"});
		// Pale Style
		var stylePale = new google.maps.StyledMapType([{"featureType":"water","stylers":[{"visibility":"on"},{"color":"#acbcc9"}]},{"featureType":"landscape","stylers":[{"color":"#f2e5d4"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#c5c6c6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#e4d7c6"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#fbfaf7"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#c5dac6"}]},{"featureType":"administrative","stylers":[{"visibility":"on"},{"lightness":33}]},{"featureType":"road"},{"featureType":"poi.park","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":20}]},{},{"featureType":"road","stylers":[{"lightness":20}]}], {name: "Pale Dawn"});
		// Monochrome Style
		var styleMonochrome = new google.maps.StyledMapType([{"featureType":"water","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":-78},{"lightness":67},{"visibility":"simplified"}]},{"featureType":"landscape","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#e9ebed"},{"saturation":-90},{"lightness":-8},{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":10},{"lightness":69},{"visibility":"on"}]},{"featureType":"administrative.locality","elementType":"all","stylers":[{"hue":"#2c2e33"},{"saturation":7},{"lightness":19},{"visibility":"on"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":-2},{"visibility":"simplified"}]}], {name: "Light Monochrome"});
		// Blue Water Style
		var styleBlueWater = new google.maps.StyledMapType([{"featureType":"water","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"landscape","stylers":[{"color":"#f2f2f2"}]},{"featureType":"road","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]}], {name: "Blue Water"});
		// Blue Style
		var styleBlue= new google.maps.StyledMapType([{stylers: [ {hue: "#0000b0"}, {invert_lightness: true}, {saturation: -30} ]}], {name: "Blue"});
		
		getShortcode();
		if( cleiomaps.provider == "gmap" ){			
			var mapOptions = {
	          center: new google.maps.LatLng(48.8534100,2.3488000),
	          zoom: 4,
	          disableDefaultUI: true
	        };
			var map = new google.maps.Map(document.getElementById("map-generator-preview"), mapOptions);
			map.mapTypes.set( "Night", styleNight );
			map.mapTypes.set( "Red", styleRed );
			map.mapTypes.set( "Chilled", styleChilled );
			map.mapTypes.set( "Retro", styleRetro );
			map.mapTypes.set( "Pale Dawn", stylePale );
			map.mapTypes.set( "Light Monochrome", styleMonochrome );
			map.mapTypes.set( "Blue Water", styleBlueWater );
			map.mapTypes.set( "Blue", styleBlue );
			map.mapTypes.set( "Toner", new google.maps.StamenMapType("toner"));
			map.mapTypes.set( "Watercolor", new google.maps.StamenMapType("watercolor"));
		}
		else {
			var lonlatCenter =  new L.LatLng(51.500, -0.167);
			//var layer = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {subdomains: [ "a", "b", "c" ]});
			var layer = new L.Google("ROADMAP");
			var mapOptions = { minZoom: 1, layers: [ layer ], center: lonlatCenter, zoom: 1};
			var map = new L.map("map-generator-preview", mapOptions);
		}

		var markers = [];
		if( cleiomaps.provider == "gmap" )	var bounds = new google.maps.LatLngBounds();
		else var bounds = new L.LatLngBounds();

		jQuery(".cmaps").click(function(){ 
			addMarkers("black");
			if( cleiomaps.provider == "gmap" ){	
				google.maps.event.trigger(map, 'resize');
				map.fitBounds(bounds);
			}
			else {
				map.invalidateSize(false);
			}
		})

		function addMarkers(color){
			if( cleiomaps.provider == "gmap" ){
				var marker1 = new google.maps.Marker({
					map: map,			
					icon: {
						path: fontawesome.markers.MAP_MARKER,
						scale: 0.4,
						strokeWeight: 0.2,
						strokeColor: color,
						strokeOpacity: 1,
						fillColor: color,
						fillOpacity: 1,
						anchor: new google.maps.Point(20,-29)
					},
					position: new google.maps.LatLng(45,-2)
				});
				var marker2 = new google.maps.Marker({
					map: map,			
					icon: {
						path: fontawesome.markers.CIRCLE_O,
						scale: 0.15,
						strokeWeight: 0.7,
						strokeColor: color,
						strokeOpacity: 1,
						fillColor: color,
						fillOpacity: 1,
						anchor: new google.maps.Point(20,-29)
					},
					position: new google.maps.LatLng(51.500,-0.167)
				});			
				var marker3 = new google.maps.Marker({
					map: map,			
					icon: {
						path: fontawesome.markers.CAMERA,
						scale: 0.3,
						strokeWeight: 0.2,
						strokeColor: color,
						strokeOpacity: 1,
						fillColor: color,
						fillOpacity: 1,
						anchor: new google.maps.Point(20,-29)
					},
					position: new google.maps.LatLng(48,5)
				});
				var marker4 = new google.maps.Marker({
					map: map,			
					icon: {
						path: fontawesome.markers.PLUS_CIRCLE,
						scale: 0.3,
						strokeWeight: 0.2,
						strokeColor: color,
						strokeOpacity: 1,
						fillColor: color,
						fillOpacity: 1,
						anchor: new google.maps.Point(20,-29)
					},
					position: new google.maps.LatLng(35,15)
				});
				var marker5 = new google.maps.Marker({
					map: map,			
					icon: {
						path: fontawesome.markers.FLAG,
						scale: 0.3,
						strokeWeight: 0.2,
						strokeColor: color,
						strokeOpacity: 1,
						fillColor: color,
						fillOpacity: 1,
						anchor: new google.maps.Point(20,-29)
					},
					position: new google.maps.LatLng(25,25)
				});
				var marker6 = new google.maps.Marker({
					map: map,			
					icon: {
						path: fontawesome.markers.PENCIL,
						scale: 0.3,
						strokeWeight: 0.2,
						strokeColor: color,
						strokeOpacity: 1,
						fillColor: color,
						fillOpacity: 1,
						anchor: new google.maps.Point(20,-29)
					},
					position: new google.maps.LatLng(30,10)
				});			
				var marker7 = new google.maps.Marker({
					map: map,				
					icon: {
						path: fontawesome.markers.PLANE,
						scale: 0.35,
						strokeWeight: 0.2,
						strokeColor: color,
						strokeOpacity: 1,
						fillColor: color,
						fillOpacity: 1,
						anchor: new google.maps.Point(20,-29)
					},
					position: new google.maps.LatLng(40,30)
				});

				bounds.extend(marker1.position);
				bounds.extend(marker2.position);
				bounds.extend(marker3.position);
				bounds.extend(marker4.position);
				bounds.extend(marker5.position);
				bounds.extend(marker6.position);
				bounds.extend(marker7.position);
			}
			else {
				var size = new L.Point( 20, 20 );
				var offset = new L.Point( 2, 20 );
				var markerIcon1 = L.AwesomeMarkers.icon({icon: "map-marker", iconSize: size, iconAnchor: offset, prefix: "fa", iconColor: color });
				var markerIcon2 = L.AwesomeMarkers.icon({icon: "pencil", iconSize: size, iconAnchor: offset, prefix: "fa", iconColor: color });
				var markerIcon3 = L.AwesomeMarkers.icon({icon: "camera", iconSize: size, iconAnchor: offset, prefix: "fa", iconColor: color });
				var markerIcon4 = L.AwesomeMarkers.icon({icon: "plane", iconSize: size, iconAnchor: offset, prefix: "fa", iconColor: color });
				var markerIcon5 = L.AwesomeMarkers.icon({icon: "flag", iconSize: size, iconAnchor: offset, prefix: "fa", iconColor: color });
				var markerIcon6 = L.AwesomeMarkers.icon({icon: "plus-circle", iconSize: size, iconAnchor: offset, prefix: "fa", iconColor: color });
				var markerIcon7 = L.AwesomeMarkers.icon({icon: "circle-o", iconSize: size, iconAnchor: offset, prefix: "fa", iconColor: color });
				var coord1 = new L.LatLng(51.500,-0.167);
				var coord2 = new L.LatLng(45,-2);
				var coord3 = new L.LatLng(48,5);
				var coord4 = new L.LatLng(35,15);
				var coord5 = new L.LatLng(25,25);
				var coord6 = new L.LatLng(30,10);
				var coord7 = new L.LatLng(40,30);

				var marker1 = new L.Marker(coord1,{ icon: markerIcon1}).addTo(map);
				var marker2 = new L.Marker(coord2,{ icon: markerIcon2}).addTo(map);
				var marker3 = new L.Marker(coord3,{ icon: markerIcon3}).addTo(map);
				var marker4 = new L.Marker(coord4,{ icon: markerIcon4}).addTo(map);
				var marker5 = new L.Marker(coord5,{ icon: markerIcon5}).addTo(map);
				var marker6 = new L.Marker(coord6,{ icon: markerIcon6}).addTo(map);
				var marker7 = new L.Marker(coord7,{ icon: markerIcon7}).addTo(map);

				bounds.extend( coord1 );
				bounds.extend( coord2 );
				bounds.extend( coord3 );
				bounds.extend( coord4 );
				bounds.extend( coord5 );
				bounds.extend( coord6 );
				bounds.extend( coord7 );
			}

			markers.push(marker1);
			markers.push(marker2);
			markers.push(marker3);
			markers.push(marker4);
			markers.push(marker5);
			markers.push(marker6);
			markers.push(marker7);
		}

		if( cleiomaps.provider == "gmap" ){	
			var listener = google.maps.event.addListener(map, "idle", function () {
				map.setZoom(1);
				google.maps.event.removeListener(listener);
			});
		}
		
		jQuery("#gen-map-style").change(function(){
			if( cleiomaps.provider == "gmap" ){
				if( jQuery("#gen-map-style option:selected").val() == "Roadmap" ){ 
					map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
				}
				else if ( jQuery("#gen-map-style option:selected").val() == "Satellite" ) {
					map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
				}
				else map.setMapTypeId(jQuery("#gen-map-style option:selected").val());
			}
			else {
				map.eachLayer(function (layer) {
				    map.removeLayer(layer);
				});

				addMarkers(jQuery('#gen-pins-color').val());
				if( jQuery("#gen-map-style option:selected").val() == "Roadmap" ){
					var layer = new L.Google("ROADMAP");
				}
				else if ( jQuery("#gen-map-style option:selected").val() == "Toner" ) {
					var layer = new L.StamenTileLayer("toner");
				}
				else if ( jQuery("#gen-map-style option:selected").val() == "Watercolor" ) {
					var layer = new L.StamenTileLayer("watercolor");
				}
				else if ( jQuery("#gen-map-style option:selected").val() == "aerial" ) {
					var layer = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png", {subdomains: [ "otile1", "otile2", "otile3", "otile4" ]});
				}
				else if ( jQuery("#gen-map-style option:selected").val() == "cycle" ) {
					var layer = new L.TileLayer("http://{s}.tile.opencyclemap.org/cycle/{z}/{x}/{y}.png", {subdomains: [ "a", "b", "c" ]});
				}	
				else if ( jQuery("#gen-map-style option:selected").val() == "transport" ) {
					var layer = new L.TileLayer("http://{s}.tile2.opencyclemap.org/transport/{z}/{x}/{y}.png", {subdomains: [ "a", "b", "c" ]});
				}		
				else {
					var layer = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {subdomains: [ "a", "b", "c" ]});
				}
				map.addLayer(layer)			
			}
			getShortcode();
			return false;
		})

		jQuery("#gen-zoom").change(function(){ map.setZoom( parseInt(jQuery(this).val()) ); getShortcode();})

		jQuery('#gen-cluster-color').spectrum({
			chooseText: cleiovar.txtValid,
			cancelText: cleiovar.txtCancel, 
			preferredFormat: "hex",
    		showInput: true,
			change: function(color){
				getShortcode();
			}
		})
		jQuery('#gen-pins-color').spectrum({
			chooseText: cleiovar.txtValid,
			cancelText: cleiovar.txtCancel, 
			preferredFormat: "hex",
    		showInput: true,
			change: function(color){
				for (var i = 0; i < markers.length; i++ ) {
					if( cleiomaps.provider == "gmap" )	markers[i].setMap(null);
					else map.removeLayer( markers[i] )
				}				
				markers = [];
				addMarkers(color.toHexString());
				getShortcode();
			}
		})
		jQuery('input[name="gen-cluster"]').click(function(){
			getShortcode();
		})

		jQuery('input[name="gen-content-type[]"]').click(function(){
			getShortcode();
		})

		jQuery('#gen-max-pins, #gen-width, #gen-height').keyup(function(){
			getShortcode();
		})

		jQuery("#gen-filterdatevalue").datepicker({
			dateFormat : 'dd-mm-yy',
		  	onSelect: function() {
		    	getShortcode();
		  	}
		});

		

		jQuery("input[name='gen-filter']").click(function(){ getShortcode(); })

		jQuery('#gens-pins-selector').each(function(){
			jQuery(this).children("input[type=radio]").click(function(){
				getShortcode();
			})

		})
		var clip = new ZeroClipboard(jQuery("#gen-shortcode-paste"));
		jQuery("#gen-shortcode-paste").click(function(){ return false;})
		clip.on( "ready", function( readyEvent ) {
		  clip.on( "aftercopy", function( event ) {
		  	var textAfter = jQuery("#gen-shortcode-paste").text()
		  	jQuery("#gen-shortcode-paste").text("").text("Shortcode copied!")
		  	setTimeout(
		  		function(){
		  			jQuery("#gen-shortcode-paste").text("").text(textAfter)		  			
				},
				5000
			);
		  });
		});

		function getShortcode(){
			// Get option
			var pins            = $("input[name='gen-pins']:checked").val();
			var color           = $("#gen-pins-color").val();
			var map             = $("#gen-map-style").val();
			var zoom            = $("#gen-zoom").val();
			var markerslimit    = $("#gen-max-pins").val();
			var filter          = $("input[name='gen-filter']:checked").val();
			var cluster			= $("input[name='gen-cluster']").is(":checked");
			var posttype        = "";
			$("input[name='gen-content-type[]']:checked").each(function(){ posttype += (posttype == "") ? jQuery(this).val() : ","+jQuery(this).val(); });
			var datefiltervalue = $("#gen-filterdatevalue").val();
			var width           = $("#gen-width").val();
			var height          = $("#gen-height").val();
			// Generate code
			var genshortcode = '[cleiomaps';
			genshortcode += ' pins="' + pins + '"';
			if (pins=="cleio-pins") genshortcode += ' pinscolor="' + color + '"';
			genshortcode += ' map="' + map + '"';
			genshortcode += ' zoom="' + zoom + '"';
			genshortcode += ' width="' + width + '"';
			genshortcode += ' height="' + height + '"';
			if( posttype ) genshortcode += ' exclude="' + posttype + '"';
			if( filter == "filter-amount" ){
				if( markerslimit != 0 ) genshortcode += ' maxmarkers="' + markerslimit + '"';
			} 
			else {
				if( datefiltervalue ) genshortcode += ' priorto="' + datefiltervalue + '"';
			}
			if( cluster ){
				genshortcode += ' cluster="1" clustertextcolor="' + $("#gen-cluster-color").val() + '"';
			}
			genshortcode += ']';
			$("#gen-shortcode").text("")
			$("#gen-shortcode").text( genshortcode )
		}

	}
)