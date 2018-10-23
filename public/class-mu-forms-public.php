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

	public function create_post_type(){
		$labels = array(
			'name' => __('Mu Form', $this->plugin_name),
			'singular_name' => __('Mu Form', $this->plugin_name),
			'menu_name'          => __('Mu Form', $this->plugin_name),
			'name_admin_bar'     => __('Mu Form', $this->plugin_name),
			'add_new'            => __('Add New', $this->plugin_name),
			'add_new_item'       => __('Add New Form', $this->plugin_name),
			'new_item'           => __('New Form', $this->plugin_name),
			'edit_item'          => __('Edit Form', $this->plugin_name),
			'view_item'          => __('View Form', $this->plugin_name),
			'all_items'          => __('All Forms', $this->plugin_name),
			'search_items'       => __('Search Forms', $this->plugin_name),
			'not_found'          => __('No Form Found', $this->plugin_name),
			'not_found_in_trash' => __('No Form in trash.', $this->plugin_name)
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'muform'),
			//'menu_icon' => get_bloginfo('stylesheet_directory').'/images/linkicon_cfg.png',
			//'taxonomies' => array('muform_cate'),
			'supports' => array( 'title', 'editor', 'author' ),
			'show_in_nav_menus' => false,
			'exclude_from_search' => true
		);
		register_post_type( 'muform', $args);

		$labels = array(
			'name' => __('Mu Form Data', $this->plugin_name),
			'singular_name' => __('Mu Form Data', $this->plugin_name),
			'menu_name'          => __('Mu Form Data', $this->plugin_name),
			'name_admin_bar'     => __('Mu Form Data', $this->plugin_name),
			'add_new'            => __('Add New', $this->plugin_name),
			'add_new_item'       => __('Add New Form Data', $this->plugin_name),
			'new_item'           => __('New Form Data', $this->plugin_name),
			'edit_item'          => __('Edit Form Data', $this->plugin_name),
			'view_item'          => __('View Form Data', $this->plugin_name),
			'all_items'          => __('All Forms Data', $this->plugin_name),
			'search_items'       => __('Search Forms Data', $this->plugin_name),
			'not_found'          => __('No Form Data Found', $this->plugin_name),
			'not_found_in_trash' => __('No Form Data in trash.', $this->plugin_name)
		);
		$args = array(
			'labels' => $labels,
			'public' => false,
			'has_archive' => true,
			'rewrite' => array('slug' => 'muform_data'),
			//'menu_icon' => get_bloginfo('stylesheet_directory').'/images/linkicon_cfg.png',
			//'taxonomies' => array('muform_cate'),
			'supports' => array( 'title', 'editor' ),
			'show_in_nav_menus' => false,
			'exclude_from_search' => true
		);
		register_post_type( 'muform_data', $args);
	}

	public function disable_cpt_view() {
		$queried_post_type = get_query_var('post_type');
		$private_post_types = array(
			'muform', 'muform_data'
		);
		if ( is_single() && in_array($queried_post_type, $private_post_types) ) {
			wp_redirect( home_url(), 301 );
			exit;
		}
	}
	
	public function create_taxonomy() {
		//TBD
	}

	public function add_shortcodes() {
		add_shortcode( 'muform', array($this, 'muform_func') );
	}

	public function muform_func($atts) {
		$atts = shortcode_atts( array(
			'slug' => '',
		), $atts, 'muform' );
		
		$the_slug = sanitize_text_field($atts['slug']);

		if (empty($the_slug)) return false;

		$args = array(
			'name'        => $the_slug,
			'post_type'   => 'muform',
			'post_status' => 'publish',
			'numberposts' => 1
		);
		$my_posts = get_posts($args);

		$muform_post = $my_posts[0];

		if ( !empty($muform_post) ) {
			$muform_post_id = $muform_post->ID;
			$content_post = get_post($muform_post_id);
			$form_content = '<form class="muform" method="POST">';
			$nonce = wp_create_nonce( 'muform_'.$muform_post_id );
			$form_content .= '<input type="hidden" id="_wpnonce" name="_wpnonce" value="'.$nonce.'" />';
			$form_content .= '<input type="hidden" name="muform_slug" value="'.$the_slug.'">';
			if (!empty($content_post)) {
				$form_content .= $content_post->post_content;
				$form_content = apply_filters('the_content', $form_content);
				$form_content = str_replace(']]>', ']]&gt;', $form_content);
			}
			$form_content .= '</form>';

			// $form_content .= '
			// 	<form class="muform" method="POST">
			// 		<table>
			// 		<tr>
			// 			<td>
			// 				<div>發票號碼 <input type="text" name="invoice_en" placeholder="英文 2 碼"> <input type="text" name="invoice_num" placeholder="數字 8 碼"></div>
			// 				<div>姓名 <input type="text" name="user_name"></div>
			// 				<div>手機 <input type="mobile" name="user_mobile"></div>
			// 				<div>驗證碼 <div class="g-recaptcha" data-sitekey="6LfAo3UUAAAAAMY3c_OV0Z1n6gLw7WV_7TWOs37W"></div> </div>
			// 			</td>
			// 			<td>
			// 				<div><label><input type="checkbox" name="agree1"> 我已瞭解並同意抽獎活動辦法</label></div>
			// 				<div><label><input type="checkbox" name="agree2"> 我已瞭解並同意個資蒐集及使用規定</label></div>
			// 				<div><label><input type="checkbox" name="agree3"> 本次所填寫之資料將作為會員資料之修正</label></div>
			// 				<div><input type="submit" value="送出資料"></div>
			// 			</td>
			// 		</tr>
			// 		</table>
			// 	</form>
			// ';
			return $form_content;
		}
		return false;
	}
}
