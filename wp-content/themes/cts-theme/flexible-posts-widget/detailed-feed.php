<?php
/**
 * Template Name: detailed-feed.php
 *
 * "Flexible widget" template for detailed post feed.
 *
 * @author Ben Talberg <ben.talberg@gmail.com>
 * @link http://appcanny.com
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @since 1.0
 */
?>
	<div id="post-feed" class="widget flexible-posts">
<?php
echo $before_widget;

if ( !empty($title) )
	echo $before_title . $title . $after_title;

if( $flexible_posts->have_posts() ):
?>
	<?php while( $flexible_posts->have_posts() ) : $flexible_posts->the_post(); global $post; ?>
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
				<a href="<?php the_permalink(); ?>" ?><?php the_title(); ?><?php edit_post_link(' ✍','',' ');?></a>
			</div>
			<div class="post-author">
				<?php echo "by "; the_author(); ?>
			</div>
			<div class="post-image">
				<?php
					if( $thumbnail == true ) {
						// If the post has a feature image, show it
						if( has_post_thumbnail() ) {
							the_post_thumbnail( $thumbsize );
						// Else if the post has a mime type that starts with "image/" then show the image directly.
						} elseif( 'image/' == substr( $post->post_mime_type, 0, 6 ) ) {
							echo wp_get_attachment_image( $post->ID, $thumbsize );
						}
					}
				?>
			</div>
			<div class="post-content">
				<?php the_content(); ?>
			</div>
			<div class="post-footer">
				<div class="post-footer-link">
					<a class="cts-button clickable" href="<?php the_permalink(); ?>">READ ON</a>
				</div>
				<div class="post-footer-comments">
					<span class="info-box cts-button unclickable"><?php comments_number(); ?></span>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
<?php endif; // End have_posts() ?>
</div> <!-- post-feed -->
<?php echo $after_widget; ?>