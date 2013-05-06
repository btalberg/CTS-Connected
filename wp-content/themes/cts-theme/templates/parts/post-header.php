<?php
/**
 * Infinity Theme: Post Header Template
 *
 * @author Ben Talberg
 * @link http://appcanny.com
 * @copyright Copyright (C) 2013 Ben Talberg
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package CTS Connected
 * @subpackage templates
 * @since 1.0
 *
 * This template displays the post header, which includes the title and the author info.
 */
?>
<div class="post-header">
	<?php
		do_action( 'open_loop_post_header' );
	?>
	<div class="title post-title">
		<a href="<?php the_permalink(); ?>" ?><?php the_title(); ?></a><?php edit_post_link(' ✍','',' ');?>
	</div>
	<div class="post-author">
		<?php echo "by "; echo bp_core_get_userlink ( get_the_author_meta( 'ID' ) ); ?>
	</div>
	<?php
		do_action( 'close_loop_post_header' );
	?>
</div>