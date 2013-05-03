<?php
/**
 * Add Core Options
 *
 * @package CTS
 * @subpackage base
 * @todo move this to a feature extension, no direct plugin support
 */

/**
 * Disable Wordpress Toolbar
 *
 * @package CTS
 * @subpackage base
 */
function remove_admin_bar() {
  	if (!is_admin() && !current_user_can('add_users')){
		show_admin_bar( false );
	}
}
add_action('init', 'remove_admin_bar');
add_action('open_head','remove_admin_bar');

function remove_admin_bar_style_backend() { 
	if (!is_admin() && !current_user_can('add_users')){
		echo '<style>body { border-top: 0px !important; } html { margin-top: 0px !important; } </style>';
	}
}
add_action('init','remove_admin_bar_style_backend');

function remove_admin_bar_initiator() {
	if (!is_admin() && !current_user_can('add_users')){
		remove_action('wp_head', '_admin_bar_bump_cb');
	}
}
add_action('get_header', 'remove_admin_bar_initiator');

?>