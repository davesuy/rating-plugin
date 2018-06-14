<?php

/**
 * Plugin Name: Lateral Pixel Rating 
 * Description: Plugin for Rating
 * Author: Dave Ramirez
 */

if( ! defined( 'ABSPATH' ) ) {
	return;
} 

require_once 'includes/class-settings.php';
require_once 'includes/class-checking.php';
require_once 'includes/class-handler.php';
require_once 'includes/class-display.php';


class Rating_App {

	private static $instance;
	protected static $options_prefix = 'suy_rating';
	protected static $options_short_prefix = 'srating';
	public $controllers = array();


	public function __construct() {

		$this->controllers['Rating_Settings'] = new Rating_Settings;
		$this->controllers['Rating_Checking'] = new Rating_Checking;
		$this->controllers['Rating_Checking'] = new Rating_Handler;

		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles_and_scripts' ));
		add_action( 'admin_enqueue_scripts', array($this,'admin_include_myuploadscript'));
		add_filter( 'the_excerpt', array($this, 'lp_the_excerpt_more_link'), 40 );
		add_filter( 'get_the_excerpt', array($this, 'lp_the_excerpt_more_link'), 40 );
	}

	public static function generate_instance() {

		if (!isset($GLOBALS[static::class]) || is_null($GLOBALS[static::class])) {
			$GLOBALS[static::class] = new static();
		}

  	}

  	public function enqueue_styles_and_scripts() {
		wp_enqueue_style( 'rating-css', plugin_dir_url( __FILE__ ) . '/assets/rating.css', array(), '', 'screen' );
	    wp_register_script( 'rating-js', plugin_dir_url( __FILE__ ) . '/assets/rating.js', array('jquery'), '', true );
	    wp_localize_script( 'rating-js', 'lp_object', array(
	        'ajax_url' => admin_url( 'admin-ajax.php' ),
	        'nonce'    => wp_create_nonce( 'lp_rating' ),
	        'text'     => array(
	            'close_rating' => __( 'Close Rating', 'lp' ),
	            'rate_it' => __( 'Rate It', 'lp' ),
	            'choose_rate' => __( 'Choose a Rate', 'lp' ),
	            'submitting' => __( 'Submitting...', 'lp' ),
	            'thank_you' => __( 'Thank You for Your Rating!', 'lp' ),
	            'submit' => __( 'Submit', 'lp' ),
	        )
	    ));
	    wp_enqueue_script( 'rating-js' );
  	}

  	public function admin_include_myuploadscript() {
		
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
	 
 		wp_enqueue_script( 'ratinguploadscript',  plugin_dir_url( __FILE__ ) . '/assets/admin-rating.js', array('jquery'), null, false );

 		   wp_localize_script( 'ratinguploadscript', 'lp_object', array(
	        'ajax_url' => admin_url( 'admin-ajax.php' ),
	        'nonce'    => wp_create_nonce( 'lp_rating' ),
	        'text'     => array(
	            'close_rating' => __( 'Close Rating', 'lp' ),
	            'rate_it' => __( 'Rate It', 'lp' ),
	            'choose_rate' => __( 'Choose a Rate', 'lp' ),
	            'submitting' => __( 'Submitting...', 'lp' ),
	            'thank_you' => __( 'Thank You for Your Rating!', 'lp' ),
	            'submit' => __( 'Submit', 'lp' ),
	        )
	    ));
	}

	public function lp_the_excerpt_more_link( $excerpt ){
		global $post;		

		$rating_display = new Rating_Display;
		$rating_output = $rating_display->display_rating();
	   
	    $excerpt = $excerpt;

	    if(has_excerpt() ) {
    		$excerpt .= '<div class="lp-rating-display-container">';
    				$excerpt .=	$rating_output;
    		$excerpt .= '</div>';
	      }

	    return $excerpt;
	}



  	public static function delete_options_and_meta() {
	    global $wpdb;

	    delete_option(self::$options_prefix);
	    $wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => self::$options_prefix) );
  	}



}

Rating_App::generate_instance();


//-- On uninstallation, delete plugin-related database stuffs.
register_uninstall_hook( __FILE__, Rating_App::delete_options_and_meta());


