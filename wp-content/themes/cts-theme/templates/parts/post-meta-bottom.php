<?php
/**
 * Infinity Theme: Post Meta Bottom Template
 *
 * @author Bowe Frankema <bowe@presscrew.com>
 * @link http://infinity.presscrew.com/
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package Infinity
 * @subpackage templates
 * @since 1.0
 *
 * This template display the post tags attached to a post. You can hook into this section 
 * to add your own stuff as well!
 */
?>

<footer class="post-meta-data post-bottom">
	<?php
		do_action( 'open_loop_post_meta_data_bottom' );
	?>
		<div class="post-link">
			<a class="cts-button clickable" href="<?php the_permalink(); ?>">READ ON</a>
		</div>
		<div class="post-comments">
			<span class="info-box cts-button unclickable"><?php comments_number(); ?></span>
		</div>
		<div class="post-tags">
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
		</div
<?php
	do_action( 'close_loop_post_meta_data_bottom' );
?>
</footer>
