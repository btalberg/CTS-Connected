<?php

/**
 * Bootstrap the BP_Group_Announcements extension
 */
function bpga_bootstrap() {
	bp_register_group_extension( 'BP_Group_Announcements' );
}
add_action( 'bp_init', 'bpga_bootstrap' );


/**
 * Implementation of BP_Group_Extension
 *
 * Registers the group extension and provides a display method
 *
 * @since 1.0
 */
class BP_Group_Announcements extends BP_Group_Extension {

	var $enable_create_step = false;
	var $enable_nav_item = true;
	var $enable_edit_item = false;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * */
	public function __construct() {
		// localization
		$this->localization();

		// extension variables
		$this->name = __( 'Announcements', 'bpga' );
		$this->slug = bpga_get_slug();

		$this->nav_item_position = 13;

		// Disable the activity update form on the group home page. Props r-a-y
		add_action( 'bp_before_group_activity_post_form', create_function( '', 'ob_start();' ), 9999 );
		add_action( 'bp_after_group_activity_post_form',  create_function( '', 'ob_end_clean();' ), 0 );

		// Because this isn't the group home page, we have to ensure
		// that 'groups' is passed as the object type with the post form
		add_action( 'bp_activity_post_form_options', array( $this, 'object_input' ) );

		// In the activity directory post form, there is a dropdown menu allowing
		// users to select the group to post their update in
		//
		// We don't want non-admins to be able to post in groups, so we blank out the
		// groups by returning false in bp_has_groups()
		add_action( 'bp_before_activity_post_form', array( $this, 'remove_groups' ) );
		add_action( 'bp_after_activity_post_form',  array( $this, 'restore_groups' ) );

		// Make sure that only announcements show up on that page
		add_filter( 'bp_ajax_querystring', array( $this, 'filter_querystring' ), 9999 );
	}

	/**
	 * Display the content of the Announcements tab
	 *
	 * @since 1.0
	 */
	public function display() {

		if ( bp_group_is_admin() || bp_current_user_can( 'bp_moderate' ) ) {
			$group_role = 'an administrator';
		} else if ( bp_group_is_mod() ) {
			$group_role = 'a moderator';
		}

		?>

		<h3><?php _e( 'Announcements', 'bpga' ) ?></h3>

		<p><?php _e( 'On this page, you&#8217;ll see announcements that administrators and moderators have left for the group.', 'bpga' ) ?></p>

		<?php if ( bpga_can_post_group_announcements() ) : ?>

			<?php $group_role = bp_group_is_admin() || bp_current_user_can( 'bp_moderate' ) ? __( 'administrator', 'bpga' ) : __( 'moderator', 'bpga' ) ?>
			<p><?php printf( __( 'As %1$s in %2$s, you can post announcements to the group&#8217;s activity stream.', 'bpga' ), $group_role, bp_get_group_name() ) ?></p>

			<?php locate_template( array( 'activity/post-form.php'), true ) ?>

		<?php endif ?>

		<div class="activity single-group">
			<?php locate_template( array( 'activity/activity-loop.php' ), true ) ?>
		</div>

		<?php
	}

	/**
	 * The post-form template logic won't provide the necessary -object
	 * -post-in hidden inputs because this is not the group home page. So
	 * we provide them manually
	 *
	 * @since 1.0
	 */
	public static function object_input() {
		if ( bp_is_group() && bp_is_current_action( bpga_get_slug() ) ) {
			echo '<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />';
			echo '<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="' . bp_get_group_id() . '" />';
		}
	}

	/**
	 * Make sure that the Announcement tab shows only updates
	 *
	 * @since 1.0.1
	 */
	public static function filter_querystring( $qs ) {
		if ( bp_is_group() && bp_is_current_action( bpga_get_slug() ) ) {
			if ( '' != $qs ) {
				$qs .= '&';
			}
			$qs .= 'type=activity_update&action=activity_update';
		}

		return $qs;
	}

	/**
	 * Custom textdomain loader.
	 *
	 * Checks WP_LANG_DIR for the .mo file first, then the plugin's language folder.
	 * Allows for a custom language file other than those packaged with the plugin.
	 *
	 * @since 1.0.2
	 *
	 * @uses get_locale() To get the current locale
	 * @uses load_textdomain() Loads a .mo file into WP
	 * @return bool True on success, false on failure
	 */
	public function localization() {
		// Use the WP plugin locale filter from load_plugin_textdomain()
		$locale        = apply_filters( 'plugin_locale', get_locale(), 'bpga' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'bpga', $locale );

		$mofile_global = trailingslashit( constant( 'WP_LANG_DIR' ) ) . $mofile;
		$mofile_local  = trailingslashit( dirname( __FILE__ ) ) . 'languages/' . $mofile;

		// look in /wp-content/languages/ first
		if ( is_readable( $mofile_global ) ) {
			return load_textdomain( 'bpga', $mofile_global );

		// if that doesn't exist, check for bundled language file
		} elseif ( is_readable( $mofile_local ) ) {
			return load_textdomain( 'bpga', $mofile_local );

		// no language file exists
		} else {
			return false;
		}
	}

	/**
	 * Return no groups for the logged-in user when on the activity directory's
	 * post form.
	 *
	 * This is done so no groups can be listed under the post form dropdown menu.
	 *
	 * Note: This only occurs for users without the 'bp_moderate' capability.
	 * We don't do checks for group admins or mods as that would be intensive.
	 *
	 * @since 1.0.2
	 *
	 * @uses current_user_can() To see if the current user can do something
	 */
	public function remove_groups() {
		if ( ! bp_current_user_can( 'bp_moderate' ) )
			add_filter( 'bp_has_groups', '__return_false', 1, 1 );
	}

	/**
	 * Restore groups for the logged-in user after the activity directory post
	 * form.
	 *
	 * This is to prevent any unintended behavior when returning false for
	 * bp_has_groups().
	 *
	 * Note: We restore groups for users without the 'bp_moderate' capability.
	 *
	 * @since 1.0.2
	 *
	 * @uses current_user_can() To see if the current user can do something
	 * @see BP_Group_Announcements::remove_groups()
	 */
	public function restore_groups() {
		if ( ! bp_current_user_can( 'bp_moderate' ) )
			remove_filter( 'bp_has_groups', '__return_false', 1, 1 );
	}
}

/**
 * Return the Announcements slug
 */
function bpga_get_slug() {
	return apply_filters( 'bpga_get_slug', 'announcements' );
}

/**
 * Can the current user post group announcements?
 *
 * By default, posting is limited to
 *
 * @since 1.0
 */
function bpga_can_post_group_announcements() {
	$can_post = false;

	if ( bp_is_group() ) {
		if ( bp_current_user_can( 'bp_moderate' ) ||
		     bp_group_is_admin() ||
		     bp_group_is_mod()
		   )
		{
			$can_post = true;
		}
	}

	return apply_filters( 'bpga_can_post_group_announcements', $can_post );
}

