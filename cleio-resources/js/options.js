jQuery( 
	function() {

		var defTab = 0;
		var isDefaultUpdate = 'noupdate';
		if ( jQuery("#defTab").val() ) defTab = jQuery("#defTab").val() - 1
		
		// Handle tabs
		jQuery("#tabsOptions .hidden").removeClass('hidden');
		jQuery("#tabsOptions").tabs({ active: defTab });
		
		jQuery('.datepicker').datepicker({ dateFormat : 'dd-mm-yy' });
		
		jQuery('.cleio-pinsselector-container').each(function(){
			if ( jQuery(this).children("input[type=radio][value=cleio-pins]").is(':checked') ) jQuery(this).children("div:last").hide()
			else jQuery(this).children("div:first").hide()
			jQuery(this).children("input[type=radio]").click(function(){
				if( jQuery(this).val() == "cleio-pins" ) {
					jQuery(this).parent().children("div:last").hide()					
					jQuery(this).parent().children("div:first").show()
				}
				else {
					jQuery(this).parent().children("div:first").hide()					
					jQuery(this).parent().children("div:last").show()					
				}
			})
		})
		
		jQuery(".cleio-colorpicker").spectrum({
			chooseText: cleiovar.txtValid,
			cancelText: cleiovar.txtCancel, 
			preferredFormat: "hex",
    		showInput: true,
			change: function(color) { jQuery(this).val(color.toHexString()); }
		})
		
		jQuery( '.selectpages' ).chosen({width:"100%"});
		
		jQuery( '.cleio-slider' ).each(
			function(){			
				var minval 	= jQuery(this).data("min")
				var maxval 	= jQuery(this).data("max")
				var val 	= jQuery(this).data("value") == "auto" ? 0 : jQuery(this).data("value")
				var inputid	= jQuery(this).data("inputassoc")
				jQuery(this).slider(
					{ 
						min:minval, 
						max:maxval, 
						step:1, 
						value:val,
						change: function( event, ui ) {
							jQuery("#"+inputid).val(ui.value == 0 ? "auto" : ui.value).trigger('change');
							jQuery("#"+inputid+"-label").find("strong").text( ui.value == 0 ? "Auto" : ui.value)							
						}
					}
				);
			}
		)
		
		jQuery('.gwebfonts-button').click(
			function(e) {
				e.preventDefault()
				//alert( jQuery('select[name=default-font]').val() )
				var url = "http://fonts.googleapis.com/css?family=" + jQuery('select[name=google-font]').val().replace(/ /g, "+"); 
				jQuery("head").append(jQuery("<link/>").attr({
				  rel:  "stylesheet",
				  href: url
				}));
				
				jQuery('#gwebfonts-preview').empty() 
				
				jQuery("<p/>", { style: "font-family:'" + jQuery('select[name=google-font]').val() + "',sans-serif" })
					.append("This is an example.").appendTo( 
						jQuery('#gwebfonts-preview')
					);
				return false;
			}
		)
		
		jQuery( '#cleio-change-account' ).click(
			function(){
				jQuery( ".cleio-logout-form" ).hide()
				jQuery( ".cleio-login-form" ).append( '<input type="button" value="Cancel" name="cleio-cancel-button" id="cleio-cancel-button" />' ).show()		
				jQuery( "#cleio-cancel-button").click(function(){ jQuery( ".cleio-login-form" ).hide(); jQuery( this ).remove(); jQuery( ".cleio-logout-form" ).show(); })			
			}
		)
		
		jQuery('#cleio-auth-button').click(
		    
		    function(e){
					
					jQuery.post(
						ajaxurl,
						{
							action 		: 'cleioco_auth',
							'username'	: jQuery( "#cleio-auth-login" ).val(),
							'password'	: jQuery( "#cleio-auth-pwd" ).val()
						},
						function( response ){	
							
							var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
							
							jQuery.each( 
								res.responses, 
								function() { 
								
									jQuery( ".message-connect" ).text( this.data ).show();	
									if ( this.id == 1 ) {									
										setTimeout(
											function () {
												location.reload()
											}, 
											2500
										);
									}
									else {									
										setTimeout(
											function () {
												jQuery( ".message-connect" ).hide()
											}, 
											2500
										);
									}
									
								}
							);//end each
							
							return false;
						}
						
					);
					
		    }
		)
		
		// Handling map style
		if ( cleiovar.mapsProvider == 'osm' ) {
			jQuery("[id*='-map-style'] option[class*='gmap']").attr("disabled", "disabled")
			jQuery("[id*='-map-style'] option[class*='osm']").removeAttr("disabled")	
		}
		else {			
			jQuery("[id*='-map-style'] option[class*='osm']").attr("disabled", "disabled")
			jQuery("[id*='-map-style'] option[class*='gmap']").removeAttr("disabled")		
		}
		
		jQuery('input[name="maps-provider"]').click(function(){
		
			if ( jQuery(this).val() == "osm" ) {
				jQuery("[id*='-map-style'] option[class*='gmap']").attr("disabled", "disabled")
				jQuery("[id*='-map-style'] option[class*='osm']").removeAttr("disabled")
				//console.log( jQuery("option.gmap") );
				//console.log( jQuery("option.osm") );
				jQuery("[id*='-map-style']") .each( function() {
					
					if( !jQuery( "#" + this.id + " option:selected").hasClass("osm") ) {
						jQuery("#" + this.id).val( jQuery("#" + this.id +" option:first").val() );	
					}
				})
				
				
			}
			else {			
				jQuery("[id*='-map-style'] option[class*='osm']").attr("disabled", "disabled")
				jQuery("[id*='-map-style'] option[class*='gmap']").removeAttr("disabled")	

				jQuery("[id*='-map-style']") .each( function() {
					if( !jQuery( "#" + this.id + " option:selected").hasClass("gmap") ) {
						jQuery("#" + this.id).val( jQuery("#" + this.id +" option:first").val() );	
					}									
				})
				
			}
		})
		
		// Handling Default map options
		jQuery( "#default-map-style" ).change(
			function(e) {
				jQuery("[id*='-map-style']").val( jQuery(this).val() );
				isDefaultUpdate = 'update';
			}
		)
		jQuery( "#maps-pins" ).change(
			function(e) {
				jQuery("select.pinsselector").val( jQuery(this).val() );
				isDefaultUpdate = 'update';
			}
		)
		jQuery( "#maps-pinstextcolor" ).change(
			function(e) {
				jQuery("[id*='-pinstextcolor']").val( jQuery(this).val() );
			}
		)

		// Handle logout instagram		
		jQuery(".cleio-instagram-logout").click(
			function(e){
				jQuery.post(
					ajaxurl,
					//Data
					{
						action 		: 'exile_logout_instagram'
					},
					
					// On success
					function( response ){	
						var response = eval("(function(){return " + response + ";})()");
						if ( response.id ) {							
							// Show and Fade the message
							jQuery('#instagram-log-control').empty().append( response.data );							
						}						
						return false;
					}
					
				);
				return false;
			}
		);

		// Handle logout twitter		
		jQuery(".cleio-twitter-logout").click(
			function(){
			
				jQuery.post(
					ajaxurl,
					//Data
					{
						action 		: 'exile_logout_twitter'
					},
					
					// On success
					function( response ){	
						var response = eval("(function(){return " + response + ";})()");
						if ( response.id ) {							
							// Show and Fade the message
							jQuery('#twitter-log-control').empty().append( response.data );							
						}						
						return false;
					}
					
				);
				return false;
			}
		);
		
		// Handle submit form
		jQuery('.cleio-options-save').click(
		    function(){
		        var saveArrayUncheck = Array();
		        jQuery('.cbYesNo').each(
		            function(){
		                if( jQuery(this).is(":checked") ) {
		                    jQuery(this).attr('value','1')
		                }
		                else {
		                    jQuery(this).attr('value','0')
		                    saveArrayUncheck.push( this )
		                }
		                jQuery(this).attr('checked',true)
		            }
		        )		        
		        
		        var sValues = jQuery("#cleio-options-form").serialize();
				jQuery('.message').append('<img class="cleio-loading" src="' + cleiovar.loadingurl + '" />');
				
				console.log("passe")

		        jQuery.post(
					ajaxurl,
					//Data
					{
						action 		: 'cleio_options_save',
						mapDefault	: isDefaultUpdate,
						data	    : sValues
					},					
					// On success
					function( response ){	
					    var response = eval("(function(){return " + response + ";})()");
						if ( response.status == "OK" ) {
						
							// Show and Fade the message
							jQuery('.cleio-loading').empty().remove();
							jQuery('.message').append('<span class="messageOk updated">'+response.message+'</span>');
							isDefaultUpdate = 'noupdate';
							var refresh = response.refresh;
							setTimeout(
								function () {
									jQuery('.messageOk').fadeOut(function(){
										jQuery(this).empty().remove();
										if( refresh ) location.reload();
									});
								}, 
								1500
							);	
						}
						else{								
							// Show and Fade the error messagee
							jQuery('.message').append('<span class="messageNok error">'+response.message+'</span>');
							
							setTimeout(
								function () {
									jQuery('.messageNok').fadeOut(function(){
										jQuery(this).empty().remove();
									});
								}, 
								1500
							);
							
						}
						
						return false;
					}
					
				);
		        
		        jQuery.each(
		                saveArrayUncheck,
		                function(){ 
		                    jQuery(this).attr('checked',false) 
		                }
		                );
		        
		    }    
		)

		jQuery('.thumbnail-generate').click(
		    
		    function(){
			
				//var idType = jQuery("#thumbnail-type").val()				// ID for the thumbnail type (defined with add_image_size)
				//var textType = jQuery("#thumbnail-type :selected").text()	// Name of this tumbnail type (defined with add_image_size)
				//alert( "Regen des thumbnails pour le type : "+textType+" ("+idType+")" )
				
				// Get the attachment list to regenerate
				jQuery.post(
					ajaxurl,
					
					//Data
					{ action: 'exile_get_attachments' },
					
					// On success
					function( response ){	
					    	
						var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
						
						jQuery.each( 
							res.responses, 
							function() { 
								if ( this.id ) {
									jQuery('<div id="progressBar" class="cleio-fw-progressbar"><div></div></div>').appendTo("#import_settings")
									jQuery('<div id="reportGenerate" class="cleio-fw-report"></div>').appendTo("#import_settings")
									var tabData = eval( this.data )
									var i = 0
									jQuery.each(									
										tabData,
										function() {
											
											jQuery.post(
												ajaxurl,
												
												//Data
												{ action: 'exile_generate_attachment', 'attachment_id': this.id},
												
												function( response ) {
													var resGen = wpAjax.parseAjaxResponse(response, 'ajax-response');
													i++;
													var percent = Math.round( ( i / tabData.length ) * 100 );
													var progressBarWidth = percent * jQuery('#progressBar').width() / 100;
													
													jQuery('#progressBar')
														.find('div')
														.animate(
															{ width: progressBarWidth }, 
															700,
															'swing',
															function(){ 
																jQuery('#progressBar').find('div').html(percent + "%&nbsp;");
																if ( jQuery('#reportGenerate > p').size() > 4 ) {
																	jQuery('#reportGenerate :last-child').fadeOut(function(){ jQuery(this).empty().remove(); jQuery('<p class="cleio-report-line"></p>').html( resGen.responses[0].data ).prependTo("#reportGenerate") })
																}
																else jQuery('<p class="cleio-report-line"></p>').html( resGen.responses[0].data ).prependTo("#reportGenerate")
																
																
																if ( percent == 100 ) {
																	setTimeout(
																		function () {
																		
																			jQuery('#progressBar').fadeOut(function(){
																				jQuery(this).empty().remove();
																			});
																			
																			jQuery('#reportGenerate').fadeOut(function(){
																				jQuery(this).empty().remove();
																			});
																			
																		}, 
																		2500
																	);
																}
															}
														);
												}
											);
										}
									)
								}
								
							}
						);//end each
						
						return false;
					}
					
				);
				
			}	
		)
		
		jQuery( "#cleio-framework" ).show()
    }
);
