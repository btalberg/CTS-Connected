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
			<div class="post-content">
				<?php do_action( 'open_loop_post' ); ?>
				<div id="post-container" class="container white">
			
					<?php infinity_get_template_part( 'templates/parts/post-meta-top');	?>
			
					<?php infinity_get_template_part( 'templates/parts/post-header'); ?>
			
					<div class="no-margin">
						<?php infinity_get_template_part( 'templates/parts/post-banner-image');	?>
					</div>
					
					<div class="post-content">
						<?php 
							do_action( 'open_loop_post_content' );
							$content = get_the_content();
							if (strlen($content) <= 500) {
								echo $content;
							} else {
								echo (substr($content, 0, 500) . "...");
							}
							do_action( 'close_loop_post_content' );
						?>
					</div>
			
					<?php infinity_get_template_part( 'templates/parts/post-meta-bottom');	?>
			
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
