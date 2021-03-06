# Mu Forms

此外掛可協助建立收集資料用的表單，並在後台匯出 XLS 檔。
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
1. Use shortcode [muform slug=mu-form-slug] to display form.
1. Please build the frontend data validation by yourself.
2. Please use document.do_muform_submit('your_muform', callback(result, response){...}) to submit form data to admin and get responses. For example, if the form class is "muform":

```javascript
// form name is always 'form.muform'
$(document).on('submit', 'form.muform', function(e){
    e.preventDefault();
    if (confirm('Are you sure?')) {
        // lock the submit button to prevent more clicks at the same time
        $('form.muform input[type=submit]').prop('disabled', true);
        document.do_muform_submit('form.muform', function(result, res){
            // alert success/error message from backend
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
            // unlock the submit button
            $('form.muform input[type=submit]').prop('disabled', false);
        });
    }
});
```

### Export submitted data of Mu Form
Please go to admin "Settings > Mu Form" to export to XLS.

## 1.0.0
Initialization

