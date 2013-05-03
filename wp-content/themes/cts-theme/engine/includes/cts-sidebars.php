<?php

// Add Homepage Sidebars (only registered on WP Single & Main Blog in MultiSite)
function register_cts_sidebars() {
  if ( is_main_site() )
  {
    //Cleanup the majority of default sidebars, then add new CTS SideBars
    unregister_sidebar( 'homepage-top-right' );
    unregister_sidebar( 'homepage-left' );
    unregister_sidebar( 'homepage-middle' );
    unregister_sidebar( 'homepage-right' );

    register_sidebar(array(
  		'name' => 'Homepage Middle Left',
  		'id' => 'homepage-middle-left',
  		'description' => "The Middle Left Widget on the Homepage",
  		'before_widget' => '<div id="%1$s" class="widget %2$s">',
  		'after_widget' => '</div>',
  		'before_title' => '<h4>',
  		'after_title' => '</h4>'
  	));
	
  	register_sidebar(array(
  		'name' => 'Homepage Middle Center',
  		'id' => 'homepage-middle-center',
  		'description' => "The Middle Center Widget on the Homepage",
  		'before_widget' => '<div id="%1$s" class="widget %2$s">',
  		'after_widget' => '</div>',
  		'before_title' => '<h4>',
  		'after_title' => '</h4>'
  	));
	
  	register_sidebar(array(
  		'name' => 'Homepage Middle Right',
  		'id' => 'homepage-middle-right',
  		'description' => "The Middle Right Widget on the Homepage",
  		'before_widget' => '<div id="%1$s" class="widget %2$s">',
  		'after_widget' => '</div>',
  		'before_title' => '<h4>',
  		'after_title' => '</h4>'
  	));
	
  	register_sidebar(array(
  		'name' => 'Homepage Bottom Left',
  		'id' => 'homepage-bottom-left',
  		'description' => "The Bottom Left Widget on the Homepage",
  		'before_widget' => '<div id="%1$s" class="widget %2$s">',
  		'after_widget' => '</div>',
  		'before_title' => '<h4>',
  		'after_title' => '</h4>'
  	));
	
  	register_sidebar(array(
  		'name' => 'Homepage Bottom Right',
  		'id' => 'homepage-bottom-right',
  		'description' => "The Bottom Right Widget on the Homepage",
  		'before_widget' => '<div id="%1$s" class="widget %2$s">',
  		'after_widget' => '</div>',
  		'before_title' => '<h4>',
  		'after_title' => '</h4>'
  	));
  }
}

// We have to deprioritize the deregistry/reregistry of sidebar elements because
// the parent theme's functions.php is called later
add_action( 'widgets_init', 'register_cts_sidebars', 11 );

?>