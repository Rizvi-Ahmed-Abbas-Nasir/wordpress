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
		<div id="content" class="row row-content container">
			<div class="col-md-9">
			
			<?php			

				while ( have_posts() ) : the_post(); 
					
					get_template_part( 'content' ); 
					
					if( $king->cfg['showShareBox'] == 1 ){
					
					$link =  "//".$king->ext['sv']('HTTP_HOST').$king->ext['sv']('REQUEST_URI');
					$escaped_link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
					
					?>
					
					<div class="sharepost">
					    <h4><?php _e('Share this Post','arkahost'); ?></h4>
					    <ul>
					    <?php if( $king->cfg['showShareFacebook'] == 1 ){ ?>
					      <li>
					      	<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( $escaped_link ); ?>">
					      		&nbsp;<i class="fab fa-facebook-f fa-lg"></i>&nbsp;
					      	</a>
					      </li>
					      <?php } ?>
					      <?php if( $king->cfg['showShareTwitter'] == 1 ){ ?>
					      <li>
					      	<a href="https://twitter.com/home?status=<?php echo esc_url( $escaped_link ); ?>">
					      		<i class="fab fa-twitter fa-lg"></i>
					      	</a>
					      </li>
					      <?php } ?>
					      <?php if( $king->cfg['showShareGoogle'] == 1 ){ ?>
					      <li>
					      	<a href="https://plus.google.com/share?url=<?php echo esc_url( $escaped_link ); ?>">
					      		<i class="fab fa-google-plus-g fa-lg"></i>	
					      	</a>
					      </li>
					      <?php } ?>
					      <?php if( $king->cfg['showShareLinkedin'] == 1 ){ ?>
					      <li>
					      	<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=&amp;title=&amp;summary=&amp;source=<?php echo esc_url( $escaped_link ); ?>">
					      		<i class="fab fa-linkedin-in fa-lg"></i>
					      	</a>
					      </li>
					      <?php } ?>
					      <?php if( $king->cfg['showSharePinterest'] == 1 ){ ?>
					      <li>
					      	<a href="https://pinterest.com/pin/create/button/?url=&amp;media=&amp;description=<?php echo esc_url( $escaped_link ); ?>">
					      		<i class="fab fa-pinterest fa-lg"></i>
					      	</a>
					      </li>
					      <?php } ?>
					    </ul>
					</div>
					
					
					<?php
					
					}
			 
				endwhile;
				
				 // end of the loop. ?>
			</div>
			<div class="col-md-3">
				<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
					<div id="sidebar" class="widget-area king-sidebar">
						<?php dynamic_sidebar( 'sidebar' ); ?>
					</div><!-- #secondary -->
				<?php endif; ?>
			</div>
		</div>
	</div>
				
<?php get_footer(); ?>	