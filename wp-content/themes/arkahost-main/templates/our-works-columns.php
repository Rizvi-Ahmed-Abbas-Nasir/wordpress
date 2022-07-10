<?php
	
global $posts, $king;
$column = $king->cfg[ 'our_works_listing_layout' ];
?>
<div class="cbp-panel">
	<div id="grid-container" class="cbp" data-cols="<?php echo esc_attr( $column ); ?>"c>
		<?php
					
			$i = 0;
			while ( have_posts() ) : the_post();
				global $post;
				$i++;

				$image = $king->get_featured_image( $post );
				$des   = '<strong>'. get_the_title() .'</strong><br />'. get_the_excerpt();

				if(!isset($king->cfg['our_works_show_link']) || $king->cfg['our_works_show_link'] ==1){
					$des .=' <a href="'. get_permalink( $post->ID ) .'">'.__( 'Read More', 'arkahost' ).' &raquo;</a>';
				}
				$image_thumb = king_createLinkImage( $king->get_featured_image( $post ), '680x600xc');
				
				
		?>
	    <div class="cbp-item ">
	        <a href="<?php echo esc_url($image); ?>" class="cbp-caption cbp-lightbox" data-title="<?php echo esc_attr( $des ); ?>">
	            <div class="cbp-caption-defaultWrap">
	                <img src="<?php echo esc_url($image_thumb); ?>" alt="ArkaHost">
	            </div>
	            <div class="cbp-caption-activeWrap">
	                <div class="cbp-l-caption-alignLeft">
	                    <div class="cbp-l-caption-body">
	                        <div class="cbp-l-caption-title"><?php the_title(); ?></div>
	                        <div class="cbp-l-caption-desc"><?php the_excerpt(); ?></div>
	                    </div>
	                </div>
	            </div>
	        </a>
	    </div>
	    <?php
			endwhile;	
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