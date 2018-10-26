(function( $ ) {
	'use strict';

	document.do_muform_submit = function(muform, callback) {
		$.ajax({
			async: true,
			type: 'POST',
			url: muforms.ajaxurl,
			data: {
				action: 'muform_ajax_submit_func',
				inputs: $(muform).serialize(),
			},
			dataType: 'JSON',
			success: function(res) {
				callback('success', res);
				grecaptcha.reset();
			},
			error:function (xhr, ajaxOptions, thrownError){
				callback('error', ajaxOptions+':'+thrownError);
				grecaptcha.reset();
			}
		});
	}

	$(function(){

	});

})( jQuery );
