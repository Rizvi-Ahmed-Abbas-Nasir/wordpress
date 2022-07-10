<?php
/**
 * (c) king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $king;

get_header();

$sidebar = false;

if( isset( $king->cfg['display_single_sidebar'] ) && $king->cfg['display_single_sidebar'] != '0' ){
	$sidebar = true;
}

?>

	<?php $king->breadcrumb(); ?>

	<div id="primary" class="site-content container-content content ">
		<div id="content" class="row row-content container">
			<div class="<?php $king->ext['pr'] ( $sidebar )? "col-md-9" : "col-md-12";?>">
			
			<?php			

				while ( have_posts() ) : the_post(); 
					
					get_template_part( 'content' ); 
					
					if( $king->cfg['showShareBox'] == 1 ){
					
					$link =  "//".$king->ext['sv']('HTTP_HOST').$king->ext['sv']('REQUEST_URI');
					$escaped_link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
					$post_link = get_permalink();
					?>
					
					<div class="sharepost">
					    <h5 class="caps"><?php _e('Share this Article','arkahost'); ?></h5>
					    <ul>
					    <?php if( $king->cfg['showShareFacebook'] == 1 ){ ?>
					      <li>
					      	<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( $post_link ); ?>">
					      		&nbsp;<i class="fab fa-facebook-f fa-lg"></i>&nbsp;
					      	</a>
					      </li>
					      <?php } ?>
					      <?php if( $king->cfg['showShareTwitter'] == 1 ){ ?>
					      <li>
					      	<a href="https://twitter.com/intent/tweet?text=<?php echo get_the_title();?> <?php echo esc_url( get_permalink() ); ?>">
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
					      	<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=&amp;title=&amp;summary=&amp;url=<?php echo esc_url( $post_link ); ?>">
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
					
					if( $king->cfg['navArticle'] == 1 ):
					
					?>
						<nav id="nav-single">
							<span class="nav-previous"><?php previous_post_link( '%link', wp_kses( __( '<span class="meta-nav">&larr;</span> Previous Article', 'arkahost' ), array('span'=>array())) ); ?></span>
							<span class="nav-next"><?php next_post_link( '%link', wp_kses( __( 'Next Article<span class="meta-nav">&rarr;</span>', 'arkahost' ), array('span'=>array())) ); ?></span>
						</nav><!-- #nav-single -->
					<?php 
					
					endif;
					
					
					if( $king->cfg['archiveAboutAuthor'] == 1 ){
					?>
					
					<!--About author-->
					<div class="clearfix"></div>
					<h5 class="caps"><?php _e('About the Author', 'arkahost');?></h5>
					<div class="about_author">
				        <?php echo get_avatar(get_the_author_meta( 'ID' )); ?>
				        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" target="_blank">
				        	<?php echo get_the_author(); ?>
				        </a>
				        <br>
						<?php the_author_meta( 'description' ); ?>
				    </div>
				
					<?php
					}
					
					
					
					if( $king->cfg['archiveRelatedPosts'] == 1 ){
						get_template_part( 'post-related' ); 
					}
				
					if( is_single() ){
						if ( comments_open() ) {
							comments_template( '', true );
						}
					}
					 
				endwhile; // end of the loop. ?>
			</div>
			<?php 
			if ( $sidebar ):
			?>
			<div class="col-md-3">
				<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
					<div id="sidebar" class="widget-area king-sidebar">
						<?php dynamic_sidebar( 'sidebar' ); ?>
					</div><!-- #secondary -->
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
				
<?php get_footer(); ?>	