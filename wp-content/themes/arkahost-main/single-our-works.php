<?php
/**
 * (c) king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $king;

$image = $king->get_featured_image( $post );
$link =  "//".$king->ext['sv']('HTTP_HOST').$king->ext['sv']('REQUEST_URI');
$escaped_link = get_permalink($post->ID);
$project_meta = get_post_meta( $post->ID, 'king_work', true );
get_header();

?>


<?php $king->breadcrumb(); ?>

<div id="primary" class="site-content container-content content ">
	<div id="content" class="row row-content container">
		<div class="lessmar">
			<div class="portfolio_area">
				<div class="portfolio_area_left">
					<div class="animated fadeInLeft">
						<div id="portfolio-large-preview">
							<img src="<?php echo esc_url( $image ); ?>" alt="ArkaHost" />
						</div>	
					</div>
					<?php

						preg_match_all('/(?<=src=")[^"]+(?=")/', $post->post_content, $srcs, PREG_PATTERN_ORDER);
						
						if( !empty( $srcs ) ){
							if( !empty( $srcs[0] ) ){
								$srcs = $srcs[0];
								echo '<div class="portfolio_thumbnails">';
								foreach( $srcs as $src ){
									
									$ex = explode( '-', $src );
									if( count( $ex ) > 1 ){
										if(strpos($ex[count( $ex )-1],'x')!==false&&strpos($ex[count($ex)-1],'.')!==false){
											$ex[count( $ex )-1] = substr( $ex[count( $ex )-1], strpos($ex[count($ex)-1],'.') );
										}
										$src = str_replace( '-.', '.', implode( '-', $ex ) );
									}
									
									echo '<a href="'.esc_url($src).'" target=_blank>loading</a>';
								}
								echo '</div>';
							}
						}
						
					?>	
				</div>
				<div class="portfolio_area_right animated eff-fadeInRight delay-200ms">
					<h4>
						<?php echo esc_html($post->post_title); ?>
					</h4>
					<div class="work-des">
						<?php echo apply_filters('the_content',$post->post_content ); ?></div>
					<a href="javascript:void(0)" onclick="jQuery('.work-des').css({'max-height':'none'});jQuery(this).remove();" class="addto_favorites">
						<i class="fa fa-chevron-down"></i>
						<?php _e('Show More', 'arkahost' ); ?>
					</a>
					<ul class="small_social_links">
						<li>
							<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u='.$escaped_link ); ?>">
								<i class="fab fa-facebook-f">
								</i>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( 'https://twitter.com/home?status='.$escaped_link ); ?>">
								<i class="fab fa-twitter">
								</i>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( 'https://plus.google.com/share?url='.$escaped_link ); ?>">
								<i class="fab fa-google-plus-g">
								</i>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( 'https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source='.$escaped_link ); ?>">
								<i class="fab fa-linkedin-in">
								</i>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( 'https://pinterest.com/pin/create/button/?url=&media=&description='.$escaped_link ); ?>">
								<i class="fab fa-pinterest">
								</i>
							</a>
						</li>
					</ul>
					<div class="project_details animated eff-fadeInUp delay-500ms">
						<h5>
							<?php _e('Project Details', 'arkahost' ); ?>
						</h5>
						<span>
							<strong>
								<?php _e('Name', 'arkahost' ); ?>
							</strong>
							<em>
								<?php the_title(); ?></em>
						</span>
						<span>
							<strong>
								<?php _e('Date', 'arkahost' ); ?>
							</strong>
							<em>
								<?php echo get_the_time('m D Y',$post); ?></em>
						</span>
						<?php
						if(!isset($king->cfg['our_works_show_category']) || $king->cfg['our_works_show_category'] ==1){
						?>
						<span>
							<strong>
								<?php _e('Categories', 'arkahost' ); ?>
							</strong>
							<em>
								<?php
									$terms = wp_get_post_terms($post->ID, 'our-works-category', array("fields" => "all"));
									if( !empty( $terms ) ){
										foreach( $terms as $term ){
											echo '<a href="'.esc_url( get_term_link( $term ) ).'">'.esc_html( $term->name ).'</a>';
										}
									}
								?>
							</em>
						</span>
						<?php }?>
						<span>
							<strong>
								<?php _e('Author', 'arkahost' ); ?>
							</strong>
							<em>
								<?php echo the_author_meta( 'display_name' , $post->post_author); ?>
							</em>
						</span>
						<div class="clearfix margin_top5">
						</div>
						<?php
						if(!isset($king->cfg['our_works_visit_link']) || $king->cfg['our_works_visit_link'] ==1){

							$target = '';
							//check the external => target open new tab
							if( is_url_external( $project_meta['link'] ) ){
								$target = ' target="_blank"';
							}
						?>
						<a href="<?php echo esc_url( $project_meta['link'] ); ?>" class="but_goback globalBgColor"<?php echo esc_attr( $target ); ?>>
							<i class="fa fa-hand-o-right fa-lg">
							</i>
							<?php _e('Visit Site', 'arkahost' ); ?>
						</a>
						<?php }?>
					</div>


				</div>
			</div>
			<div class="portfolio_pagination">
				<?php 
				previous_post_link('%link', '%title', true, '', 'our-works-category'); ?>
				<?php 
				//if the config of main portfolio page set on the theme panel
				if( isset( $king->cfg[ 'our_works_main_page' ] ) && !empty( $king->cfg[ 'our_works_main_page' ] ) )
				{
					$our_works_mainpage = get_post( $king->cfg[ 'our_works_main_page' ] );
					$page_title = $our_works_mainpage->post_title;
				?>
				<a href="<?php echo get_permalink( $our_works_mainpage );?>" rel="main-page-our-works" class="main-page-our-works" title="<?php echo esc_attr( $page_title );?>">
	                <i class="icon-grid"></i>
	            </a>
				<?php
				}else{
					$main_link =  get_post_type_archive_link( 'our-works' );
					$our_works_title = (isset($king->cfg['our_works_title']) && !empty($king->cfg['our_works_title']))?$king->cfg['our_works_title']:__('Our Works', 'arkahost' );
					?>
				<a href="<?php echo esc_url( $main_link );?>" rel="main-page-our-works" class="main-page-our-works" title="<?php echo esc_attr( $our_works_title );?>">
	                <i class="icon-grid"></i>
	            </a>
				<?php
				}
				?>				
				<?php next_post_link( '%link', '%title', true, '', 'our-works-category'); ?>
			</div>
			<!-- end section -->
		</div>
		<div class="clearfix margin_top5"></div>
	</div>
</div>	
<script type="text/javascript">
(function($){
	$(window).load(function() {
		$('.portfolio_thumbnails a').each(function(){
			var obj = this;
			var img = new Image();
			img.onload = function(){
				$(obj).html('').append( this ).on( 'click', function(e){
					var new_src = $(this).attr('href');
					$('#portfolio-large-preview img').animate({'opacity':0.1},150,function(){
						$('#portfolio-large-preview img').attr({ 'src' : new_src }).css({ 'opacity' : 0 }).animate({ 'opacity' : 1 });
					});
					e.preventDefault();
				});
			}
			img.src = $(this).attr('href');
		});
	});
})(jQuery);	
</script>
<?php get_footer(); ?>	