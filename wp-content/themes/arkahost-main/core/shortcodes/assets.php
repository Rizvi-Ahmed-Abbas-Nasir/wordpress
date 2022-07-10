<?php

/**
 * Class for managing plugin assets
 */
class king_shortcode_assets {

	/**
	 * Set of queried assets
	 *
	 * @var array
	 */
	static $assets = array( 'css' => array(), 'js' => array() );

	/**
	 * Constructor
	 */
	function __construct() {
		// Register
		add_action( 'wp_head',                     array( __CLASS__, 'register' ) );
		add_action( 'admin_head',                  array( __CLASS__, 'register' ) );
		add_action( 'sc/generator/preview/before', array( __CLASS__, 'register' ) );
		add_action( 'sc/examples/preview/before',  array( __CLASS__, 'register' ) );
		// Enqueue
		add_action( 'wp_footer',                   array( __CLASS__, 'enqueue' ) );
		add_action( 'admin_footer',                array( __CLASS__, 'enqueue' ) );
		// Print
		add_action( 'sc/generator/preview/after',  array( __CLASS__, 'prnt' ) );
		add_action( 'sc/examples/preview/after',   array( __CLASS__, 'prnt' ) );
		
		add_action('wp_enqueue_scripts', array( __CLASS__, 'default_enqueue'), 0 );
		
	}

