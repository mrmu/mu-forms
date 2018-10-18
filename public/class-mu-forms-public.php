<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Mu_Forms
 * @subpackage Mu_Forms/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mu_Forms
 * @subpackage Mu_Forms/public
 * @author     Audi Lu <mrmu@mrmu.com.tw>
 */
class Mu_Forms_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mu-forms-public.css', array(), filemtime( plugin_dir_path(__FILE__) . 'css/mu-forms-public.css'), 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mu-forms-public.js', array( 'jquery' ), filemtime( plugin_dir_path(__FILE__) . 'js/mu-forms-public.js'), false );
		wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js');

		wp_localize_script( $this->plugin_name, 'muforms', array('ajaxurl' => admin_url('admin-ajax.php')) );
	}

	public function add_shortcodes() {
		add_shortcode( 'muform', array($this, 'muform_func') );
	}

	public function muform_func($atts) {
		$atts = shortcode_atts( array(
			'id' => 1,
		), $atts, 'muform' );
	
		$google_recaptcha_site_key = '6LfAo3UUAAAAAMY3c_OV0Z1n6gLw7WV_7TWOs37W';

		$form_content = $atts['id'];
		$form_content .= '
			<form class="muform" method="POST">
				<table>
				<tr>
					<td>
						<div>發票號碼 <input type="text" name="invoice_en" placeholder="英文 2 碼"> <input type="text" name="invoice_num" placeholder="數字 8 碼"></div>
						<div>姓名 <input type="text" name="user_name"></div>
						<div>手機 <input type="mobile" name="user_mobile"></div>
						<div>驗證碼 <div class="g-recaptcha" data-sitekey="'.$google_recaptcha_site_key.'"></div> </div>
					</td>
					<td>
						<div><label><input type="checkbox" name="agree1"> 我已瞭解並同意抽獎活動辦法</label></div>
						<div><label><input type="checkbox" name="agree2"> 我已瞭解並同意個資蒐集及使用規定</label></div>
						<div><label><input type="checkbox" name="agree3"> 本次所填寫之資料將作為會員資料之修正</label></div>
						<div><input type="submit" data-muform="'.$atts['id'].'" value="送出資料"></div>
					</td>
				</tr>
				</table>
			</form>
		';
		return $form_content;
	}

}
