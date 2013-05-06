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

function wrap_group_header_start() { ?>
	<!-- html -->
	<div id="group-header-container" class="container white">
	<!-- end -->
<?php }

add_action('bp_before_group_header','wrap_group_header_start');

function wrap_group_header_end() { ?>
	<!-- html -->
	</div>
	<!-- end -->
<?php }

add_action('bp_after_group_header','wrap_group_header_end');
