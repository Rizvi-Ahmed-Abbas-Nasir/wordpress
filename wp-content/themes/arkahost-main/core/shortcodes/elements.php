<?php

class king_elements {

	public static function icon( $atts ){

		$image = $atts['image'];
		if((int)$image > 0 && ($image_url = wp_get_attachment_url( $image, 'thumbnail' )) !== false) {
			$image_data = wp_get_attachment_metadata( $image );
	    }else{
		    $image_url = THEME_URI.'/assets/images/default.png';
	    }
	    if( $image_url != THEME_URI.'/assets/images/default.png' ){
    		echo '<img ';
    		if( isset( $atts['retina'] ) ){
	    		if( $atts['retina'] == 'yes' ){
		    		echo ' width="'.($image_data['width']/2).'" ';
	    		}
    		}
			$alt ='';
			$alt_arr = get_post_meta($image, '_wp_attachment_image_alt', true);
			if(is_array($alt_arr) && count($alt_arr))
			{
				$alt = $alt_arr;
			}
    		echo ' src="'.esc_url( $image_url ).'" class="element-icon '.esc_attr( $atts['icon_class'] ).'" alt="' . $alt .'"/>';
		}else if( strpos( $atts['icon_awesome'], 'empty' ) === false ){
    		echo '<i class="'.esc_attr( $atts['icon_awesome'] ).' element-icon '.esc_attr( $atts['icon_class'] ).'"></i>';
		}else if( strpos( $atts['icon_simple_line'], 'empty' ) === false ){
    		echo '<i aria-hidden="true" class="icon-'.esc_attr( $atts['icon_simple_line'] ).' element-icon '.esc_attr( $atts['icon_class'] ).'"></i>';
		}else if( strpos( $atts['icon_etline'], 'empty' ) === false ){
    		echo '<i aria-hidden="true" class="et-'.esc_attr( $atts['icon_etline'] ).' element-icon '.esc_attr( $atts['icon_class'] ).'"></i>';
		}
	}

	public static function display( $atts ){
		
		global $king;
		
	?>

		<div class="king-elements <?php echo esc_attr( $atts['class'] ); ?>">
			<div class="king-elements-inner">
        	<?php

        		self::icon( $atts );

				$readmore_text = $atts['readmore_text'];
				if(empty($readmore_text)){
					$readmore_text = __( 'Read More', 'arkahost' );
				}
	        	if( !empty( $atts['title'] ) ){
	        		if( strpos( $atts['title'] , '<h' ) === false ){
	        			echo '<h4>'.esc_html($atts['title']).'</h4>';
	        		}else{
		        		$king->ext['pr']( $atts['title'] );
	        		}
				}

				if( !empty( $atts['des'] ) ){
					if( strpos( $atts['des'] , '<' ) === false ){
	        			$king->ext['pr']( '<p>'.$atts['des'].'</p>' );
	        		}else{
		        		$king->ext['pr']( $atts['des'] );
	        		}
				}
				if( !empty( $atts['link']) && (!isset($atts['hidden_readmore']) || (isset($atts['hidden_readmore']) && empty($atts['hidden_readmore'])) ) ){
					echo '<a href="'.esc_url($atts['link']).'" class="'.esc_attr($atts['linkclass']).'"><i class="fa fa-caret-right"></i> '.king::esc_js($readmore_text).'</a>';
				}
			?>
			</div>
        </div>

	<?php
	}

	public static function sec2( $atts ){
		
		global $king;
		
		$image = $atts['image'];
		if((int)$image > 0 && ($image_url = wp_get_attachment_url( $image, 'thumbnail' )) !== false) {

	    }else{
		    $image_url = THEME_URI.'/assets/images/default.png';
	    }

	?>
		<div class="box">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="ArkaHost">
			<h5>
				<?php
					if( $atts['link'] != '' )echo '<a href="'.esc_url( $atts['link'] ).'">';
					echo esc_html($atts['title']);
					if( $atts['link'] != '' )echo '</a>';
				?>
			</h5>
			<p>
				<?php $king->ext['pr']( $atts['des'] ); ?>
			</p>
		</div>

	<?php
	}

