<?php
	/**
	*
	* @author king-theme.com
	*
	*/
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
get_header();
	
?>
<div class="clearfix margin_top6"></div>
<div class="clearfix"></div>

<div class="page_title2 sty5">
	<div class="container">
		<h1><?php _e( 'Our History', 'arkahost' ); ?></h1>
	    <span class="line"></span>
	    <h5>
	    	<?php _e( 'Therefore discovered the undoubtable source comes from the cites of the making words with sections always.', 'arkahost' ); ?>
	    </h5>
	</div>
</div>
<div class="clearfix"></div>
<div class="content_fullwidth less featured_section121 blog-timeline">
	<div class="features_sec65">
		<div class="container no-touch">
			<div id="cd-timeline" class="cd-container">
				<?php king_ajax_loadPostsTimeline(); ?>
			</div>
		</div>
	</div>
</div>

<div class="clearfix margin_top10"></div>
<div class="clearfix"></div>
<?php get_footer(); ?>   