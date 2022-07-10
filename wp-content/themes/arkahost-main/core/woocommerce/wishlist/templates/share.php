<?php
/**
 * Share template
 */

global $king_wishlist, $king;

if( !is_user_logged_in() ) { return; }

if( $king->cfg['wl_facebook'] == 1 || $king->cfg['wl_twitter'] == 1 || $king->cfg['wl_pinterest'] == 1 )
    { echo king_WISHLIST_UI::get_share_links( $king_wishlist->get_wishlist_url() . '&user_id=' . get_current_user_id() ); }