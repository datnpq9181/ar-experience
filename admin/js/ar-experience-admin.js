(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	jQuery(document).ready(function($) {
		$('#activate_license').on('click', function(e) {
			e.preventDefault();
			var licenseKey = $('#license_key').val();
			
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'validate_license_ajax',
					license_key: licenseKey,
				},
				success: function(response) {
					if (response.success) {
						$('#license_message').html('<div class="updated"><p>' + response.data.message + '</p></div>');
					} else {
						$('#license_message').html('<div class="error"><p>' + response.data.message + '</p></div>');
					}
				},
				error: function(response) {
					$('#license_message').html('<div class="error"><p>There was an error during the request.</p></div>');
				}
			});
		});
	});
	

})( jQuery );
