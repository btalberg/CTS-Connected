<?php
/**
 * Defaults
 *
 * @author ben.talberg <ben.talberg@appcanny.com>
 */

/**
 * Turn on developer mode
 */
//define( 'INFINITY_DEV_MODE', true );

?>
<?php
/**
 * CTS Theme: theme functions
 *
 * @author ben.talberg <ben.talberg@appcanny.com>
 * @link http://appcanny.com
 * @copyright Copyright (C) 2013 AppCanny
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 */
 
/**
 * Add Shortcut Log Function
 */
if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

/**
 * CTS Helpers
 */
require_once( 'engine/includes/cts-helpers.php' );

/**
 * CTS Options
 */
require_once( 'engine/includes/cts-options.php' );
 
/**
 * CTS Sidebars
 */
require_once( 'engine/includes/cts-sidebars.php' );

/**
 * CTS Widgets
 */
require_once( 'engine/includes/cts-widgets-templates.php');
require_once( 'engine/includes/cts-widgets.php');

/**
 * CTS Patches
 */
require_once( 'engine/includes/cts-patches.php');

/**
 * CTS Feature Slider
 */
require_once( 'engine/includes/cts-feature-slider.php');
?>
