<?php

global $posts, $king;

$items = get_option('posts_per_page');
?>
<div class="masonry_section1">
	<div id="grid-container" class="cbp">
		<?php
			$_posts = $posts;
			
			if( $items < 10 ){
				while( count( $_posts ) < $items ){
					foreach( $_posts as $key => $val ){
						$_posts[ count($_posts) ] = $val;
					}
				}
			}
						
			$_class = 'four';
			$i      = 0;

			foreach( $_posts as $post ){
			
				if( $i < $items ){
						
					$i++;
					
					if( $i == 1 ){
						$_class = 'one';
					}else if( $i == 2 || $i == 3 ){
						$_class = 'three';
					}else{
						$_class = 'four';
					}
						
					$image = king_createLinkImage( $king->get_featured_image( $post ), '680x600xc');
						
				?>
			    <div class="cbp-item <?php echo esc_attr( $_class ); ?>">
			    	<div class="box">
			    	<img src="<?php echo esc_url($image); ?>" alt="ArkaHost">
			    	<div class="caption scale-caption">
		                <h3 class="white"><?php echo esc_html($post->post_title); ?></h3>
		                <p class="bigtfont gray">
		                	<?php 
		                		if( $i == 1 ){
			                		echo wp_trim_words( strip_tags( $post->post_content ), 75 );	
		                		}else{
			                		echo wp_trim_words( $post->post_excerpt, 10 );	
		                		}
		                	?>
		                	<span class="clearfix margin_bottom1"></span>
							<?php
								if(!isset($king->cfg['our_works_show_link']) || $king->cfg['our_works_show_link'] ==1){
							?>
							<a href="<?php echo get_permalink($post->ID); ?>" class="more">
								<?php _e( 'view details', 'arkahost' ); ?> 
								<i class="fa fa-caret-right"></i>
							</a>
							<?php }?>
		                </p>
		            </div>
			    	</div>
			    </div>
			 <?php
				
				if( $i == 3 ){
					echo '<p class="clearfix"></p>';
				}
				
	    		}
			}	
			?>
	</div>
	<div id="loadMore-container" class="cbp-l-loadMore-button hidden">
        <a href="#" class="cbp-l-loadMore-link">
            <span class="cbp-l-loadMore-defaultText">LOAD MORE</span>
            <span class="cbp-l-loadMore-loadingText">LOADING...</span>
            <span class="cbp-l-loadMore-noMoreLoading">NO MORE WORKS</span>
        </a>
    </div>
</div>	        
