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
	
	echo '<div class="feature_section105_elm">';
	
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
		
		if( ($i-1) % 4 == 0 && $i > 4 ){
			echo '<div class="clearfix margin_top3"></div>';
		}
		
?>	
	<div class="one_fourth animated <?php echo esc_attr( $eff ); ?> delay-<?php echo esc_attr( $i );?>00ms<?php if( $i%4==0 )echo ' last' ?>">  
    	<div class="box">
        	<?php 
				$thumbn = get_the_post_thumbnail_url($post);
				if (!empty($thumbn)) {
			?>
	        <div class="box-cnt"><img src="<?php echo esc_url($thumbn); ?>" alt="<?php the_title(); ?>" /></div>
	        <?php } ?>
            <div class="box-details">
            	
            	<h5><?php the_title(); ?> <em><?php echo esc_html( $options['position'] ); ?></em></h5>
                <div class="hline"></div>
                <p><?php echo wp_trim_words( $post->post_content, $words ); ?></p>
                <a href="<?php the_permalink(); ?>"><?php _e( 'Read more', 'arkahost' ); ?></a>
                
            </div>
		</div>
    </div>
<?php
	
	$i++;
	
	endwhile;
	echo '</div>';
}else {
	echo '<h4>' . __( 'Teams not found', 'arkahost' ) . '</h4> <a href="'.admin_url('post-new.php?post_type=our-team').'"><i class="far fa-plus"></i> Add New Staff</a>';
}
	
?>