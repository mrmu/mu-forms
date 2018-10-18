(function( $ ) {
	'use strict';

	$(function(){
		$(document).on('submit', 'form.muform', function(){
			if (confirm('確定送出資料嗎？')) {
				$('form.muform input[type=submit]').prop('disabled', true);
				$.ajax({
					async: true, //mimic POST use false
					type: 'POST',
					url: muforms.ajaxurl,
					data: {
						action: 'ajax_submit_func',
						inputs: $(this).serialize(),
					},
					dataType: 'JSON',
					success: function(res) {
						alert(res.text);
						if (res.code == '0'){
							//do sth when err...
						}
						if (res.code == '1'){
							$('form.muform')[0].reset();
						}
						grecaptcha.reset();
						$('form.muform input[type=submit]').prop('disabled', false);
					},
					error:function (xhr, ajaxOptions, thrownError){
						alert(ajaxOptions+':'+thrownError);
						grecaptcha.reset();
						$('form.muform input[type=submit]').prop('disabled', false);
					}
				});	
			}
			return false;
		});
	});

})( jQuery );
