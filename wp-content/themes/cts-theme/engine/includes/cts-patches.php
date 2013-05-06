<?php
/*
Script Name: 	Patch broken pieces of mishmashy code
Contributors: 	Ben Talberg (btalberg@appcanny.com)
Description: 	This will fix various bugs I've run into along the way.
Version: 		0.1
*/

// Removes scripts that prevent the Events calendar from displaying
function dequeue_cmb_scripts( $hook ) {
	global $post;
	
  	if ( $hook == 'post.php' OR $hook == 'post-new.php' OR $hook == 'page-new.php' OR $hook == 'page.php' ) {
		if ( 'event' === $post->post_type ) { 
			wp_dequeue_script( 'cmb-scripts' );
			wp_deregister_script( 'cmb-scripts');
		}
  	}
}
add_action( 'admin_enqueue_scripts', 'dequeue_cmb_scripts', 11, 1 );