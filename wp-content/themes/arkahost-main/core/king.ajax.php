<?php

/* Timeline history lazy load */
add_action('wp_ajax_nopriv_loadPostsTimeline', 'king_ajax_loadPostsTimeline');
add_action('wp_ajax_loadPostsTimeline', 'king_ajax_loadPostsTimeline');

function king_ajax_loadPostsTimeline( $index = 0 ){

	global $king, $wpdb, $cat;

	if( !empty( $_REQUEST['index'] ) ){
		$index = $_REQUEST['index'];
	}
	$limit = get_option('posts_per_page');

	$cates = '';
	$cat_req = 0;
	if( empty( $king->cfg['timeline_categories'] ) ){
		$king->cfg['timeline_categories'] = '';
	}else if( $king->cfg['timeline_categories'][0] == 'default' ){
		$king->cfg['timeline_categories'] = '';
	}
	if( is_array( $king->cfg['timeline_categories'] ) ){
		$cates = implode( ',', $king->cfg['timeline_categories'] );
	}


	if( !empty( $_REQUEST['cat'] ) &&  $_REQUEST['cat'] != $cat_req){
		$cates = $_REQUEST['cat'];
		$cat_req = $_REQUEST['cat'];
	}

	if( is_category() ){
		
		$cates = $cat;
		$cat_req = $cat;
	}


	$cfg = array(
			'post_type' => 'post',
			'category' => $cates,
			'posts_per_page' => $limit,
			'offset' => $index,
			'post_status'      => 'publish',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
		);

	$posts = get_posts( $cfg );

	$cfg['offset'] = 0;
	$cfg['posts_per_page'] = 1000;

	$total = count( get_posts( $cfg ) );


	if( count( $posts ) >= 1 && is_array( $posts ) ){

		$i = 0;

		foreach( $posts as $post ){

			$img = esc_url( king_createLinkImage( $king->get_featured_image( $post, true ), '120x120xc' ) );
			if( strpos( $img, 'youtube') !== false ){
				$img = explode( 'embed/', $img );
				if( !empty( $img[1] ) ){
					$img = 'http://img.youtube.com/vi/'.$img[1].'/0.jpg';
				}
			}
		?>

		<div class="cd-timeline-block animated fadeInUp">
			<div class="cd-timeline-img cd-picture animated eff-bounceIn delay-200ms">
				<img src="<?php echo esc_url( $img ); ?>" alt="ArkaHost">
			</div>

			<div class="cd-timeline-content animated eff-<?php if( $i%2 != 0 )echo 'fadeInRight';else echo 'fadeInLeft'; ?> delay-100ms">
				<a href="<?php echo get_the_permalink($post->ID); ?>"><h2><?php echo esc_html( $post->post_title ); ?></h2></a>
				<p class="text"><?php echo substr( strip_tags( apply_filters('get_the_content', $post->post_content)),0,150); ?>...</p>
				<a href="<?php echo get_the_permalink($post->ID); ?>" class="cd-read-more"><?php echo __('Read more', 'arkahost');?></a>
				<span class="cd-date">
					<?php
						$date = esc_html( get_the_date('M d Y', $post->ID ) );
						if( ($i+$index)%2 == 0 ){
							echo '<strong>'.$date.'</strong>';
						}else{
							echo '<b>'.$date.'</b>';
						}
					?>
				</span>
			</div>
		</div>

		<?php
			$i++;
		}
	}

	if( $index + $limit < $total ){
		echo '<a href="#" onclick="return timelineLoadmore('.($index+$limit).', ' . $cat_req . ', this)" class="btn btn-info aligncenter" style="margin-bottom: -80px;">' . __('Load more', 'arkahost') . '<i class="fa fa-angle-double-down"></i></a>';
	}else{
		echo '<span class="aligncenter cd-nomore">' . __('No More Article', 'arkahost') . '</span>';
	}

	if( !empty( $_REQUEST['index'] ) ){
		exit;
	}

}

/* Timeline history lazy load */
add_action('wp_ajax_nopriv_loadPostsMasonry', 'king_ajax_loadPostsMasonry');
add_action('wp_ajax_loadPostsMasonry', 'king_ajax_loadPostsMasonry');

