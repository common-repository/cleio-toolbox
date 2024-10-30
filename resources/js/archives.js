jQuery(document).ready(
	
	function($) {
		
		plugEvent();
		
		/*
		 * Handle the year navigation
		 */
		function plugEvent() {
		
			$('.exile-pagination-year').click(
			
				function(){
					
					var year = $( this ).attr( 'id' ).split( 'exile-year-' )[1];
					getArchives( 'year', year, 1 );			
					
				}
				
			);
			
			$('.exile-pagination-month').click(
				
				function(){
				
					var month = $( this ).attr( 'id' ).split( 'exile-month-' )[1];
					getArchives( 'month', month, 0 );			
					
				}
				
			)
			
		}
		
		/*
		 * Method to perform the AJAX call and get the content of archives
		 */
		function getArchives( mode, value, changeYear ) {
			if ( !mode ) mode = 'year';
			var year 	= parseInt( $( "#exile-current-year" ).val() );
			var month 	= parseInt( $( "#exile-current-month" ).val() );
			
			if ( mode == 'year' ) {
				year = parseInt( value );
			}
			else if ( mode == 'month' ) {
				month = parseInt( value );
			}
			var pYear 	= parseInt( $( "#exile-paginate-year" ).val() );
			var pMonth 	= parseInt( $( "#exile-paginate-month" ).val() );
			var page 	= parseInt( $( "#exile-current-page" ).val() );
			var posttype_page =  parseInt( $( "#exile-posttype-page" ).val() );
			var posttype_countries =  parseInt( $( "#exile-posttype-countries" ).val() );
			var posttype_addresses =  parseInt( $( "#exile-posttype-addresses" ).val() );
			var posttype =  $( "#exile-posttype" ).val();
			// Do the AJAX call
			jQuery.post(
				exile_vars.ajaxurl,
				{
					action 		: 'cleiotoolbox_get_archives',
					'year'	    : year,
					'month'	    : month,
					'pYear'	    : pYear,
					'pMonth'	: pMonth,
					'postId'	: page,
					'changeYear': changeYear,
					'posttype_exclude': posttype,
					'posttype_page' : posttype_page,
					'posttype_countries' : posttype_countries,
					'posttype_addresses' : posttype_addresses
				},
				
				// On success
				function( response ){	
					//var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
					//var id = res.responses[0].id;
					//var data = res.responses[0].data;
					
					$('.exile-archives-container').empty().append( response )
					$( "#exile-current-year" ).val( year )
					$( "#exile-current-month" ).val( month )
					
					plugEvent();
					
					return false;
					
				}
				
			);
		}
		
	}

)