	public static function flex_sliders( $content, $class ) {

		$rgex = '\[(\[?)(vc_tab)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		$content = preg_replace( '/'.$rgex.'/s', '<li>$5</li>', $content );
	?>
		<div class="slider <?php echo esc_attr($class); ?>">
			<div class="flexslider carousel">
				<ul class="slides">
					<?php echo do_shortcode( $content ); ?>
				</ul>
			</div>
		</div>

	<?php

	}

	public static function ipad_sliders( $content, $class ) {

		$rgex = '\[(\[?)(vc_tab)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		$content = preg_replace( '/'.$rgex.'/s', '<div class="item">$5</div>', $content );
	?>
	 <div class="ms-phone-template">
		<div class="ms-phone-cont">
			<img src="<?php echo THEME_URI; ?>/assets/images/phone.png" class="ms-phone-bg" />
			<div class="owl-demo8 ms-phone-slider-cont">
				<div class="owl-carousel">
				    <?php echo do_shortcode( $content ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php

	}

	public static function owl_carousel( $content, $class ) {

		$rgex = '\[(\[?)(vc_tab)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		$content = preg_replace( '/'.$rgex.'/s', '<div class="item">$5</div>', $content );

		if( strpos( $class, 'owl-demo' ) === false ){
			$class .= ' owl-demo16';
		}

	?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<div class="owl-carousel">
			    <?php echo do_shortcode( $content ); ?>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				if( !document.owl_demo16 ){
					$(".owl-demo16>.owl-carousel").owlCarousel({
						autoPlay : 12000,
						stopOnHover : true,
						lazyLoad : true,
						pagination:true,
						singleItem : true,
					});
					document.owl_demo16 = true;
				}
				if( !document.owl_demo22 ){
					$(".owl-demo22>.owl-carousel").owlCarousel({
						autoPlay : 9000,
						stopOnHover : true,
						navigation: true,
						paginationSpeed : 1000,
						goToFirstSpeed : 2000,
						singleItem : true,
						autoHeight : true,
						pagination:false,
					});
					document.owl_demo22 = true;
				}
			});
		</script>
	<?php
	}


	public static function outline_slider( $content, $class ) {

	 	wp_enqueue_script('king-loopslider');

		$rgex = '\[(\[?)(vc_tab)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		$content = preg_replace( '/'.$rgex.'/s', '<div class="sl-div">$5</div>', $content );

	?>

		<div id="slider-outline" class="<?php echo esc_attr( $class ); ?>">
		    <div id="slider" class="outer_slider">
		        <div id="sl-view" class="clearfix">
		            <div id="sl-wrap">
		                <?php echo do_shortcode( $content ); ?>
					</div>
		        </div>
		        <div id="sl-next"><span>&raquo;</span></div>
		        <div id="sl-prev"><span>&laquo;</span></div>
		    </div>
		</div>

	<?php
	}

	public static function icon_tabs( $content, $class ) {
		global $king;
		$rgex = '\[(\[?)(vc_tab)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		$navs = explode( '{-x-}', preg_replace( '/'.$rgex.'/s', '$3{-x-}', $content ) );
		$nav = '';
		foreach( $navs as $prx ){
			if( $prx == '' ){
				break;
			}
			preg_match_all('/(\w+)\s*=\s*"(.*?)"/', $prx, $matches);
			$atts = array( 'title' => '', 'icon' => 'leaf' );
			if( count( $matches[1] ) ){
				foreach( $matches[1] as $k => $v ){
					if( isset( $matches[2] ) ){
						if( isset( $matches[2][$k] ) ){
							$atts[ $v ] = $matches[2][$k];
						}
					}
				}
			}
			$nav .= '<div class="item"><i title="'.esc_attr($atts['title']).'" class="'.esc_attr($atts['icon']).'"></i></div>';
		}
		$content = preg_replace( '/'.$rgex.'/s', '<div class="item">$5</div>', $content );

		if(empty($class))$class = '';
	?>
		<div id="sync4" class="owl-carousel <?php echo esc_attr( $class ); ?>">
		    <?php $king->ext['pr']( $nav ); ?>
		</div>
		<div class="clearfix margin_bottom5"></div>
		<div id="sync3" class="owl-carousel">
			<?php echo do_shortcode( $content ); ?>
		</div>
	<?php
	}


}
