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


// Modify  options on the user profile views
function cts_remove_profile_nav_links() {
	bp_core_remove_nav_item( 'forums' );
	bp_core_remove_nav_item( 'invite-anyone' );
	bp_core_remove_nav_item( 'docs' );
}

add_action( 'bp_setup_nav', 'cts_remove_profile_nav_links', 15 );

if ( !function_exists( 'cts_dtheme_comment_form' ) ) :
/**
 * Applies CTS customisations to the post comment form.
 *
 * @param array $default_labels The default options for strings, fields etc in the form
 * @see comment_form()
 * @since BuddyPress (1.5)
 */
function cts_dtheme_comment_form( $default_labels ) {

	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );
	$fields    =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'buddypress' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'buddypress' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'buddypress' ) . '</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$new_labels = array(
		'comment_field'  => '<p class="form-textarea"><textarea name="comment" id="comment" cols="30" rows="5" aria-required="true"></textarea></p>',
		'fields'         => apply_filters( 'comment_form_default_fields', $fields ),
		'logged_in_as'   => '',
		'must_log_in'    => '<p class="alert">' . sprintf( __( 'You must be <a href="%1$s">logged in</a> to post a comment.', 'buddypress' ), wp_login_url( get_permalink() ) )	. '</p>',
		'title_reply'    => __( 'Connect!', 'cts' )
	);

	return apply_filters( 'cts_dtheme_comment_form', array_merge( $default_labels, $new_labels ) );
}
add_filter( 'comment_form_defaults', 'cts_dtheme_comment_form', 10 );
endif;
?>