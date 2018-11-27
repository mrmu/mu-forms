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
						'<input type="text" name="muform_fields[name][]" placeholder="name" style="width:100px;"/>'+
						'<input type="text" name="muform_fields[require][]" placeholder="require"/>'+
						'<input type="text" name="muform_fields[type][]" placeholder="type"/>'+
						'<input type="text" name="muform_fields[limit_num][]" placeholder="limit_num" style="width:80px;"/>'+
						'<input type="text" name="muform_fields[require_msg][]" placeholder="require_msg"/>'+
						'<input type="text" name="muform_fields[export_title][]" placeholder="export_title"/>'+
						'<a href="#" class="button remove_field">X</a>'+
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

		$(document).on('click', '.btn_export_html', function(e) {
			var muform_id = $('#select_muform').val();
			var export_s_date = $('#export_s_date').val();
			var export_e_date = $('#export_e_date').val();

			$(function(){
				$.ajax({
					async: true, //mimic POST use false
					type: 'POST',
					url: muforms_adm.ajax_url,
					data: {
						action: 'muform_load_export_html',
						muform_id: muform_id,
						s_date: export_s_date,
						e_date: export_e_date
					},
					dataType: 'JSON',
					success: function(res) {
						if (res.code=='1'){
							//do sth when success...
							$('.display_html').html(res.text);
						}
					},
					error:function (xhr, ajaxOptions, thrownError){
						alert(ajaxOptions+':'+thrownError);
					}
				});	
			});
			
			// var open_url = muforms_adm.adm_muform_url+'&export_html='+muform_id+'&s_date='+export_s_date+'&e_date='+export_e_date;
			// if (muform_id != 0) {
			// 	var wnd = window.open(open_url);
			// }
			return false;
		});

		$(document).on('click', '.btn_export_xls', function(e) {
			var muform_id = $('#select_muform').val();
			var export_s_date = $('#export_s_date').val();
			var export_e_date = $('#export_e_date').val();
			var open_url = muforms_adm.adm_muform_url+'&export_xls='+muform_id+'&s_date='+export_s_date+'&e_date='+export_e_date;
			if (muform_id != 0) {
				var wnd = window.open(open_url);
				// setTimeout(function() {
				// 	wnd.close();
				// }, 6000);
			}
			return false;
		});
	});

})( jQuery );
