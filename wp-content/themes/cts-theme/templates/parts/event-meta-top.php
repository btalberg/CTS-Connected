<?php
/**
 * Infinity Theme: Event Meta Top Template
 *
 * @author Ben Talberg <btalberg@appcanny.com>
 * @link http://appcanny.com
 * @copyright Copyright (C) 2013 Ben Talberg
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package CTS Connected
 * @subpackage templates
 * @since 1.0
 *
 * This template display the post meta date attached to an event.
 */
?>
<div class="top post-meta-data post-top">
<?php
	do_action( 'open_loop_post_meta_data_top' );
?>		
	<div class="post-date">
		<?php echo EM_Events::output(array('format'=>'#_EVENTDATES #_EVENTTIMES')) ?>
	</div>

	<div class="post-category">
		<?php the_category(', ') ?>						
	</div>
<?php
	do_action( 'close_loop_post_meta_data_top' );
?>
</div>