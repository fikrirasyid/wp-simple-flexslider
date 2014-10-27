jQuery(document).ready(function($){
	/**
	* If there's an slide already, hide the no-wp-simple-flexslider-slide-notice
	*/
	if( $('.wp-simple-flexslider-slide-wrap').length > 0 ){
		$('.no-wp-simple-flexslider-slide-notice').hide();
	}

	/**
	* Making the order of slide sortable
	*/
	$('#slideshow-metabox .slides-wrap').sortable();

	/**
	* Adding slide mechanism
	*/
	$('body').on( 'click', '.wp-simple-flexslider-slide-add', function(e){
		e.preventDefault();

		var file_frame;

		// If the media frame already exists, reopen it.
	    if ( file_frame ) {
	      file_frame.open();
	      return;
	    }

	    // Create the media frame.
	    file_frame = wp.media.frames.file_frame = wp.media({
	      multiple: true  // Set to true to allow multiple files to be selected
	    });
	 
	    // When an slide is selected, run a callback.
	    file_frame.on( 'select', function() {
			// We set multiple to false so only get one slide from the uploader
			attachments = file_frame.state().get('selection').toJSON();

			for (var i = attachments.length - 1; i >= 0; i--) {
				var attachment = attachments[i];

				// Check if selected slide has been existed
				if( $('.wp-simple-flexslider-slide-wrap[data-slide-id="'+attachment.id+'"]').length > 0 ){
					alert( wp_simple_flexslider_editor_params.no_duplicate_message.replace( '%filename%', attachment.filename ) );

					continue;
				}
	 
				// Prepare template
				slide_wrap = $('#template-wp-simple-flexslider-slide-wrap').clone().html();

				// Prepare input name
				var name_slide_id 		= "slideshow["+attachment.id+"][slide_id]";
				var name_slide_caption 	= "slideshow["+attachment.id+"][slide_caption]";

				// Append
				$('.slides-wrap').append( slide_wrap );

				// Modify data
				$('.slides-wrap .wp-simple-flexslider-slide-wrap:last img').attr({ 'src' : attachment.url, 'alt' : attachment.caption });
				$('.slides-wrap .wp-simple-flexslider-slide-wrap:last .wp-simple-flexslider-slide-id').attr({ 'name' : name_slide_id, 'value' : attachment.id });
				$('.slides-wrap .wp-simple-flexslider-slide-wrap:last .wp-simple-flexslider-slide-caption').attr({ 'name' : name_slide_caption, 'value' : attachment.caption });

				// Hide no slide notice
				$('.no-wp-simple-flexslider-slide-notice').hide();

			};			
	    });
	 
	    // Finally, open the modal
	    file_frame.open();
	});

	/**
	* Removing slide mechanism
	*/
	$('body').on( 'click', '.wp-simple-flexslider-slide-remove', function(e){
		e.preventDefault();

		$(this).parents('.wp-simple-flexslider-slide-wrap').remove();

		/**
		* Display no slide yet notice if there's no more wp-simple-flexslider-slide-wrap
		*/
		if( $('.wp-simple-flexslider-slide-wrap').length == 0 ){
			$('.no-wp-simple-flexslider-slide-notice').show();
		}
	});

	/**
	* Removing all slide mechanism
	*/
	$('body').on( 'click', '.wp-simple-flexslider-slide-remove-all', function(e){
		e.preventDefault();

		$('.wp-simple-flexslider-slide-wrap').remove();

		/**
		* Display no slide yet notice
		*/
		$('.no-wp-simple-flexslider-slide-notice').show();
	});
});