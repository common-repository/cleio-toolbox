jQuery( 
	function($) {
	
		getSitemapShortcode();

		jQuery('input[name="gen-hide-cat"]').click(function(){
			getSitemapShortcode();
		})

		jQuery('input[name="gen-sitemap-content[]"]').click(function(){
			getSitemapShortcode();
		})

		var clip = new ZeroClipboard(jQuery("#gen-sitemap-shortcode-paste"));
		jQuery("#gen-sitemap-shortcode-paste").click(function(){ return false;})
		clip.on( "ready", function( readyEvent ) {
		  clip.on( "aftercopy", function( event ) {
		  	var textAfter = jQuery("#gen-sitemap-shortcode-paste").text()
		  	jQuery("#gen-sitemap-shortcode-paste").text("").text("Shortcode copied!")
		  	setTimeout(
		  		function(){
		  			jQuery("#gen-sitemap-shortcode-paste").text("").text(textAfter)		  			
				},
				5000
			);
		  });
		});

		function getSitemapShortcode(){
			// Get option
			var hidecat = $("input[name='gen-hide-cat']:checked").val();
			var posttype = "";
			$("input[name='gen-sitemap-content[]']:checked").each(function(){ posttype += (posttype == "") ? jQuery(this).val() : ","+jQuery(this).val(); });
			// Generate code
			var genshortcode = '[cleio-sitemap';
			if( hidecat )	genshortcode += ' categories=0';
			else	genshortcode += ' categories=1';
			if( posttype ) genshortcode += ' exclude="' + posttype + '"';
			genshortcode += ']';
			$("#gen-sitemap-shortcode").text("")
			$("#gen-sitemap-shortcode").text( genshortcode )
		}

	}
)