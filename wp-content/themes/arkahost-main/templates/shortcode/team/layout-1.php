<?php

global $posts, $king;

$atts = $king->bag['atts'];

$i = 1;
$eff = rand(0,10);
if( $eff <= 2 ){
	$eff = 'eff-fadeInUp';
}else if( $eff > 2 && $eff <=4 ){
	$eff = 'eff-fadeInRight';
}else if( $eff > 4 && $eff <=8 ){
	$eff = 'eff-fadeInLeft';
}else{
	$eff = 'eff-flipInY';
}
$words = $atts['words'];
if ( $posts->have_posts() ){
	echo '<div class="feature_section14_elm">';
	while ( $posts->have_posts() ) :
		$posts->the_post();
		global $post;
		
		$options = get_post_meta( $post->ID , 'king_staff' );
		$options = shortcode_atts( array(
			'position'	=> 'position',
			'facebook'	=> '',
			'twitter'	=> '',
			'gplus'	=> '',
		), $options[0], false );	
		
		$cls = 'one_half';
?>	
	<div class="<?php echo esc_attr($cls); ?> animated <?php echo esc_attr( $eff ); ?> delay-<?php echo esc_attr( $i );?>00ms<?php if( $i%2==0 )echo ' last' ?>">  
        	<?php 
				$thumbn = get_the_post_thumbnail_url($post);
				if (!empty($thumbn)) {
			?>
	        <img src="<?php echo esc_url($thumbn); ?>" alt="<?php the_title(); ?>" />
	        <?php } ?>
			<h5 class="sitecolor"><?php the_title(); ?> <em><?php echo esc_html( $options['position'] ); ?></em></h5>
			<p>
			<?php echo wp_trim_words( $post->post_content, $words ); ?>
			</p>
    </div>
<?php
	if( $i%2 == 0 && $atts['items'] > $i ){
		echo '<div class="clearfix margin_bottom3"></div>';
	}
	$i++;
	endwhile;
	echo "</div>";
}else {
	echo '<h4>' . __( 'Teams not found', 'arkahost' ) . '</h4> <a href="'.admin_url('post-new.php?post_type=our-team').'"><i class="far fa-plus"></i> Add New Staff</a>';
}
	
?>