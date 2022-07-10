<?php

#	(c) king-theme.com




	global $wpdb,$king;
	
	$file = THEME_PATH.DS.'core'.DS.'sample'.DS.'data'.DS.'widgets.export.txt';

	if (file_exists($file)) {
		
		if( !get_option( KING_OPTNAME ) ){
		
			$handle = $king->ext['fo']( $file, 'r' );
			$export = $king->ext['fr']( $handle, 800000 );
			$impt = '';
			if( !empty( $_REQUEST['king'] ) ){
				$impt = $_REQUEST['king'];
			}
			
			$imports = json_decode( $export, true );

			if( is_array($imports) ){
				
				foreach( $imports as $key => $import ){
					
					if( $key == KING_OPTNAME ){
	
						if( $key == KING_OPTNAME ){
							$val2upd = json_decode( str_replace( '%THEME_URI%', THEME_URI, $import ), true );
						}
						
						if( add_option( $key, $val2upd ) != 1 && $impt == 'import' ){
							update_option( $key, $val2upd );
						}	
						continue;
					}else{
						$val2upd = json_decode( $king->ext['bd']( $import ), true );
                        if( get_option( $key ) !== false )
                            update_option( $key, $val2upd );
                        else add_option( $key, $val2upd, null, 'no' );
					}
						
				}	
			}
		}
		/*Reset homepage -> index_php*/
		update_option( 'show_on_front', 'posts' );
 
		/* We need to reset primary menu */
		$thememods = 'theme_mods_'.get_option('stylesheet', true);
		$mod = get_option( $thememods );
		$menuID = $wpdb->get_results('SELECT `term_id` FROM `'.$wpdb->prefix.'terms` WHERE `'.$wpdb->prefix.'terms`.`slug` = "main-menu"');
		$menuOnepageID = $wpdb->get_results('SELECT `term_id` FROM `'.$wpdb->prefix.'terms` WHERE `'.$wpdb->prefix.'terms`.`slug` = "menu-onepage"');
		$oid = 0;
		if( isset( $menuOnepageID[0] ) ){
			$oid = $menuOnepageID[0]->term_id;
		}
		if( isset( $menuID[0] ) ){
			if( !isset( $mod ) ){
				$mod = array( 'nav_menu_locations' => array( 'primary' => $menuID[0]->term_id, 'onepage' => $oid )  );
			}else{
				$mod['nav_menu_locations']['primary'] = $menuID[0]->term_id;
				$mod['nav_menu_locations']['onepage'] = $oid;
			}
			add_option( $thememods , $mod ) || update_option( $thememods , $mod );
		}
		$checkPage = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = 'home-version-1' AND post_type = 'page' AND post_status = 'publish'");
		if( isset( $checkPage ) ){
			add_option( 'show_on_front', 'page' ) || update_option( 'show_on_front', 'page' );
			add_option( 'page_on_front' , $checkPage ) || update_option( 'page_on_front' , $checkPage );
		}
		$checkPage = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = 'blog' AND post_type = 'page' AND post_status = 'publish'");
		if( isset( $checkPage ) ){	
			add_option( 'page_for_posts' , $checkPage ) || update_option( 'page_for_posts' , $checkPage );
		}
		
		# Update option imported
		$opname = strtolower( THEME_NAME) .'_import';
		$import_val = 'imported';
		if( add_option( $opname, $import_val ) === false ){
			update_option( $opname, $import_val );
		}
		
		$wpdb->flush();
		
	}
	else
	{
		if(isset($_REQUEST['king'])){
			if($_REQUEST['king']=='import'){
				echo 'File not found: <i>'.THEME_SLUG.DS.'core'.DS.'sample'.DS.'data'.DS.'widgets.export.txt</i>';
				return;
			}
		}		
	}
	
		






