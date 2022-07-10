<?php
/**
 * (c) www.king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $more, $king;

get_header();

?>

<div id="primary" class="site-content">
	<div id="content" class="container">
		<div class="entry-content blog_postcontent">
			<div class="margin_top1"></div>
			<?php get_template_part( 'templates/login' ); ?>
			<div class="margin_top6"></div>
		</div>
	</div>
</div>



<?php get_footer(); ?>   