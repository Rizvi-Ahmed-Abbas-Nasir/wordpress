<?php
/**
 * (c) king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $king;

get_header();

?>

	<?php $king->breadcrumb(); ?>

	<div id="primary" class="site-content container-content content ">
		<div id="content" class="row row-content container blog-2-columns">
			<div class="col-md-12">
				<?php
					
					$i = 0;
					while ( have_posts() ) : the_post();
					
						$i++;
						
						echo '<div class="content_halfsite';
						if( $i%2 == 0 )echo ' last';
						echo '">';
						
							get_template_part( 'content', get_post_format() );
						
						echo '</div>';
						
					endwhile;
					
				?>
				<?php $king->pagination(); ?>
			</div>
		</div>
	</div>
				
<?php get_footer(); ?>		



		