	/**
	 * Register assets
	 */
	public static function default_enqueue() {	
		
		
	}
	/**
	 * Register assets
	 */
	public static function register() {
	
		// Chart.js
		wp_register_script( 'work',null, array( 'jquery' ), '0.2', true );
		
		wp_register_script('king-shortcode', THEME_URI.'/core/shortcodes/assets/js/shortcode.js', false, KING_VERSION, true );
		wp_enqueue_script('king-shortcode');
		
		// SimpleSlider
		wp_register_script( 'simpleslider', THEME_URI.'/core/shortcodes/assets/js/simpleslider.js' , array( 'jquery' ), '1.0.0', true );
		wp_register_style( 'simpleslider', THEME_URI.'/core/shortcodes/assets/css/simpleslider.css', false, '1.0.0', 'all' );
		// Owl Carousel
		wp_register_script( 'owl-carousel', THEME_URI.'/core/shortcodes/assets/js/owl-carousel.js', array( 'jquery' ), '1.3.2', true );
		wp_register_style( 'owl-carousel', THEME_URI.'/core/shortcodes/assets/css/owl-carousel.css' , false, '1.3.2', 'all' );
		wp_register_style( 'owl-carousel-transitions', THEME_URI.'/core/shortcodes/assets/css/owl-carousel-transitions.css' , false, '1.3.2', 'all' );

		wp_register_style( 'qtip', THEME_URI.'/core/shortcodes/assets/css/qtip.css', false, '2.1.1', 'all' );
		wp_register_script( 'qtip', THEME_URI.'/core/shortcodes/assets/js/qtip.js' , array( 'jquery' ), '2.1.1', true );
		// jsRender
		wp_register_script( 'jsrender', THEME_URI.'/core/shortcodes/assets/js/jsrender.js' , array( 'jquery' ), '1.0.0-beta', true );
		// Magnific Popup
		wp_register_style( 'magnific-popup', THEME_URI.'/core/shortcodes/assets/css/magnific-popup.css' , false, '0.9.7', 'all' );
		wp_register_script( 'magnific-popup', THEME_URI.'/core/shortcodes/assets/js/magnific-popup.js' , array( 'jquery' ), '0.9.7', true );
		wp_localize_script( 'magnific-popup', 'su_magnific_popup', array(
				'close'   => __( 'Close (Esc)', 'arkahost' ),
				'loading' => __( 'Loading...', 'arkahost' ),
				'prev'    => __( 'Previous (Left arrow key)', 'arkahost' ),
				'next'    => __( 'Next (Right arrow key)', 'arkahost' ),
				'counter' => sprintf( __( '%s of %s', 'arkahost' ), '%curr%', '%total%' ),
				'error'   => sprintf( __( 'Failed to load this link. %sOpen link%s.', 'arkahost' ), '<a href="%url%" target="_blank"><u>', '</u></a>' )
			) );

		// Swiper
		wp_register_script( 'classyloader', THEME_URI.'/core/shortcodes/assets/js/jquery.classyloader.min.js' , array( 'jquery' ), '2.6.1', true );
		
		
		wp_register_script( 'progress-bar', THEME_URI.'/core/shortcodes/assets/js/progress.js' , array( 'jquery' ), '2.6.1', true );
		wp_register_style( 'progress-bar', THEME_URI.'/core/shortcodes/assets/css/ui.progress-bar.css', false , KING_VERSION, 'all' );
		
		wp_register_script( 'swiper', THEME_URI.'/core/shortcodes/assets/js/swiper.js' , array( 'jquery' ), '2.6.1', true );
		// jPlayer
		wp_register_script( 'jplayer', THEME_URI.'/core/shortcodes/assets/js/jplayer.js' , array( 'jquery' ), '2.4.0', true );
		// Options page
		wp_register_style( 'king-options-page', THEME_URI.'/core/shortcodes/assets/css/options-page.css' , false, KING_VERSION, 'all' );
		wp_register_script( 'king-options-page', THEME_URI.'/core/shortcodes/assets/js/options-page.js' , array( 'magnific-popup', 'jquery-ui-sortable', 'ace', 'jsrender' ), KING_VERSION, true );
		wp_localize_script( 'king-options-page', 'su_options_page', array(
				'upload_title'  => __( 'Choose files', 'arkahost' ),
				'upload_insert' => __( 'Add selected files', 'arkahost' ),
				'not_clickable' => __( 'This button is not clickable', 'arkahost' )
			) );
		// Generator
		wp_register_style( 'king-generator', THEME_URI.'/core/shortcodes/assets/css/generator.css', array( 'farbtastic', 'magnific-popup' ), KING_VERSION, 'all' );
		wp_register_script( 'king-generator', THEME_URI.'/core/shortcodes/assets/js/generator.js', array( 'farbtastic', 'magnific-popup', 'qtip' ), KING_VERSION, true );
		wp_localize_script( 'king-generator', 'king_Shortcode_Generator', array(
				'upload_title'         => __( 'Choose file', 'arkahost' ),
				'upload_insert'        => __( 'Insert', 'arkahost' ),
				'isp_media_title'      => __( 'Select images', 'arkahost' ),
				'isp_media_insert'     => __( 'Add selected images', 'arkahost' ),
				'presets_prompt_msg'   => __( 'Please enter a name for new preset', 'arkahost' ),
				'presets_prompt_value' => __( 'New preset', 'arkahost' ),
				'last_used'            => __( 'Last used settings', 'arkahost' )
			) );
		// Shortcodes stylesheets
		wp_register_style( 'king-flex-slider-css', THEME_URI.'/css/flexslider.css', false, KING_VERSION, 'all' );
		wp_register_style( 'king-content-shortcodes', self::skin_url( 'content-shortcodes.css' ), false, KING_VERSION, 'all' );
		wp_register_style( 'king-box-shortcodes', self::skin_url( 'box-shortcodes.css' ), false, KING_VERSION, 'all' );
		wp_register_style( 'king-media-shortcodes', self::skin_url( 'media-shortcodes.css' ), false, KING_VERSION, 'all' );
		wp_register_style( 'king-galleries-shortcodes', self::skin_url( 'galleries-shortcodes.css' ), false, KING_VERSION, 'all' );
		wp_register_style( 'king-players-shortcodes', self::skin_url( 'players-shortcodes.css' ), false, KING_VERSION, 'all' );
		// Shortcodes scripts

		wp_register_script( 'king-flex-slider', THEME_URI.'/js/jquery.flexslider.js', array( 'jquery', 'swiper' ), KING_VERSION, true );
		
		wp_register_script( 'king-galleries-shortcodes', THEME_URI.'/core/shortcodes/assets/js/galleries-shortcodes.js', array( 'jquery', 'swiper' ), KING_VERSION, true );
		wp_register_script( 'king-players-shortcodes', THEME_URI.'/core/shortcodes/assets/js/players-shortcodes.js', array( 'jquery', 'jplayer' ), KING_VERSION, true );
		wp_register_script( 'king-other-shortcodes', THEME_URI.'/core/shortcodes/assets/js/other-shortcodes.js', array( 'jquery' ), KING_VERSION, true );
		
	}

