<?php
/**
 * Infinity Theme: Post Banner Image Template
 *
 * @author Ben Talberg
 * @link http://appcanny.com
 * @copyright Copyright (C) 2013 Ben Talberg
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package CTS Connected
 * @subpackage templates
 * @since 1.0
 *
 * This template displays the post banner image
 */
?>
<div class="post-image">
	<?php
		do_action( 'before_post_thumb' );
		// If the post has a feature image, show it
		if( has_post_thumbnail() ) {
			the_post_thumbnail( 'large' );
		// Else if the post has a mime type that starts with "image/" then show the image directly.
		} elseif( 'image/' == substr( $post->post_mime_type, 0, 6 ) ) {
			echo wp_get_attachment_image( $post->ID, $thumbsize );
		}
	?>
</div>