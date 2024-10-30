jQuery( 
	function($) {
		
	
		getShortcode();

		jQuery('input[name="gen-paginate"]').click(function(){
			getShortcode();
		})

		jQuery('input[name="gen-archives-content[]"]').click(function(){
			getShortcode();
		})

		var clip = new ZeroClipboard(jQuery("#gen-archives-shortcode-paste"));
		jQuery("#gen-archives-shortcode-paste").click(function(){ return false;})
		clip.on( "ready", function( readyEvent ) {
		  clip.on( "aftercopy", function( event ) {
		  	var textAfter = jQuery("#gen-archives-shortcode-paste").text()
		  	jQuery("#gen-archives-shortcode-paste").text("").text("Shortcode copied!")
		  	setTimeout(
		  		function(){
		  			jQuery("#gen-archives-shortcode-paste").text("").text(textAfter)		  			
				},
				5000
			);
		  });
		});

		function getShortcode(){
			// Get option
			var paginate = $("input[name='gen-paginate']:checked").val();
			var posttype = "";
			$("input[name='gen-archives-content[]']:checked").each(function(){ posttype += (posttype == "") ? jQuery(this).val() : ","+jQuery(this).val(); });
			// Generate code
			var genshortcode = '[cleio-archives';
			if( paginate == "year")	genshortcode += ' paginate_year=1';
			else 	genshortcode += ' paginate_year=1 paginate_month=1';
			if( posttype ) genshortcode += ' exclude="' + posttype + '"';
			genshortcode += ']';
			$("#gen-archives-shortcode").text("")
			$("#gen-archives-shortcode").text( genshortcode )
		}

	}
)