	/**
	 * Enqueue assets
	 */
	public static function enqueue() {
	
		// Get assets query and plugin object
		
		$assets = self::assets();
		// Enqueue stylesheets
		foreach ( $assets['css'] as $style ) wp_enqueue_style( $style );
		// Enqueue scripts
		foreach ( $assets['js'] as $devnript ) wp_enqueue_script( $devnript );
		// Hook to dequeue assets or add custom
		do_action( 'sc/assets/enqueue', $assets );
	}

	/**
	 * Print assets without enqueuing
	 */
	public static function prnt() {
		// Prepare assets set
		$assets = self::assets();

		// Enqueue stylesheets
		wp_print_styles( $assets['css'] );
		// Enqueue scripts
		wp_print_scripts( $assets['js'] );
		// Hook
		do_action( 'sc/assets/print', $assets );
	}

	/**
	 * Print custom CSS
	 */
	public static function custom_css() {
		// Get custom CSS and apply filters to it
		$custom_css = apply_filters( 'sc/assets/custom_css', str_replace( '&#039;', '\'', html_entity_decode( (string) get_option( 'su_option_custom-css' ) ) ) );
		// Print CSS if exists
		if ( $custom_css ) echo "\n\n<!-- Shortcodes Ultimate custom CSS - begin -->\n<style type='text/css'>\n" . stripslashes( str_replace( array( '%theme_url%', '%home_url%', '%plugin_url%' ), array( trailingslashit( get_stylesheet_directory_uri() ), trailingslashit( home_url() ), trailingslashit( THEME_URI.'/core/shortcodes' ) ), $custom_css ) ) . "\n</style>\n<!-- Shortcodes Ultimate custom CSS - end -->\n\n";
	}

	/**
	 * Styles for visualised shortcodes
	 */
	public static function mce_css( $mce_css ) {
		if ( ! empty( $mce_css ) ) $mce_css .= ',';
		return THEME_URI.'/core/shortcodes/assets/css/tinymce.css';
	}

	/**
	 * TinyMCE plugin for visualised shortcodes
	 */
	public static function mce_js( $plugins ) {

		return THEME_URI.'/core/shortcodes/assets/js/tinymce.js';

	}

	/**
	 * Add asset to the query
	 */
	public static function add( $type, $handle ) {
		// Array with handles
		if ( is_array( $handle ) ) { foreach ( $handle as $h ) self::$assets[$type][$h] = $h; }
		// Single handle
		else self::$assets[$type][$handle] = $handle;
	}
	/**
	 * Get queried assets
	 */
	public static function assets() {
		// Get assets query
		$assets = self::$assets;
		// Apply filters to assets set
		$assets['css'] = array_unique( ( array ) apply_filters( 'sc/assets/css', ( array ) array_unique( $assets['css'] ) ) );
		$assets['js'] = array_unique( ( array ) apply_filters( 'sc/assets/js', ( array ) array_unique( $assets['js'] ) ) );
		// Return set
		return $assets;
	}

	/**
	 * Helper to get full URL of a skin file
	 */
	public static function skin_url( $file = '' ) {
		return THEME_URI.'/core/shortcodes/assets/css/'.$file;
	}
}

new king_shortcode_assets;

/**
 * Helper function to add asset to the query
 *
 * @param string  $type   Asset type (css|js)
 * @param mixed   $handle Asset handle or array with handles
 */
function king_su_query_asset( $type, $handle ) {
	king_shortcode_assets::add( $type, $handle );
}

/**
 * Helper function to get current skin url
 *
 * @param string  $file Asset file name. Example value: box-shortcodes.css
 */
function king_su_skin_url( $file ) {
	return king_shortcode_assets::skin_url( $file );
}
