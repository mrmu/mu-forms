# Mu Forms
To help users build forms quickly and then export the data input from forms in xls format.

## Install
1. Download this plugin.
2. `$ cd /your-wp-site-path/wp-content/plugins/mu-forms`
3. `$ composer install`
4. Activate plugin 'Mu Forms' in plugins of admin.

## Usage

### Admin

1. Add a new Mu Form in admin.
2. Add form html code in content editor.
3. Add fields setting and make sure the fields' name are matched with what you write in html code.
4. Fill fields settings.

### Frontend Form
1. Please build the frontend data validation by yourself.
2. Please use document.do_muform_submit('your_muform', callback(result, response){...}) to submit form data to admin and get responses. For example, if the form class is "muform":
`
$(document).on('submit', 'form.muform', function(e){
	e.preventDefault();
	if (confirm('Are you sure?')) {
		$('form.muform input[type=submit]').prop('disabled', true);
		
		document.do_muform_submit('form.muform', function(result, res){
			alert(res.text);
			if (result === 'success') {						
				if (res.code == '0'){
					// do sth when data err...
				}
				if (res.code == '1'){
					// reset form fields
					$('form.muform')[0].reset();
				}
			}else if(result === 'error') {
				// do sth when ajax err ...
			}
			$('form.muform input[type=submit]').prop('disabled', false);
		});
	}
	return false;
});
`

### Export submitted data of Mu Form
Please go to admin "Settings > Mu Form" to export to XLS.

## 1.0.0
Initialization

