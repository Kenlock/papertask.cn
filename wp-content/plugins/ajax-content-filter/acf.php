<?php
/*
Plugin Name: Ajax Content Filter
Plugin URI: http://www.perceptionsystem.com
Description: Simple Content Filter using Ajax 
Version: 1.0
Author: Perception System
Author URI: http://www.perceptionsystem.com
Contributors: Perception System Pvt. Ltd.
*/
final class ajax_content_filter {

	private static $instance;

	private $directory_path;

	private $directory_uri;

	public function __construct() {
		
		add_action( 'plugins_loaded', array( $this, 'path_setup' ), 1 );

		add_action('init', array( $this, 'ajax_filter_content_data' ) );
		
		add_action( 'init', array( $this, 'acf_code_button' ) );
		
		add_shortcode('ACF', array( $this, 'ajax_filter_content' ) );
		
		add_action('wp_enqueue_scripts', array( $this, 'ACF_scripts' ) );
		
		add_action('wp_head', array( $this, 'custom_header' ),0);
		
		add_action('wp_ajax_user_log_callback', array( $this, 'custom_header' ),0);
		
		add_action('wp_ajax_my_action', array( $this, 'my_action_callback' ) );

		add_action('wp_ajax_nopriv_my_action', array( $this, 'my_action_callback' ) );
		
		add_filter('admin_head', array( $this, 'add_admin_style' ));

		register_activation_hook( __FILE__, array( __CLASS__, 'ACF_activation' ) );
	
		register_deactivation_hook(__FILE__, array( __CLASS__, 'ACF_deactivation' ) );

	}
	public function path_setup() {
		$this->directory_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->directory_uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );
	}

	public static function ACF_activation() {

	}

	public static function ACF_deactivation() {

	}

	public function ACF_scripts() {
		
		wp_enqueue_script('jquery');

		wp_register_script('ACFjs', plugins_url('js/acf_script.js', __FILE__));

		wp_enqueue_script('ACFjs');
		
		wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));	

	}
	
	public function add_admin_style(){
		
		if ( is_admin() ) {

			wp_register_style('ACFcss', plugins_url('css/acf_style.css', __FILE__));

			wp_enqueue_style('ACFcss');

		}

	}

	public function ajax_filter_content() {
		
		require_once( $this->directory_path."template.php" );
		
	}
	public function acf_code_button(){
		
		add_filter( 'mce_external_plugins', array( $this,'acf_code_add_button') );
		add_filter( 'mce_buttons', array( $this,'acf_code_register_button') );
		
	}

	public function acf_code_add_button( $plugin_array ) {
		$plugin_array['mycodebutton'] = $dir = plugins_url( 'js/acf_script.js', __FILE__ );
		return $plugin_array;
	}
	public function acf_code_register_button( $buttons ) {
		array_push( $buttons, 'codebutton' );
		return $buttons;
	}	

	public function ajax_filter_content_data() {
		$labels = array(
			'name' => 'ACF Posts',
			'singular_name' => 'ACF Posts',
			'menu_name' => 'ACF Posts',
			'edit_item' => 'Edit ACF Post ',
			'add_new'=> 'Add New ACF Post',
			'add_new_item'=> 'Add New ACF Post',
					
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'description' => 'Simple Content Filter using Ajax',
			'supports' => array('title', 'editor'),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'menu_position' => 25,
			'menu_icon' => plugins_url( 'images/perceptionlogo.png' , __FILE__ )
		);
		register_post_type('ajax_filter', $args);
	}

	public function custom_header() {
			echo '<script type="text/javascript">';
			echo 'var ajaxurl = \''.admin_url('admin-ajax.php').'\';';
			echo 'var siteurl = \''.get_site_url().'\';';
			echo '</script>';
	}

	public function my_action_callback() {
		global $wpdb;
		$id = intval($_POST['id']);
		$post = get_post($id);
		echo $post->post_content;
		die();
	}

	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

ajax_content_filter::get_instance();
?>
