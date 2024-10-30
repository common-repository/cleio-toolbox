var idWidget = "";
mediaControl = {
	// Initializes a new media manager or returns an existing frame.
	// @see wp.media.featuredImage.frame()
	frame: function() {
		if ( this._frame )
			return this._frame;

		this._frame = wp.media({
			title: "Choose or upload an image!",
			library: {
				type: "image"
			},
			button: {
				text: "Attach image"
			},
			multiple: false
		});
		
		this._frame.on( "open", this.updateFrame ).state("library").on( "select", this.select );
		
		return this._frame;
	},
	
	select: function() {
		// Do something when the "update" button is clicked after a selection is made.
		var selection = this.get("selection").first()
		//jQuery("#' . $this->get_field_id('picture') . '").val(selection.url);
		jQuery("#widget-" + idWidget + "-pictureid").val(selection.id);
		jQuery(".exile-media-control-choose").hide()
	},
	
	updateFrame: function() {
		// Do something when the media frame is opened.
	},
	
	init: function() {
		jQuery("#wpbody").on("click", ".exile-media-control-choose", function(e) {
			e.preventDefault();
			idWidget = jQuery( this ).attr('id').split("-img-bt")[0]
			mediaControl.frame().open();
		});
	}
};

mediaControl.init();

function _select_bio( elem ){

	var val = jQuery( elem ).val()
	var idDiv = jQuery( elem ).attr( "name" ).replace( /\[/g, "-").replace(/\]/g, "")
	console.log( idDiv )
	
	if( val == "image" ) {
		jQuery("#" + idDiv + "-avatar" ).hide();
		jQuery("#" + idDiv + "-image").show();
		jQuery(".exile-media-control-choose").show()
	}
	else if ( val == "avatar" ) {
		jQuery("#" + idDiv + "-avatar").show();
		jQuery("#" + idDiv + "-image").hide();									
	}
	else {
		jQuery("#" + idDiv + "-image").hide();		
		jQuery("#" + idDiv + "-avatar").hide();									
	}
			
	
}
					