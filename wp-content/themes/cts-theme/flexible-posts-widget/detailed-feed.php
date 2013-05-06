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

	if ( $flexible_posts->have_posts() ):
		while ( $flexible_posts->have_posts() ):
			$flexible_posts->the_post();
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
							the_content();
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
	endif;
?> 
</div> <!-- post-feed -->
<?php
	echo $after_widget;
?>