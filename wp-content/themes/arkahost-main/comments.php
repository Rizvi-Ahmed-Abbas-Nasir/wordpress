<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to king_comment() which is
 * located in the functions.php file.
 *
 */
?>
	<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'arkahost' ); ?></p>
	</div><!-- #comments -->
	<?php
			/* Stop the rest of comments.php from being processed,
			 * but don't kill the script entirely -- we still have
			 * to fully load the template.
			 */
			return;
		endif;
	?>

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
	
		<div class="clearfix margin_top5"></div>
		
		<h4 id="comments-title">
			<?php
				printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'arkahost' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h4>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'arkahost' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'arkahost' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'arkahost' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<ol class="commentlist">
			<?php
				wp_list_comments( array( 'callback' => 'king_comment' ) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'arkahost' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'arkahost' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'arkahost' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

	<?php
		/* If there are no comments and comments are closed, let's leave a little note, shall we?
		 * But we don't want the note on pages or post types that do not support comments.
		 */
		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'arkahost' ); ?></p>
	<?php endif; ?>

	<?php 
		
		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );
		
		$args = array(
		
		  'id_form'           => 'commentform',
		  'id_submit'         => 'comment_submit',
		  'title_reply'       => __( 'Leave a Reply', 'arkahost' ),
		  'title_reply_to'    => __( 'Leave a Reply to %s', 'arkahost' ),
		  'cancel_reply_link' => __( 'Cancel Reply', 'arkahost' ),
		  'label_submit'      => __( 'Submit Comment', 'arkahost' ),
		
		  'comment_field' =>  '<p class="comment-form-comment"><textarea id="comment" name="comment" class="comment_textarea_bg" cols="45" rows="8" aria-required="true">' .
		    '</textarea></p><div class="clearfix"></div>',
		
		  'must_log_in' => '<p class="must-log-in">' .
		    sprintf(
		      wp_kses( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'arkahost' ), array('a'=>array())),
		      wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
		    ) . '</p>',
		
		  'logged_in_as' => '<p class="logged-in-as">' .
		    sprintf(
		    wp_kses( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'arkahost' ),array('a'=>array())),
		      admin_url( 'profile.php' ),
		      $user_identity,
		      wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
		    ) . '</p>',
		
		  'comment_notes_before' => '',
		
		  'comment_notes_after' => '',
		
		  'fields' => apply_filters( 'comment_form_default_fields', array(
		
		    'author' =>
		      '<p class="comment-form-author">' .
		      '<input id="author" class="comment_input_bg" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
		      '" size="30"' . $aria_req . ' />'.
		      '<label for="author">' . __( 'Name', 'arkahost' ) .( $req ? '*' : '' ) . '</label></p>',
		
		    'email' =>
		      '<p class="comment-form-email">' .
		      '<input id="email" class="comment_input_bg" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
		      '" size="30"' . $aria_req . ' /> <label for="email">' . __( 'Email', 'arkahost' ) .( $req ? '*' : '' ) . '</label></p>',
		
		    'url' =>
		      '<p class="comment-form-url">'.
		      '<input id="url" class="comment_input_bg" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'], 'arkahost' ) . '" size="30" />'
		      .'<label for="url">' .__( 'Website', 'arkahost' ) . '</label>' .
		      '</p>'
		    )
		  ),
		);

		comment_form( $args ); 
	
	?>

</div><!-- #comments -->
