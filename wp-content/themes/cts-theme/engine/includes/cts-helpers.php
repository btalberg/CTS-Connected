<?php
/**
 * Add Helper Fucntions
 *
 * @package CTS
 * @subpackage base
 */

/**
 * Disable Wordpress Toolbar
 *
 * @package CTS
 * @subpackage base
 */
function convert_list_column_number_to_string($column_number) {
  	switch(intval($column_number)) { 
		case 1:
			return "single";
			break;
		case 2:
			return "double";
			break;
		case 3:
			return "triple";
			break;			
		case 4:
			return "quad";
			break;
		case 6:
			return "six";
			break;
	}
}

?>
