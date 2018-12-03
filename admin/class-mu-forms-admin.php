<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Mu_Forms
 * @subpackage Mu_Forms/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mu_Forms
 * @subpackage Mu_Forms/admin
 * @author     Audi Lu <mrmu@mrmu.com.tw>
 */
class Mu_Forms_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mu_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mu_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mu-forms-admin.css', array(), filemtime( plugin_dir_path(__FILE__) . 'css/mu-forms-admin.css'), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mu_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mu_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mu-forms-admin.js', array( 'jquery' ), filemtime( plugin_dir_path(__FILE__) . 'js/mu-forms-admin.js'), false );
		wp_localize_script( 
			$this->plugin_name, 
			'muforms_adm', 
			array(
				'adm_muform_url' => wp_nonce_url(admin_url('options-general.php?page=mu-forms'), 'muform_export_xls'),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'text_plz_select' => esc_html__( 'Please select one form.', $this->plugin_name ),
			) 
		);
	}

	public function ajax_load_export_html_func() {
		
		$s_date = sanitize_text_field($_POST['s_date']);
		$e_date = sanitize_text_field($_POST['e_date']);
		$muform_id = absint($_POST['muform_id']);
		$rtn_ary = array('code'=>1, 'text'=>'');
		$content = '';
		if (current_user_can('manage_options')) {
			if ($muform_id > 0) {
				$content .= '<table>';
				$content .= '<tr>';
				// get muform data by title (muform id)
				global $wpdb;
		
				// $muform_id = 15;
				// $s_date = '2018-11-19';
				// $e_date = '2018-11-21';
		
				// get mu form fields' name
				$muform_fd_names = array();
				$muform_fd_titles = array();
				$muform_fields = get_post_meta($muform_id, 'muform_fields', true);
				
				if (!empty($muform_fields['name']) && is_array($muform_fields['name'])) {
					$i = 0;
					foreach ($muform_fields['name'] as $fd_name) {
						$fd_title = $muform_fields['export_title'][$i];
						if ($fd_title) {
							$content .= '<th>'.$fd_title.'</th>';
							$muform_fd_names[] = $fd_name;
							$muform_fd_titles[] = $fd_title;
						}
						$i++;
					}
					// last column
					$content .= '<th>Date</th>';
				}
				$content .= '</tr>';
		
				$rst = $wpdb->get_results( 
					$wpdb->prepare( 
						"SELECT ID, post_date FROM $wpdb->posts WHERE 
							post_title = %s AND post_type = %s AND post_status = 'private' AND post_date Between '%s' AND '%s'", 
						$muform_id, 
						'muform_data',
						$s_date,
						$e_date
					)
				);
				$i = 2;
		
				foreach ( $rst as $row ) {
					$j = 0;
					$muform_data_id = $row->ID;
					$muform_data_date = $row->post_date;
					$content .= '<tr>';
					foreach ($muform_fd_names as $fd_name) {
						$fd_val = get_post_meta($muform_data_id, 'muform_fd_'.$fd_name, true);
						$content .= '<td>'.$fd_val."</td>";
						$j++;
					}
					$content .= '<td>'.$muform_data_date.'</td>';
					$content .= '</tr>';
					$i++;
				}
				$content .= '</table>';
			}
		}
		$rtn_ary['text'] = $content;
		echo json_encode($rtn_ary, JSON_FORCE_OBJECT);
		die();
	}

	public function export_to_xls_check() {
		if (is_admin() && isset($_GET['export_xls']) && $_GET['export_xls']) {
			$s_date = sanitize_text_field($_GET['s_date']);
			$e_date = sanitize_text_field($_GET['e_date']);

			$nonce = $_REQUEST['_wpnonce'];
			if (!wp_verify_nonce($nonce, 'muform_export_xls') ) {
				wp_die('Illegal operation! Go back to <a href="'.admin_url('options-general.php?page=mu-forms').'">Mu Forms</a>.');
			}
			if (current_user_can('manage_options')) {
				$muform_id = absint($_GET['export_xls']);
				if ($muform_id > 0) {
					//TBD: max columns limit: 26
					$col_name = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
					$export_filename = get_the_title($muform_id).'('.$s_date.'-To-'.$e_date.').xls';
					$spreadsheet = new Spreadsheet(); 
					$Excel_writer = new Xls($spreadsheet); 
	
					$spreadsheet->setActiveSheetIndex(0);
					$activeSheet = $spreadsheet->getActiveSheet();
					$activeSheet->setTitle("MuForm Data");  //設定標題
					$activeSheet->getDefaultColumnDimension()->setWidth(20);

					$s_date = date('Y-m-d H:i:s', strtotime($s_date.'00:00:00'));
					$e_date = date('Y-m-d H:i:s', strtotime($e_date.'23:59:59'));

					// get mu form fields' name
					$muform_fd_names = array();
					$muform_fd_titles = array();
					$muform_fields = get_post_meta($muform_id, 'muform_fields', true);
					if (!empty($muform_fields['name']) && is_array($muform_fields['name'])) {
						$i = 0;
						foreach ($muform_fields['name'] as $fd_name) {
							$fd_title = $muform_fields['export_title'][$i];
							if ($fd_title) {
								$activeSheet->setCellValue($col_name[$i].'1' , $fd_title)->getStyle($col_name[$i].'1')->getFont()->setName('Noto Sans CJK')->setBold(true);
								$muform_fd_names[] = $fd_name;
								$muform_fd_titles[] = $fd_title;
							}
							$i++;
						}
						// last column
						$activeSheet->setCellValue($col_name[sizeof($muform_fd_titles)].'1' , 'Date')->getStyle($col_name[sizeof($muform_fd_titles)].'1')->getFont()->setName('Noto Sans CJK')->setBold(true);
					}

					// get muform data by title (muform id)
					global $wpdb;
					// echo "SELECT ID, post_date FROM $wpdb->posts WHERE 
					// 	post_title = '$muform_id' AND post_type = 'muform_data' AND post_status = 'private' AND post_date Between '$s_date' AND '$e_date'";

					$rst = $wpdb->get_results( 
						$wpdb->prepare( 
							"SELECT ID, post_date FROM $wpdb->posts WHERE 
								post_title = %s AND post_type = %s AND post_status = 'private' AND post_date Between '%s' AND '%s'", 
							$muform_id, 
							'muform_data',
							$s_date,
							$e_date
						)
					);
					$i = 2;
					foreach ( $rst as $row ) {
						$j = 0;
						$muform_data_id = $row->ID;
						$muform_data_date = $row->post_date;
						foreach ($muform_fd_names as $fd_name) {
							$fd_val = get_post_meta($muform_data_id, 'muform_fd_'.$fd_name, true);
							$activeSheet->setCellValue($col_name[$j].$i , $fd_val)->getStyle($col_name[$j].$i)->getFont()->setName('Noto Sans CJK')->setBold(true);
							$j++;
						}
						// last column : post date
						$activeSheet->setCellValue($col_name[$j].$i , $muform_data_date)->getStyle($col_name[$j].$i)->getFont()->setName('Noto Sans CJK')->setBold(true);
						$i++;
					}

					header('Content-Type: application/vnd.ms-excel;charset=UTF-8');
					header('Content-Disposition: attachment;filename="'.$export_filename.'"'); 
					header('Cache-Control: max-age=0');
					$Excel_writer->save('php://output');
				}
			}
		}
	}

	public function add_mf_metabox() {
		//$id, $title, $callback, $page, $context, $priority, $callback_args
		add_meta_box('muform_fields', __('Mu Form Settings', $this->plugin_name), array($this, 'muform_metabox_func'), 'muform', 'normal', 'high');
	}

	public function muform_metabox_func() {

		global $post;
		$muform_fields =   get_post_meta($post->ID, 'muform_fields', true);
		$google_recap_secret = isset($muform_fields['google_recap_secret']) ? $muform_fields['google_recap_secret']:'';
		?>
		<div class="input_fields_wrap">
			<div>
				<?php _e('Google reCaptcha Secret：', $this->plugin_name);?><input type="text" name="muform_fields[google_recap_secret]" value="<?php echo $google_recap_secret;?>" style="width:500px;" />
			</div>
			<div style="margin-top:20px;">
				<?php _e('Field Settings：', $this->plugin_name);?>
			</div>
			<?php
			if(isset($muform_fields) && is_array($muform_fields)) {
				for($i = 0; $i < sizeof($muform_fields['name']); $i++) {
					$name = $muform_fields['name'][$i];
					$require = $muform_fields['require'][$i];
					$type = $muform_fields['type'][$i];
					$limit_num = $muform_fields['limit_num'][$i];
					$require_msg = $muform_fields['require_msg'][$i];
					$export_title = (isset($muform_fields['export_title'][$i])) ? $muform_fields['export_title'][$i] : '';
				?>
				<div>
					<input type="text" name="muform_fields[name][]" placeholder="name" value="<?php echo $name;?>">
					<input type="text" name="muform_fields[require][]" placeholder="require, true/false" value="<?php echo $require;?>" style="width:50px;">
					<input type="text" name="muform_fields[type][]" placeholder="type, alphabet/number" value="<?php echo $type;?>" style="width:80px;">
					<input type="text" name="muform_fields[limit_num][]" placeholder="limit_num" value="<?php echo $limit_num;?>" style="width:80px;">
					<input type="text" name="muform_fields[require_msg][]" placeholder="require_msg" value="<?php echo $require_msg;?>">
					<input type="text" name="muform_fields[export_title][]" placeholder="export_title" value="<?php echo $export_title;?>">
				</div>
				<?php
				}
			}
			?>
			<div style="margin-top:20px;"></div>
			<a class="add_field_button button-secondary"><?php _e('Add one field setting', $this->plugin_name);?></a>
			<div>
				<input type="text" name="muform_fields[name][]" placeholder="name" style="width:100px;">
				<input type="text" name="muform_fields[require][]" placeholder="require, true/false"/>
				<input type="text" name="muform_fields[type][]" placeholder="type, alphabet/number"/>
				<input type="text" name="muform_fields[limit_num][]" placeholder="limit_num" style="width:80px;"/>
				<input type="text" name="muform_fields[require_msg][]" placeholder="require_msg"/>
				<input type="text" name="muform_fields[export_title][]" placeholder="export_title"/>
			</div>
		</div>
		<?php
	}

	public function save_mf_metabox( $post_id ) {
		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_post' ) ) return;

		// // now we can actually save the data
		// $allowed = array(
		// 	'a' => array( // on allow a tags
		// 		'href' => array() // and those anchors can only have href attribute
		// 	)
		// );
		// If any value present in input field, then update the post meta

		if(isset($_POST['muform_fields'])) {
			$pre_save_fields = $_POST['muform_fields'];
			echo sizeof($pre_save_fields['name']);
			
			$to_save_fields = array();
			if (isset($pre_save_fields['google_recap_secret']) && $pre_save_fields['google_recap_secret']) {
				$to_save_fields['google_recap_secret'] = $pre_save_fields['google_recap_secret'];
			}

			for ($i = 0; $i < sizeof($pre_save_fields['name']); $i++) {
				if (!empty($pre_save_fields['name'][$i])){
					$to_save_fields['name'][] = $pre_save_fields['name'][$i];
					$to_save_fields['require'][] = $pre_save_fields['require'][$i];
					$to_save_fields['type'][] = $pre_save_fields['type'][$i];
					$to_save_fields['limit_num'][] = $pre_save_fields['limit_num'][$i];
					$to_save_fields['require_msg'][] = $pre_save_fields['require_msg'][$i];
					$to_save_fields['export_title'][] = $pre_save_fields['export_title'][$i];
				}
			}
			// $post_id, $meta_key, $meta_value
			update_post_meta( $post_id, 'muform_fields', $to_save_fields );
		}
	}

	// frontend muform submit
	public function ajax_submit_func() {
		global $wpdb;

		$rtn_ary = array('code' => 1);
		$inputs = array();
		parse_str($_POST['inputs'], $inputs);

		// verify slug
		$the_slug = sanitize_text_field($inputs['muform_slug']);
		$args = array(
			'name'        => $the_slug,
			'post_type'   => 'muform',
			'post_status' => 'publish',
			'numberposts' => 1
		);
		$my_posts = get_posts($args);
		if (empty($my_posts)) {
			$rtn_ary['code'] = 0;
			$rtn_ary['text'] = __('Form slug is illegal.', $this->plugin_name);
			echo json_encode($rtn_ary, JSON_FORCE_OBJECT);
			die();
		}
		$muform_post = $my_posts[0];

		// verify nonce
		if (!wp_verify_nonce( $inputs['_wpnonce'], 'muform_'.$muform_post->ID )){
			$rtn_ary['code'] = 0;
			$rtn_ary['text'] = __('Nonce is illegal, please refresh page.', $this->plugin_name);
			echo json_encode($rtn_ary, JSON_FORCE_OBJECT);
			die();
		}

		$muform_fields = get_post_meta($muform_post->ID, 'muform_fields', true);
		$google_recaptcha_secret = $muform_fields['google_recap_secret'];
		$response = wp_remote_get( add_query_arg( array(
			'secret'   => $google_recaptcha_secret,
			'response' => isset( $inputs['g-recaptcha-response'] ) ? $inputs['g-recaptcha-response'] : '',
			'remoteip' => isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
			), 'https://www.google.com/recaptcha/api/siteverify' ) 
		);
		// verify google recaptcha
		if ( is_wp_error( $response ) || empty( $response['body'] ) || ! ( $json = json_decode( $response['body'] ) ) || ! $json->success ) {
			$rtn_ary['code'] = 0;
			$rtn_ary['text'] = __('Robot verification failed, please try again.', $this->plugin_name); 
			echo json_encode($rtn_ary, JSON_FORCE_OBJECT);
			die();
		}else{
			$ary_name = $muform_fields['name'];
			$ary_require = $muform_fields['require'];
			$ary_type = $muform_fields['type'];
			$ary_limit_num = $muform_fields['limit_num'];
			$ary_require_msg = $muform_fields['require_msg'];
			$ary_export_name = $muform_fields['export_title'];
	
			$legal_input_names = array();
			for ($i = 0; $i < sizeof($ary_name); $i++) {
				$legal_input_names[$ary_name[$i]] = array(
					'name' => $ary_export_name[$i],
					'require' => $ary_require[$i],
					'type' => $ary_type[$i],
					'limit_num' => $ary_limit_num[$i],
					'require_msg' => $ary_require_msg[$i]
				);
			}

			$msg = '';
			// validation
			foreach ($legal_input_names as $input_name => $input_set) {
				$verify_err = false;
				
				// required fields is not filled
				if ($input_set['require'] === 'true' && !(isset($inputs[$input_name]) && $inputs[$input_name])) {
					$msg .= sprintf( esc_html__( "The field 「%s」 %s", $this->plugin_name ), $input_set['name'], $input_set['require_msg'])."\n";
					$verify_err = true; //error
				}

				if (isset($inputs[$input_name]) && $inputs[$input_name]) {
					$input_value = sanitize_text_field($inputs[$input_name]);
					if ($input_set['require'] === 'true') {
						if (empty($input_value)) {
							$msg .= sprintf( esc_html__( "The field 「%s」 %s", $this->plugin_name ), $input_set['name'], $input_set['require_msg'])."\n";
							$verify_err = true; //error
						}
					}
					if (!empty($input_set['limit_num'])) {
						$limit_num = intval($input_set['limit_num']);
						if (strlen($input_value) !== $limit_num) {
							$msg .= sprintf( esc_html__( "The field 「%s」 has wrong number of digits.", $this->plugin_name ), $input_set['name'])."\n";
							$verify_err = true; //error
						}
					}
					if (!empty($input_set['type'])) {
						if ($input_set['type'] == 'alphabet') {
							if (!ctype_alpha($input_value)) {
								$msg .= sprintf( esc_html__( "The field 「%s」should be in alphabet format.", $this->plugin_name ), $input_set['name'])."\n";
								$verify_err = true; //error
							}
						}
						if ($input_set['type'] == 'number') {
							if (!is_numeric($input_value)){
								$msg .= sprintf( esc_html__( "The field 「%s」 should be in number format.", $this->plugin_name ), $input_set['name'])."\n";
								$verify_err = true; //error
							}
						}
						if ($input_set['type'] == 'email') {
							if (!is_email($input_value)){
								$msg .= sprintf( esc_html__( "The field 「%s」 should be in e-mail format.", $this->plugin_name ), $input_set['name'])."\n";
								$verify_err = true; //error
							}
						}
					}
				}
				if ($verify_err === true) {
					$rtn_ary['code'] = 0;
				}
			}

			if ($rtn_ary['code'] === 1) {
				$data_id = wp_insert_post(
					array(
						'post_title' => $muform_post->ID, 
						'post_type' => 'muform_data', 
						'post_status' => 'private',
						'post_content' => 'Do not remove me unless you know what I am.'
					)
				);
				foreach ($inputs as $fd => $val) {
					update_post_meta($data_id, 'muform_fd_'.$fd, $val);
					//$msg .= "post id: {$muform_post->ID} update {$fd} as {$val} \n";
				}
				$msg .= __('Submit successfully, thank you.');
			}
		}

		$rtn_ary['text'] = $msg;
		echo json_encode($rtn_ary, JSON_FORCE_OBJECT);
		die();
	}

	public function add_admin_menu() {
		add_options_page ( 
			__('Mu Forms', $this->plugin_name), 
			__('Mu Forms', $this->plugin_name), 
			'manage_options', 
			$this->plugin_name, 
			array ( $this, 'options_page' ) 
		);
	}

	public function options_page() {
		$current = isset ( $_GET['tab'] ) ? $_GET['tab'] : 'options';
		$reset = isset ( $_GET['reset'] ) ? $_GET['reset'] : '';
		if ( isset ( $_POST['reset'] ) ) {
			if ($current == 'options') {
				$defaults = $this -> get_default_options_settings();
				update_option ( 'muforms_options_settings', $defaults );
			}
			// elseif ($current == 'content') {
			// 	$defaults = $this -> get_default_content_settings();
			// 	update_option ( 'muforms_content_settings', $defaults );
			// }
			// elseif ($current == 'style') {
			// 	$defaults = $this -> get_default_style_settings();
			// 	update_option ( 'muforms_style_settings', $defaults );
			// }
		}
		$title =  __( 'Mu Forms', $this->plugin_name );
		$tabs = array (
			'options'		=>	__( 'General', $this->plugin_name ),
			// 'content'		=>	__( 'Content', $this->plugin_name ),
			// 'style'		=>	__( 'Style', $this->plugin_name )
		);
		?>
		<div class="wrap">
			<h1><?php echo $title; ?></h1>
			<div class="outer-wrap">
				<div class="inner-wrap">
					<h2 class="nav-tab-wrapper">
						<?php foreach( $tabs as $tab => $name ) {
							$class = ( $tab == $current ) ? ' nav-tab-active' : '';
							echo "<a class='nav-tab$class' href='?page=mu-forms&tab=$tab'>$name</a>";
						} ?>
					</h2>
					<form action='options.php' method='POST'>
						<?php
						// register_setting: muforms_options, muforms_content, muforms_style
						settings_fields( 'muforms_' . strtolower ( $current ) );
						do_settings_sections( 'muforms_' . strtolower ( $current ) );
						// submit_button();
						?>
					</form>
					<!-- <form action="" method="POST">
						<p class="submit">
							<input name="reset" class="button button-secondary" type="submit" value="<?php _e( 'Reset this tab controls to defaults', $this->plugin_name ); ?>" >
							<input type="hidden" name="action" value="reset" />
						</p>
					</form> -->
				</div><!-- .inner-wrap -->
			</div><!-- .outer-wrap -->
		</div><!-- .wrap -->
		<?php
	}

	public function register_options_init() {

		register_setting ( 'muforms_options', 'muforms_options_settings', array($this, 'validate') );

		add_settings_section (
			'muforms_options_section',
			__( 'General settings', $this->plugin_name ),
			array ( $this, 'settings_section_callback' ),
			'muforms_options'
		);

		// add_settings_field (
		// 	'enable',
		// 	__( 'Enable', $this->plugin_name ),
		// 	array ( $this, 'enable_checkbox' ),
		// 	'muforms_options',
		// 	'muforms_options_section'
		// );

		add_settings_field(
			'muform_select', 
			__('Export submitted data of Mu Form', $this->plugin_name ), 
			array( $this, 'muform_select_box'), 
			'muforms_options', 
			'muforms_options_section'
		);
		
		// Set default options
		$options = get_option ( 'muforms_options_settings' );
		if ( false === $options ) {
			// Get defaults
			$defaults = $this -> get_default_options_settings();
			update_option ( 'muforms_options_settings', $defaults );
		}
	}

	public function validate($input) {
		$valid = array();
		// $valid['enable'] = (isset($input['enable']) && !empty($input['enable'])) ? 1 : 0;
		return $valid;
	}

	// default settings

	public function get_default_options_settings() {
		$defaults = array (
			// 'enable' 		=> 0,
		);
		return $defaults;
	}

	// Settings Callback

	public function settings_section_callback() {
		// echo '<p>' . __( 'Basic settings', $this->plugin_name ) . '</p>';
	}

	// Controls render

	public function muform_select_box() {
		$from_date = date('Y-m-d', strtotime(current_time('mysql').'- 7 days'));
		$to_date = date('Y-m-d', strtotime(current_time('mysql')));
		?>
		<div>
			<?php _e('Start Date：', $this->plugin_name); ?>
			<input type="date" name="export_s_date" id="export_s_date" value="<?php echo $from_date;?>"> ～
			<?php _e('End Date：', $this->plugin_name); ?>
			<input type="date" name="export_e_date" id="export_e_date" value="<?php echo $to_date;?>">
		</div>

		<select name="select_muform" id="select_muform">
			<option value="0"><?php _e('Please select', $this->plugin_name);?></option>
			<?php
			$args = array(
				'post_type' => 'muform',
				'post_status' => 'publish',
				'order'    => 'ASC'
				);              
			
			$the_query = new WP_Query( $args );
			if($the_query->have_posts() ) : 
				while ( $the_query->have_posts() ) : 
					$the_query->the_post(); 
					echo '<option value="'.$the_query->post->ID.'">'.$the_query->post->post_title.'</option>';
				endwhile;
			endif;
			?>
        </select>
		<button type="button" name="export_xls" class="btn_export_xls button button-primary" value="export_xls"><?php _e('Export to XLS', $this->plugin_name);?></button>
		<button type="button" name="export_html" class="btn_export_html button button-primary" value="export_html"><?php _e('Export to HTML', $this->plugin_name);?></button>
		<div class="display_html"></div>
	   <?php
	}

}
