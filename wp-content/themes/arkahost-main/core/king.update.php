<?php
/**
* (c) King-theme.com
*/

if( is_admin() ){

	class king_update{
		
		function __construct() {
			 add_action('init',  array($this, 'check' ), 88);
		}
		
		private function ver( $st = '' ){
			return intval( substr( str_replace( '.', '', $st ).'000', 0, 3 ) );
		}
		
		public function check(){
			
			$oname = strtolower( THEME_NAME) .'_curentversion';
			$verDB = self::ver( get_option( $oname, true ) );
			$verRL = self::ver( KING_VERSION );

			if( $verDB < $verRL ){
				
				/**
				*	 If current version from database lower than real version of the theme
				*/
				
				if( $verDB < 411 ){
					//self::_411();
				}

				
				self::update_plugins();
				
				if( add_option( $oname, $verRL ) === false ){
					update_option( $oname, $verRL );
				}
			}	
			
		}
		
		private function _411(){

		}
		
		private function update_plugins(){
			
			global $king, $wp_filesystem;
			
			$king->ext['rqo']( ABSPATH . 'wp-admin/includes/file.php' );
			
			$fields = array( 'action', '_wp_http_referer', '_wpnonce' );
			$canUnZip = false;
			if ( false !== ( $creds = request_filesystem_credentials( '', '', false, false, $fields ) ) ) {
			
				if ( ! WP_Filesystem( $creds ) ) {
				    request_filesystem_credentials( $url, $method, true, false, $fields );
				}else{
					$canUnZip = true;
				}
			}
	
			if( $canUnZip == true ){
			    
				$direct = ABSPATH.DS.'wp-content'.DS.'plugins';
				$path = THEME_PATH.DS.'core'.DS.'sample'.DS.'plugins'.DS;
				
				$plugins = array('arkahost-helper', 'revslider', 'contact-form-7', 'js_composer' );
				
				foreach( $plugins as $plugin ){
					$tmpfile = $path.$plugin.'.zip';
					if( !is_dir( $direct.DS.$plugin ) ){
						unzip_file( $tmpfile, $direct );
					}else{
						@rename( $direct.DS.$plugin, $direct.DS.$plugin.'_tmpl' );
						if( unzip_file( $tmpfile, $direct ) ){
							self::removeDir( $direct.DS.$plugin.'_tmpl' );
						}else{
							@rename( $direct.DS.$plugin.'_tmpl', $direct.DS.$plugin );
						}
					}
				
				}//end foreach
					
			}//end if canUnZip		
		}
		
		public function removeDir( $dir = '' ){

			if( $dir != '' && is_dir( $dir ) ){
				
				if ( $handle = opendir( $dir ) ){
					
					while ( false !== ( $entry = readdir($handle) ) ) {
						if( is_dir( $dir.DS.$entry ) && $entry != '..' && $entry != '.' ){
							king_update::removeDir( $dir.DS.$entry );
						}else{
							@unlink( $dir.DS.$entry );
						}
					}
				}
				@rmdir( $dir );
			}	
		}	
	}
	
	new king_update();
	
}
	
?>