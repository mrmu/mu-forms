(function( $ ) {
	'use strict';

	$(function(){
		var max_fields      = 10; //maximum input boxes allowed
		var x = 1; //initlal text box count

		$(document).on('click', '.add_field_button', function(e){
			e.preventDefault();
			if(x < max_fields){ //max input box allowed
				x++; //text box increment
				$('.input_fields_wrap').append(
					'<div>'+
						'<input type="text" name="muform_fields[name][]" placeholder="name"/>'+
						'<input type="text" name="muform_fields[require][]" placeholder="require"/>'+
						'<input type="text" name="muform_fields[type][]" placeholder="type"/>'+
						'<input type="text" name="muform_fields[limit_num][]" placeholder="limit_num"/>'+
						'<input type="text" name="muform_fields[require_msg][]" placeholder="require_msg"/>'+
						'<input type="text" name="muform_fields[export_title][]" placeholder="export_title"/>'+
						'<a href="#" class="remove_field">Remove</a>'+
					'</div>'
				); //add input box
			}
		});

		//user click on remove text
		$('.input_fields_wrap').on('click', '.remove_field', function(e){ 
			e.preventDefault(); 
			$(this).parent('div').remove(); 
			x--;
		});

		$(document).on('click', '.btn_export_xls', function(e) {
			var muform_id = $('#select_muform').val();
			if (muform_id != 0) {
				alert(muform_id);
				var wnd = window.open(muforms_adm.adm_muform_url+'&export_xls='+muform_id);
				// setTimeout(function() {
				// 	wnd.close();
				// }, 6000);
			}
			return false;
		});
	});

})( jQuery );
