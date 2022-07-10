<?php


add_action( 'wp_enqueue_scripts', 'king_enqueue_content', 10 );
add_action( 'wp_enqueue_scripts', 'king_enqueue_content_last', 9999 );
add_action('admin_enqueue_scripts', 'king_enqueue_admin');
add_action( 'admin_head', 'king_admin_head', 99999 );


function king_enqueue_content() {

	global $king;

	$css_dir = THEME_URI.'/assets/css/';
	$js_dir = THEME_URI.'/assets/js/';

	wp_enqueue_style('king-reset', king_child_theme_enqueue( $css_dir.'reset.css' ), false, KING_VERSION );
	wp_enqueue_style('king-bootstrap', king_child_theme_enqueue( $css_dir.'main_menu/bootstrap.min.css' ), false, KING_VERSION );

	if( is_home() || is_single() ){
		wp_enqueue_style('king-blog-reset', king_child_theme_enqueue( $css_dir.'blog-reset.css' ), false, KING_VERSION );
	}
	wp_enqueue_style('king-awesome', king_child_theme_enqueue( THEME_URI.'/core/assets/css/font-awesome.min.css'), false, KING_VERSION );
	wp_enqueue_style('king-simple-line', king_child_theme_enqueue( THEME_URI.'/core/assets/css/simple-line-icons.css'), false, KING_VERSION );
	wp_enqueue_style('king-etlinefont', king_child_theme_enqueue( THEME_URI.'/core/assets/css/etlinefont.css'), false, KING_VERSION );
	wp_enqueue_style('king-stylesheet', king_child_theme_enqueue( THEME_URI.'/style.css' ), false, KING_VERSION );

	if( !empty( $king->cfg['effects'] ) ){
		if( $king->cfg['effects'] == 1 ){
			wp_enqueue_style('king-effects', THEME_URI.'/core/assets/css/animate.css', false, KING_VERSION );
		}
	}

	wp_enqueue_style('king-static', king_child_theme_enqueue( $css_dir.'king.css'  ), false, KING_VERSION );
	wp_enqueue_style('king-arkahost', king_child_theme_enqueue( $css_dir.'arkahost.css'  ), false, KING_VERSION );
	wp_enqueue_style('king-shortcodes', king_child_theme_enqueue( $css_dir.'shortcodes.css' ), false, KING_VERSION );
	wp_enqueue_style('king-box-shortcodes', king_child_theme_enqueue( $css_dir.'box-shortcodes.css' ), false, KING_VERSION );
	wp_enqueue_style('king-cubeportfolio', king_child_theme_enqueue( $css_dir.'cube/cubeportfolio.min.css' ), false, KING_VERSION );

	wp_enqueue_style('king-owl-transitions', king_child_theme_enqueue( $css_dir.'owl.transitions.css' ), false, KING_VERSION );
	wp_enqueue_style('king-owl-carousel', king_child_theme_enqueue( $css_dir.'owl.carousel.css' ), false, KING_VERSION );
	wp_enqueue_style('king-loopslider', king_child_theme_enqueue( $css_dir.'loopslider.css' ), false, KING_VERSION );
	wp_enqueue_style('king-tabacc', king_child_theme_enqueue( $css_dir.'tabacc.css' ), false, KING_VERSION );
	wp_enqueue_style('king-detached', king_child_theme_enqueue( $css_dir.'detached.css' ), false, KING_VERSION );
	wp_enqueue_style('king-detached', king_child_theme_enqueue( $css_dir.'detached.css' ), false, KING_VERSION );
	wp_enqueue_style('king-revolution', king_child_theme_enqueue( $css_dir.'reslider.css' ), false, KING_VERSION );
	wp_register_style('king-bootstrap-slider', king_child_theme_enqueue( $css_dir.'/bootstrap-slider.css' ), false, KING_VERSION );
	wp_register_style('king-menu', king_child_theme_enqueue( $css_dir.'main_menu/menu.css' ), false, KING_VERSION );
	wp_register_style('king-menu-2', king_child_theme_enqueue( $css_dir.'main_menu/menu-2.css' ), false, KING_VERSION );
	wp_register_style('king-menu-3', king_child_theme_enqueue( $css_dir.'main_menu/menu-3.css' ), false, KING_VERSION );
	wp_register_style('king-menu-4', king_child_theme_enqueue( $css_dir.'main_menu/menu-4.css' ), false, KING_VERSION );
	wp_register_style('king-menu-5', king_child_theme_enqueue( $css_dir.'main_menu/menu-5.css' ), false, KING_VERSION );
	wp_register_style('king-menu-onepage', king_child_theme_enqueue( $css_dir.'main_menu/menu-onepage.css' ), false, KING_VERSION );
	wp_register_style('king-menu-demo', king_child_theme_enqueue( $css_dir.'main_menu/menu-demo.css' ), false, KING_VERSION );

	if( $king->isPluginActive( 'whmpress/whmpress.php' ) ){
		wp_enqueue_style('arkahost_whmpress_css_file', king_child_theme_enqueue( $css_dir.'whmpress.css' ), false, KING_VERSION );
	}


	wp_register_script('king-bootstrap-slider', king_child_theme_enqueue( $js_dir.'bootstrap-slider.min.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_register_script('king-owl-carousel', king_child_theme_enqueue( $js_dir.'owl.carousel.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-owl-carousel');
	wp_register_script('king-modal', king_child_theme_enqueue( $js_dir.'modal.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-modal');
	wp_register_script('king-custom', king_child_theme_enqueue( $js_dir.'custom.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-custom');
	wp_register_script('king-user', king_child_theme_enqueue( $js_dir.'king.user.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-user');

	if( !empty( $king->cfg['smoother_scroll'] ) ){
		if( $king->cfg['smoother_scroll'] == 'enable' ){
			wp_register_script('king-smoothscroll', king_child_theme_enqueue( $js_dir.'smoothscroll.js' ), array( 'jquery' ), KING_VERSION, true );
			wp_enqueue_script('king-smoothscroll');
		}
	}

	wp_register_script('king-viewportchecker', king_child_theme_enqueue( $js_dir.'viewportchecker.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-viewportchecker');

	wp_register_script('king-cubeportfolio', king_child_theme_enqueue( $js_dir.'cube/jquery.cubeportfolio.min.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-cubeportfolio');

	wp_register_script('king-cubeportfolio-main', king_child_theme_enqueue( $js_dir.'cube/main.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-cubeportfolio-main');

	wp_register_script('king-loopslider', king_child_theme_enqueue( $js_dir.'jquery.loopslider.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_register_script('king-universal-custom', king_child_theme_enqueue( $js_dir.'universal/custom.js' ), array( 'jquery' ), KING_VERSION, true );
	wp_enqueue_script('king-universal-custom');

	if ( is_singular() ){
			wp_enqueue_script( "comment-reply" );
	}

   /* Register google fonts */
   $protocol = is_ssl() ? 'https' : 'http';

   wp_enqueue_style( 'king-google-fonts', "$protocol:".king_google_fonts_url() );

	ob_start();
		$header = $king->path( 'header' );
		if( $header == true ){
			$king->path['header'] = ob_get_contents();
		}
	ob_end_clean();

}

function king_enqueue_content_last(){

	global $king;
	$css_dir = THEME_URI.'/assets/css/';

	wp_enqueue_style('king-responsive', $css_dir.'responsive.css', false, KING_VERSION );
	wp_enqueue_style('king-responsive-tabs', $css_dir.'responsive-tabs.css', false, KING_VERSION );
	wp_enqueue_style('king-responsive-portfolio', $css_dir.'responsive-portfolio.css', false, KING_VERSION );

	echo "<script type=\"text/javascript\">if(!document.getElementById('rs-plugin-settings-inline-css')){document.write(\"<style id='rs-plugin-settings-inline-css' type='text/css'></style>\")}</script>";

}


function king_enqueue_admin() {

	global $king;

	$css_dir = THEME_URI.'/assets/css/';
	$js_dir = THEME_URI.'/assets/js/';

	wp_enqueue_style('king-admin', THEME_URI.'/core/assets/css/king-admin.css', false, time() );
	wp_enqueue_style('king-awesome', king_child_theme_enqueue( THEME_URI.'/core/assets/css/font-awesome.min.css'), false, KING_VERSION );

	if( $king->page == strtolower( THEME_NAME ).'-importer' ){
		add_thickbox();
	}
	if( $king->page == 'page' || $king->page == 'mega_menu' ){
		wp_enqueue_style('king-simple-line-icons.', THEME_URI.'/core/assets/css/simple-line-icons.css', false, time() );
		wp_enqueue_style('king-etlinefont-icons.', THEME_URI.'/core/assets/css/etlinefont.css', false, time() );
		wp_register_script('king-admin', THEME_URI.'/core/assets/js/king-admin.js', false, KING_VERSION, true );
		wp_register_script('king-bs64', THEME_URI.'/core/assets/js/base'.'64.js', false, KING_VERSION, true );
		wp_enqueue_style('king-owl-transitions', $css_dir.'owl.transitions.css', false, KING_VERSION );
		wp_enqueue_style('king-owl-carousel', $css_dir.'owl.carousel.css', false, KING_VERSION );
		wp_register_script('king-owl-carousel', king_child_theme_enqueue( $js_dir.'owl.carousel.js' ), array( 'jquery' ), KING_VERSION, true );
		wp_enqueue_script('king-owl-carousel');
		wp_enqueue_script('king-admin');
		wp_enqueue_script('king-bs64');
	}

}

function king_admin_head() {

	global $king;

	echo '<script type="text/javascript">var site_uri = "'.SITE_URI.'";var SITE_URI = "'.SITE_URI.'";var HOME_URL = "'.HOME_URL.'";var theme_uri = "'.THEME_URI.'";var theme_name = "'.THEME_NAME.'";</script>';

	echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#sc_select").change(function() {send_to_editor(jQuery("#sc_select :selected").val());return false;});});</script><style type="text/css">.vc_license-activation-notice,.ls-plugins-screen-notice,.rs-update-notice-wrap{display: none;}</style>';

}

function king_google_fonts_url() {

    $font_url = '';

    /*
    Translators: If there are characters in your language that are not supported
    by chosen font(s), translate this to 'off'. Do not translate into your own language.
     */
    if ( 'off' !== _x( 'on', 'Google font: on or off', 'arkahost' ) ) {
        $font_url = '//fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic|Raleway:400,100,200,300,500,600,700,800,900|Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic|Dancing+Script:400,700';
    }

    return $font_url;

}


