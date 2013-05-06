<?php
/**
 * Infinity Theme: Post Meta Top Template
 *
 * @author Bowe Frankema <bowe@presscrew.com>
 * @link http://infinity.presscrew.com/
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package Infinity
 * @subpackage templates
 * @since 1.0
 *
 * This template display the post meta date attached to a post. You can hook into this section 
 * to add your own stuff as well!
 */
?>
<div class="top post-meta-data post-top">
<?php
	do_action( 'open_loop_post_meta_data_top' );
?>		
	<div class="post-date">
		<?php the_time("d M Y"); ?>
	</div>

	<div class="post-category">
		<?php the_category(', ') ?>						
	</div>
<?php
	do_action( 'close_loop_post_meta_data_top' );
?>
</div>