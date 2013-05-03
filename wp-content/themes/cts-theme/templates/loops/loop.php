<?php
/**
 * Infinity Theme: loop template
 *
 * The loop that displays posts
 * 
 * @author Bowe Frankema <bowe@presscrew.com>
 * @link http://infinity.presscrew.com/
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package Infinity
 * @subpackage templates
 * @since 1.0
 */

	if ( have_posts() ):
		while ( have_posts() ):
			the_post();
			do_action( 'open_loop' );
?>
	<div class="post-content column eleven">
	<?php do_action( 'open_loop_post' ); ?>
		<div id="post-container">
			<div class="post-header">
				<div class="post-header-date">
					<?php the_time("d M Y"); ?>
				</div>
				<div class="post-header-tags">
					<?php
						$posttags = get_the_tags();
						$count = 0;
						if ($posttags) {
						  foreach($posttags as $tag) {
							$count++;

							if (4 == $count) break;

							echo "<a href=\"";
							echo get_tag_link($tag->term_id);
						    echo "\">" . $tag->name . " " . "</a>";
						  }
						}
					?>
				</div>
			</div>
			<div class="post-title">
				<a href="<?php the_permalink(); ?>" ?><?php the_title(); ?></a><?php edit_post_link(' ✍','',' ');?>
			</div>
			<div class="post-author">
				<?php echo "by "; the_author(); ?>
			</div>
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
			<div class="post-content">
				<?php 
					do_action( 'open_loop_post_content' );
					the_content();
					do_action( 'close_loop_post_content' );
				?>
			</div>
			<div class="post-footer">
				<div class="post-footer-link">
					<a class="cts-button clickable" href="<?php the_permalink(); ?>">READ ON</a>
				</div>
				<div class="post-footer-comments">
					<span class="info-box cts-button unclickable"><?php comments_number(); ?></span>
				</div>
			</div>
		</div><!-- post-container -->
	<?php do_action( 'close_loop_post' ); ?>
	</div><!-- post-content -->
	<?php
		do_action( 'close_loop' );
	endwhile;
		if ( current_theme_supports( 'infinity-pagination' ) ) :
   		infinity_base_paginate();
    	endif;
	else:
?>
		<h2 class="center">
			<?php _e( 'Not Found', infinity_text_domain ) ?>
		</h2>
<?php
		infinity_get_search_form();
		do_action( 'loop_not_found' );
	endif;
?>
