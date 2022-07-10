<?php
/*
(c) copyright king-theme.com
*/

	$pagination = array(
		'base' => @add_query_arg('paged','%#%'),
		'format' => '/page/%#%',
		'total' => $wp_query->max_num_pages,
		'current' => $current,
		'show_all' => false,
		'type' => 'array',
		'prev_next'=> true,
		'prev_text'=> 'Prev',
		'next_text'=> 'Next'				
	);

	if( !empty($wp_query->query_vars['s'] ) ){
			$pagination['add_args'] = array( 's' => urlencode( get_query_var( 's' ) ) );
	}
	$pgn = paginate_links( $pagination );


?>	
		
		<div id="pagenation">
			
			<?php
				if(!empty($pgn)){
					echo '<div class="pag alignleft">';
					echo '<ul class="b_pag center p_b">';
					foreach($pgn as $k => $link){
						print '<li>' . str_replace( "'" , '"' , $link ) . '</li>';
					}
					echo '</ul>';
					echo '</div>';
				}
			?>
		</div>
