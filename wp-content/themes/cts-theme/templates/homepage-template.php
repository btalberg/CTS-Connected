<?php
/**
 * Template Name: Homepage Template
 * 
 * Original author: Bowe Frankema <bowe@presscrew.com>
 *
 * @author Ben Talberg <ben.talberg@gmail.com>
 * @link http://appcanny.com
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @since 1.0
 */
    infinity_get_header();
?>
<div id="content" role="main" class="column sixteen">
	<div id="top-homepage" class="row">
		<div id="flex-slider-wrap-full" class="column sixteen">
			<!-- load template for the slider-->
			<?php
				infinity_load_template( 'templates/cts-slider-template.php' );
			?>
			<!-- end -->
		</div>
	</div>
    <?php
        do_action( 'open_content' );
        do_action( 'open_home' );
    ?>  
    <div id="center-homepage-widget">
		<?php
		    dynamic_sidebar( 'Homepage Center Widget' );
		?>
	</div>      
	<div class="homepage-widgets row">
	    <div id="homepage-widget-left" class="column six">         
	            <?php
	                dynamic_sidebar( 'Homepage Middle Left' );
	            ?>
	    </div>
	             
	    <div id="homepage-widget-middle" class="column five">  
	            <?php
	                dynamic_sidebar( 'Homepage Middle Center' );
	            ?>
	    </div>
	     
	    <div id="homepage-widget-right" class="column five">   
	            <?php
	            	dynamic_sidebar( 'Homepage Middle Right' );
	            ?>
	    </div>  
	</div>
	<div class="homepage-widgets row">
	    <div id="homepage-widget-left" class="column eight">         
	            <?php
	                dynamic_sidebar( 'Homepage Bottom Left' );
	            ?>
	    </div>
	    
	    <div id="homepage-widget-right" class="column eight">   
	            <?php
	            	dynamic_sidebar( 'Homepage Bottom Right' );
	            ?>
	    </div>  
	</div>    
    <?php
        do_action( 'close_home' );
        do_action( 'close_content' );
    ?>
</div>
<?php
    infinity_get_footer();
?>