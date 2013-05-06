<?php
/**
  * Modify the CBOX feature post type. Add a url field.
  */

// Hook into WordPress
add_action( 'admin_init', 'add_features_url_metabox' );
add_action( 'save_post', 'save_custom_url' );

/**
 * Add meta box
 */
function add_features_url_metabox() {
	add_meta_box( 'custom-metabox', __( 'URL &amp; Description' ), 'url_custom_metabox', 'features', 'normal', 'high' );
}

/**
 * Display the metabox
 */
function url_custom_metabox() {
	
	global $post;
	$urllink = get_post_meta( $post->ID, 'urllink', true );
	$urldesc = get_post_meta( $post->ID, 'urldesc', true );

	if ( !preg_match( "/http(s?):\/\//", $urllink )) {
		$errors = 'Url not valid';
		$urllink = 'http://';
	} 

	// output invlid url message and add the http:// to the input field
	if( $errors ) { echo $errors; } ?>

	<p><label for="siteurl">Url:<br />
		<input id="siteurl" size="37" name="siteurl" value="<?php if( $urllink ) { echo $urllink; } ?>" /></label></p>
	<p><label for="urldesc">Description:<br />
		<textarea id="urldesc" name="urldesc" cols="45" rows="4"><?php if( $urldesc ) { echo $urldesc; } ?></textarea></label></p>
<?php
}

/**
 * Process the custom metabox fields
 */
function save_custom_url( $post_id ) {
	global $post;	

	if( $_POST ) {
		update_post_meta( $post->ID, 'urllink', $_POST['siteurl'] );
		update_post_meta( $post->ID, 'urldesc', $_POST['urldesc'] );
	}
}

/**
 * Get and return the values for the URL and description
 */
function get_url_desc_box() {
	global $post;
	$urllink = get_post_meta( $post->ID, 'urllink', true );
	$urldesc = get_post_meta( $post->ID, 'urldesc', true );

	return array( $urllink, $urldesc );
}
?>
