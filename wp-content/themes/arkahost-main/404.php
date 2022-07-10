<?php
/**
 #		(c) king-theme.com
 */
get_header();
global $king;
?>

<?php $king->breadcrumb(); ?>

<div id="primary" class="site-content">
	<div id="content" class="container">

		<div class="error_pagenotfound">

	        <strong><?php _e('404', 'arkahost'); ?></strong>
	        <br>
	    	<b><?php _e('Oops... Page Not Found!', 'arkahost'); ?></b>

	        <em><?php _e('Sorry the Page Could not be Found here.', 'arkahost'); ?></em>

	        <p><?php _e('Try using the button below to go to main page of the site', 'arkahost'); ?></p>

	        <div class="clearfix margin_top3"></div>

	        <a href="<?php echo HOME_URL; ?>" class="but_medium1">
	        	<i class="fa fa-arrow-circle-left fa-lg"></i>&nbsp; <?php _e('Go to Back', 'arkahost'); ?>
	        </a>

	    </div>

	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>
