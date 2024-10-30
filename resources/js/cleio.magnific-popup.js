jQuery(
	function($) {
	
		$(".exile-instagram-container").magnificPopup({
			type: "image",
			delegate: 'a',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0,1]
			}
		});
		
    
	}
);