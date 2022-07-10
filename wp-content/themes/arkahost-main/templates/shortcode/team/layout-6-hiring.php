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
?>
<div class="teams">
<?php
	while ( $posts->have_posts() ) :
		$posts->the_post();
		global $post;
		
		$options = get_post_meta( $post->ID , 'king_staff' );
		$options = shortcode_atts( array(
			'position'	=> 'position',
			'facebook'	=> '',
			'twitter'	=> '',
			'gplus'	=> '',
			'linkedin'	=> '',
		), $options[0], false );	
		
		$cls = 'one_fourth';
?>	
	<div class="<?php echo esc_attr($cls); ?> animated <?php echo esc_attr( $eff ); ?> delay-<?php echo esc_attr( $i );?>00ms<?php if( $i%4==0 )echo ' last' ?>">  
		<div class="flips1">
			<div class="flips1_front flipscont1">
            	<?php 
					$thumbn = get_the_post_thumbnail_url($post);
					if (!empty($thumbn)) {
				?>
		       <img src="<?php echo esc_url($thumbn); ?>" alt="<?php the_title(); ?>" />
		        <?php } ?>
                <h5><?php the_title(); ?></h5>
                <?php echo esc_html( $options['position'] ); ?>
            </div>
        	<div class="flips1_back flipscont1">
            	<h4 class="white"><strong><?php the_title(); ?></strong></h4>
                <p><?php echo wp_trim_words( $post->post_content, $words ); ?></p>
                <div class="fsoci">
                <?php if( $options['facebook'] != '' ){ ?>            
					<a href="https://facebook.com/<?php echo esc_attr( $options['facebook'] ); ?>"><i class="fab fa-facebook"></i></a>
				<?php } ?>
				<?php if( $options['twitter'] != '' ){ ?>    
					<a href="https://twitter.com/<?php echo esc_attr( $options['twitter'] ); ?>"><i class="fab fa-twitter"></i></a>
				<?php } ?>
				<?php if( $options['gplus'] != '' ){ ?>
					<a href="https://plus.google.com/+<?php echo esc_attr( $options['gplus'] ); ?>"><i class="fab fa-google-plus"></i></a>
				<?php } ?>
				<?php if( $options['linkedin'] != '' ){ ?>
					<a href="https://www.linkedin.com/<?php echo esc_attr( $options['linkedin'] ); ?>"><i class="fab fa-linkedin"></i></a>
				<?php } ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="but_small5 light"><i class="fas fa-location-arrow"></i>&nbsp; <?php _e('Read more','arkahost');?></a>
            </div>
		</div>
    </div>
	<?php if( $i%($atts['items'])==0 ):
	?>
	<div class="<?php echo esc_attr($cls); ?> animated <?php echo esc_attr( $eff ); ?> delay-<?php echo esc_attr( $i );?>00ms last">
		<div class="flips1">
			<div class="flips1_front flipscont1">
				<img src="<?php echo THEME_URI;?>/assets/images/hiring-1.jpg" alt="ArkaHost">
				<h5><?php _e('We are Hiring','arkahost');?></h5>
				<?php _e('Member','arkahost');?>
			</div>
			<div class="flips1_back flipscont1">
				<h4 class="white"><strong><?php _e('We are Hiring','arkahost');?></strong></h4>
			</div>
		</div>
    </div>	
	<?php endif;?>
<?php
	if( $i%4 == 0 && $atts['items'] > $i ){
		echo '<div class="clearfix margin_bottom7"></div>';
	}
	$i++;
	endwhile;
	?>
</div>
	<?php
	
}else {
	echo '<h4>' . __( 'Teams not found', 'arkahost' ) . '</h4> <a href="'.admin_url('post-new.php?post_type=our-team').'"><i class="far fa-plus"></i> Add New Staff</a>';
}
	
?>