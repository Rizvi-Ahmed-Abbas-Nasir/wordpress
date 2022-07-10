<?php
	
global $posts, $king;

$atts = $king->bag['atts'];
$class = $king->bag['class'];
$items = $king->bag['items'];
$tax_term = $king->bag['tax_term'];
$filter = $atts['filter'];
$column = $atts['column'];
$margin = $atts['margin'];

if ( $posts->have_posts() ) {	

	global $king;
?>
<div class="cbp-panel <?php echo esc_attr( $class ); ?>">
	<?php if($filter=='Yes'){ ?>
	    <div id="filters-container" class="cbp-l-filters-button">
	        <div data-filter="*" class="cbp-filter-item-active cbp-filter-item">
	            All <div class="cbp-filter-counter"></div>
	        </div>
	    </div>
	    <div class="clearfix margin_bottom3"></div>
	<?php } ?>

	<div id="grid-container" class="cbp" data-cols="<?php echo esc_attr( $column ); ?>"<?php if( $margin=='No' ){echo ' data-gap="0" data-gaph="0"';} ?> >
		<?php
					
			$i = 0;
			$catsStack = array();
			while ( $posts->have_posts() ) :
	
				$posts->the_post();
				global $post;
				$i++;
				$image = $king->get_featured_image( $post );
				$des = '<strong>'.get_the_title().'</strong><br />'.get_the_excerpt();
				if(!isset($king->cfg['our_works_show_link']) || $king->cfg['our_works_show_link'] ==1){
					$des .=' <a href="'.get_permalink( $post->ID ).'">'.__( 'Read More', 'arkahost' ).' &raquo;</a>';
				}
				
				$cats = wp_get_post_terms( $post->ID, 'our-works-category' );
				$cateClass = '';
				
				if( count( $cats ) ){
					foreach( $cats as $cat ){
						$cat_name = strtolower( str_replace(array(' ','&amp;','&'),array('-','',''),$cat->name) );
						$cat_args = array( $cat_name, $cat->count );
						if( is_array($tax_term) && !in_array($cat->slug, $tax_term))
							continue;
						if( !in_array( $cat_args, $catsStack ) ){
							array_push( $catsStack , $cat_args );
						}
						$cateClass .= $cat_name.' ';
					}	
				}
				
		?>
	    <div class="cbp-item <?php echo esc_attr($cateClass); ?>">
	        <a href="<?php echo esc_url($image); ?>" class="cbp-caption cbp-lightbox" data-title="<?php echo esc_attr( $des ); ?>">
	            <div class="cbp-caption-defaultWrap">
	                <img src="<?php echo esc_url($image); ?>" alt="ArkaHost">
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

<script type="text/javascript">
	(function($){
	<?php
		if($filter=='Yes'){
			foreach( $catsStack as $cat ){
				echo '$(\'#filters-container\').append(\'<div data-filter=".'.strtolower($cat[0]).'" class="cbp-filter-item">'.esc_html(ucwords(str_replace('-',' ',$cat[0]))).'<div class="cbp-filter-counter"></div></div>\');';
			}
		}	
	?>
	})(jQuery);
</script>
<?php
		
}else {
	echo '<h4>' . __( 'Works not found', 'arkahost' ) . '</h4> <a href="'.admin_url('post-new.php?post_type=our-works').'"><i class="fa fa-plus"></i> Add New Work</a>';
}	
	
?>