function king_ajax_loadPostsMasonry( $index = 0 ){

	global $king, $wpdb;

	$limit = get_option('posts_per_page');

	$cates = '';
	if( empty( $king->cfg['timeline_categories'] ) ){
		$king->cfg['timeline_categories'] = '';
	}else if( $king->cfg['timeline_categories'][0] == 'default' ){
		$king->cfg['timeline_categories'] = '';
	}
	if( is_array( $king->cfg['timeline_categories'] ) ){
		$cates = implode( ',', $king->cfg['timeline_categories'] );
	}

	$cfg = array(
			'post_type' => 'post',
			'category' => $cates,
			'posts_per_page' => 500,
			'offset' => $limit,
			'post_status'      => 'publish',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
		);

	$posts = get_posts( $cfg );

	$cfg['offset'] = 0;

	$total = count( get_posts( $cfg ) );


	if( count( $posts ) >= 1 && is_array( $posts ) ){

		$i = 0;$j=1;

		foreach( $posts as $post ){

			if( $i%$limit == 0 ){
				echo '<div class="cbp-loadMore-block'.($j++).'">'."\n";
			}

			$height = 750;
			$cap = 'two';
			$heighClass = ' cbp-l-grid-masonry-height4';
			$rand = rand(0,10);
			if( $rand >= 3 ){
				$height = 600;
				$heighClass = ' cbp-l-grid-masonry-height3';
				$cap = 'three';
			}else if( $rand >= 6 ){
				$height = 450;
				$heighClass = ' cbp-l-grid-masonry-height2';
				$cap = 'two';
			}

			$cats = get_the_category( $post->ID );
			$catsx = array();
			for( $l=0; $l<2; $l++ ){
				if( !empty($cats[$l]) ){
					array_push($catsx, $cats[$l]->name);
				}
			}
		?>

			<div class="cbp-item<?php echo esc_attr( $heighClass ); ?>">
		       <div class="cbp-caption">
		            <div class="cbp-caption-defaultWrap <?php echo esc_attr( $cap ); ?>">
		            	 <a href="<?php echo get_permalink( $post->ID ); ?>">
				            <?php

								$img = $king->get_featured_image( $post, true );
								if( !empty( $img ) )
								{
									if( strpos( $img , 'youtube') !== false )
									{
										$img = THEME_URI.'/assets/images/default.jpg';
									}
									$img = king_createLinkImage( $img, '570x'.$height.'xc' );

									echo '<img alt="'.get_the_title().'" class="featured-image" src="'.$img.'" />';
								}

							?>
		            	 </a>
		            </div>
		            <a href="<?php echo get_permalink( $post->ID ); ?>" class="cbp-l-grid-masonry-projects-title"><?php echo wp_trim_words( $post->post_title, 4 ); ?></a>
		            <div class="cbp-l-grid-masonry-projects-desc"><?php echo implode( ' / ', $catsx ); ?></div>
		       </div>
	 		</div>

		<?php
			$i++;
			if( $i%$limit == 0 ){
				echo '</div>'."\n";
			}
		}
	}

	exit;

}


function king_ajax(){

	global $king;

	$task = !empty( $_POST['task'] )? $_POST['task']: '';
	$id = $king->vars('id');
	$amount = $king->vars('amount');

	switch( $task ){

		case 'twitter' :

			TwitterWidget::returnTweet( $id, $amount );
			exit;

		break;

		case 'flickr' :

			$link = "http://api.flickr.com/services/feeds/photos_public.gne?id=".$id."&amp;lang=en-us&amp;format=rss_200";

			$connect = $king->ext['ci']();
			curl_setopt_array( $connect, array( CURLOPT_URL => $link, CURLOPT_RETURNTRANSFER => true ) );
			$photos = $king->ext['ce']( $connect);
			curl_close($connect);
			if( !empty( $photos ) ){
				$photos = simplexml_load_string( $photos );
				if( count( $photos->entry ) > 1 ){
					for( $i=0; $i<$amount; $i++ ){
						$image_url = $photos->entry[$i]->link[1]['href'];
						//find and switch to small image
						$image_url = str_replace("_b.", "_s.", $image_url);
						echo '<a href="'.$photos->entry[$i]->link['href'].'" target=_blank><img src="'.$image_url.'" /></a>';
					}
				}
			}else{
				echo 'Error: Can not load photos at this moment.';
			}

			exit;

		break;

	}

}


add_action('wp_ajax_loadSectionsSample', 'king_ajax_loadSectionsSample');

function king_ajax_loadSectionsSample(){

	global $king;

	$install = '';
	if( !empty( $_POST['install'] ) ){
		$install = '&install='.$_POST['install'];
	}
	if( !empty( $_POST['page'] ) ){
		$install .= '&page='.$_POST['page'];
	}

	$data = $king->ext['fg']( 'http://'.$king->api_server.'/sections/arkahost/?key=ZGV2biEu'.$install );

	if( empty( $data ) ){

		$connect = $king->ext['ci']();
		$option = array( CURLOPT_URL => 'http://'.$king->api_server.'/sections/arkahost/?key=ZGV2biEu'.$install, CURLOPT_RETURNTRANSFER => true );
		curl_setopt_array( $connect, $option );

		$data = $king->ext['ce']( $connect);

		curl_close($connect);

	}
	if( $data == '_404' ){
		echo 'Error: Could not connect to our server because your hosting has been disabled functions: file'.'_get'.'_contents() and cURL method. Please contact with hosting support to enable these functions.';
		exit;
	}
	
	$king->ext['pr']( $data );

	exit;

}



add_action('wp_ajax_verifyPurchase', 'king_ajax_verifyPurchase');

function king_ajax_verifyPurchase(){

	global $king;

	if( !isset( $_POST['code'] ) || empty( $_POST['code'] ) ){

		$data = array(
			'message' => __('Error! Empty Code.', 'arkahost'),
			'status' => 0
		);

		wp_send_json( $data );

		exit;

	}

	$key = $king->ext['be']( $_POST['code'] );
	$url = $king->ext['be']( $king->bsp( site_url() ) );
	$url = 'http://'.$url.'.resp.king-theme.com/api/purchase.php?key='.$key;
	//$url = 'http://tuongpg.api/api/purchase.php?key='.$key;

	$request = wp_remote_get( $url );
	$response = wp_remote_retrieve_body( $request );
	$response = @json_decode( $response );

	if( !empty( $response ) && ( is_object( $response ) ) )
	{

		if( $response->status == 1 )
		{
			if( get_option( 'king_valid', true ) !== false )
				update_option( 'king_valid', $king->bsp( site_url() ) );
			else add_option( 'king_valid', $king->bsp( site_url() ), null, 'no' );

			if( get_option( 'king_purchase_code', true ) !== false )
				update_option( 'king_purchase_code', esc_attr( $_POST['code'] ) );
			else add_option( 'king_purchase_code', esc_attr( $_POST['code'] ), null, 'no' );

		}else if( $response->status == 0 ){
			delete_option( 'king_valid' );
			delete_option( 'king_purchase_code' );
		}

	}

	wp_send_json( $response );

	exit;

}
