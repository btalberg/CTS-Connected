<?php
/**
 * Widget Template Tags and API.
 *
 * @package WordPress
 * @subpackage Template
 */

//
// Helper functions
//

/**
 * Retrieve HTML list content for category list.
 *
 * @uses Walker_Category to create HTML list content.
 * @since 2.1.0
 * @see Walker_Category::walk() for parameters and return description.
 */
function walk_cts_category_tree() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
		$walker = new CTS_Walker_Category;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

/**
 * Display or retrieve the HTML list of categories.
 *
 * The list of arguments is below:
 *     'show_option_all' (string) - Text to display for showing all categories.
 *     'orderby' (string) default is 'ID' - What column to use for ordering the
 * categories.
 *     'order' (string) default is 'ASC' - What direction to order categories.
 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
 * in the category.
 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
 * don't have any posts attached to them.
 *     'use_desc_for_title' (bool|int) default is 1 - Whether to use the
 * description instead of the category title.
 *     'feed' - See {@link get_categories()}.
 *     'feed_type' - See {@link get_categories()}.
 *     'feed_image' - See {@link get_categories()}.
 *     'child_of' (int) default is 0 - See {@link get_categories()}.
 *     'exclude' (string) - See {@link get_categories()}.
 *     'exclude_tree' (string) - See {@link get_categories()}.
 *     'echo' (bool|int) default is 1 - Whether to display or retrieve content.
 *     'current_category' (int) - See {@link get_categories()}.
 *     'title_li' (string) - See {@link get_categories()}.
 *     'depth' (int) - The max depth.
 *
 * @since 2.1.0
 *
 * @param string|array $args Optional. Override default arguments.
 * @return string HTML content only if 'echo' argument is 0.
 */
function cts_list_categories( $args = '' ) {
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => __('No categories'),
		'orderby' => 'name', 'order' => 'ASC',
		'style' => 'list',
		'show_count' => 0, 'hide_empty' => 1,
		'use_desc_for_title' => 1, 'child_of' => 0,
		'feed' => '', 'feed_type' => '',
		'feed_image' => '', 'exclude' => '',
		'exclude_tree' => '', 'current_category' => 0,
		'hierarchical' => false, 'title_li' => __( 'Categories' ),
		'echo' => 1, 'depth' => 0,
		'taxonomy' => 'category'
	);

	$r = wp_parse_args( $args, $defaults );

	if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] )
		$r['pad_counts'] = true;

	if ( true == $r['hierarchical'] ) {
		$r['exclude_tree'] = $r['exclude'];
		$r['exclude'] = '';
	}

	if ( !isset( $r['class'] ) )
		$r['class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];

	extract( $r );

	if ( !taxonomy_exists($taxonomy) )
		return false;

	$categories = get_categories( $r );

	$output = '';
	if ( $title_li && 'list' == $style )
			$output = '<li class="' . esc_attr( $class ) . '">' . $title_li . '<ul>';

	if ( empty( $categories ) ) {
		if ( ! empty( $show_option_none ) ) {
			if ( 'list' == $style )
				$output .= '<li>' . $show_option_none . '</li>';
			else
				$output .= $show_option_none;
		}
	} else {
		if ( ! empty( $show_option_all ) ) {
			$posts_page = ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
			$posts_page = esc_url( $posts_page );
			if ( 'list' == $style )
				$output .= "<li><a href='$posts_page'>$show_option_all</a></li>";
			else
				$output .= "<a href='$posts_page'>$show_option_all</a>";
		}

		if ( empty( $r['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
			$current_term_object = get_queried_object();
			if ( $r['taxonomy'] == $current_term_object->taxonomy )
				$r['current_category'] = get_queried_object_id();
		}

		if ( $hierarchical )
			$depth = $r['depth'];
		else
			$depth = -1; // Flat.

		$output .= walk_cts_category_tree( $categories, $depth, $r );
	}

	if ( $title_li && 'list' == $style )
		$output .= '</ul></li>';

	$output = apply_filters( 'wp_list_categories', $output, $args );

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Create HTML list of categories.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class CTS_Walker_Category extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'category';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] )
			return;

		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] )
			return;

		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int $depth Depth of category in reference to parents.
	 * @param array $args
	 */
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);

		$cat_name = esc_attr( $category->name );
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );
		$link = '<a href="' . esc_url( get_term_link($category) ) . '" ';
		if ( $use_desc_for_title == 0 || empty($category->description) )
			$link .= 'title="' . esc_attr( sprintf(__( 'View all posts filed under %s' ), $cat_name) ) . '"';
		else
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		$link .= '>';
		$link .= $cat_name . '</a>';

		if ( !empty($feed_image) || !empty($feed) ) {
			$link .= ' ';

			if ( empty($feed_image) )
				$link .= '(';

			$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $feed_type ) ) . '"';

			if ( empty($feed) ) {
				$alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
			} else {
				$title = ' title="' . $feed . '"';
				$alt = ' alt="' . $feed . '"';
				$name = $feed;
				$link .= $title;
			}

			$link .= '>';

			if ( empty($feed_image) )
				$link .= $name;
			else
				$link .= "<img src='$feed_image'$alt$title" . ' />';

			$link .= '</a>';

			if ( empty($feed_image) )
				$link .= ')';
		}

		if ( !empty($show_count) )
			$link .= ' <div id="cat-meta"><span class="activity">' . intval($category->count) . '</></>';

		if ( 'list' == $args['style'] ) {
			$output .= "\t<li";
			$class = 'cat-item cat-item-' . $category->term_id;
			if ( !empty($current_category) ) {
				$_current_category = get_term( $current_category, $category->taxonomy );
				if ( $category->term_id == $current_category )
					$class .=  ' current-cat';
				elseif ( $category->term_id == $_current_category->parent )
					$class .=  ' current-cat-parent';
			}
			$output .=  ' class="' . $class . '"';
			$output .= ">$link\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}

	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Not used.
	 * @param int $depth Depth of category. Not used.
	 * @param array $args Only uses 'list' for whether should append to output.
	 */
	function end_el( &$output, $page, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] )
			return;

		$output .= "</li>\n";
	}

}

/*
 * Functions for assembling CTS_Widget_Activity_Feed
 */
function bp_cts_list_activities($args){
    $defaults=array(
            'per_page'=>10,
            'page'=>1,
            'scope'=>'',
            'max'=>20,
            'show_avatar'=>"yes",
            'show_filters'=>"yes",
            'included'=>false,
            'excluded'=>false,
            'is_personal'=>"no",
            'is_blog_admin_activity'=>"no",
            'show_post_form'=>"no");
    
    $args=wp_parse_args($args, $defaults);
    extract($args);
    
//check for the scope of activity
//is it the activity of logged in user/blog admin
//logged in user over rides blog admin
     global $bp;
     $primary_id='';
     
     if(function_exists('bp_is_group')&&bp_is_group ())
         $primary_id=null;
     
     $user_id=false;//for limiting to users

     if($is_personal=="yes")
        $user_id=  bp_loggedin_user_id();
    else if($is_blog_admin_activity=="yes")
        $user_id=swa_get_blog_admin_id();
    else if(bp_is_user())
        $user_id=null;
    
    $components_scope=swa_get_base_component_scope($included,$excluded);

    $components_base_scope="";

    if(!empty($components_scope))
        $components_base_scope=join(",",$components_scope);

   ?>
      <div class='swa-wrap'>
          <?php if(is_user_logged_in()&&$show_post_form=="yes")
                swa_show_post_form();
          ?>
          
          <?php if($show_filters=="yes"):?>
			<ul id="activity-filter-links">
				<?php cts_activity_filter_links("scope=".$scope."&include=".$included."&exclude=".$excluded) ?>
			</ul>
                        <div class="clear"></div>
          <?php endif;?>
        
          <?php if ( bp_has_activities( 'type=sitewide&max=' . $max . '&page='.$page.'&per_page=' .$per_page.'&object='.$scope."&user_id=".$user_id."&primary_id=".$primary_id ) ) : ?>
				
				<!--
                <div class="swa-pagination ">
                        <div class="pag-count" id="activity-count">
                                <?php bp_activity_pagination_count() ?>
                        </div>

                        <div class="pagination-links" id="activity-pag">
                                &nbsp; <?php bp_activity_pagination_links() ?>
                        </div>
                    <div class="clear" ></div>
                </div>
				-->

                <div class="clear" ></div>
                
                <ul  class="site-wide-stream swa-activity-list">
                    <?php $count = 0; ?>
					<?php while ( bp_activities() ) : bp_the_activity(); ?>
                        <?php 
							cts_activity_entry($args, $count);
							$count++;
						?>
                    <?php endwhile; ?>
               </ul>

	<?php else: ?>

                <div class="widget-error">
                    <?php if($is_personal=="yes")
                        $error=sprintf(__("You have no recent %s activity.","swa"),$scope);
                        else
                            $error=__('There has been no recent site activity.', 'swa');
                        ?>
                        <?php echo $error; ?>
                </div>
	<?php endif;?>
     </div>
     
<?php
}

//individual entry in the activity stream
function cts_activity_entry($args, $count){
    extract($args);
    $allow_comment=false;//for now, avoid commenting
    ?>
 <?php do_action( 'bp_before_activity_entry' ) ?>
	<li class="<?php bp_activity_css_class() ?> <?php echo (($count % 2) === 0) ? "even" : "odd"; ?>" id="activity-<?php bp_activity_id() ?>">
		<?php if($show_avatar=="yes"):?>
            <div class="swa-activity-avatar">
				<a href="<?php bp_activity_user_link() ?>">
					<?php bp_activity_avatar( 'type=thumb&width=50&height=50' ) ?>
				</a>
			</div>
		<?php endif;?>
          <div class="swa-activity-content">
		<div class="swa-activity-header">
			<?php bp_activity_action() ?>
		</div>

		<?php if ( bp_activity_has_content()&&$show_activity_content ) : ?>
			<div class="swa-activity-inner">
				<?php bp_activity_content_body() ?>
			</div>
		<?php endif; ?>

	<?php do_action( 'bp_activity_entry_content' ) ?>
	<div class="swa-activity-meta">
            <?php if ( is_user_logged_in() && bp_activity_can_comment()&&$allow_comment ) : ?>
				<a href="<?php bp_activity_comment_link() ?>" class="acomment-reply" id="acomment-comment-<?php bp_activity_id() ?>"><?php _e( 'Reply', 'buddypress' ) ?> (<span><?php bp_activity_comment_count() ?></span>)</a>
            <?php endif; ?>
           
            <?php do_action( 'bp_activity_entry_meta' ) ?>
        </div>
	<div class="clear" ></div>
    </div>
    <?php if ( 'activity_comment' == bp_get_activity_type() ) : ?>
	<div class="swa-activity-inreplyto">
            <strong><?php _e( 'In reply to', 'swa' ) ?></strong> - <?php bp_activity_parent_content() ?> &middot;
            <a href="<?php bp_activity_thread_permalink() ?>" class="view" title="<?php _e( 'View Thread / Permalink', 'swa' ) ?>"><?php _e( 'View', 'swa' ) ?></a>
	</div>
    <?php endif; ?>
    <?php if ( bp_activity_can_comment()&&$show_activity_content ) : 
        
    if(!$allow_comment){
        //hide reply link
        add_filter('bp_activity_can_comment_reply','__return_false');
    }
?>
        <div class="swa-activity-comments">
        	<?php bp_activity_comments() ?>
            <?php if ( is_user_logged_in()&&$allow_comment ) : ?>
			<form action="<?php bp_activity_comment_form_action() ?>" method="post" id="swa-ac-form-<?php bp_activity_id() ?>" class="swa-ac-form"<?php bp_activity_comment_form_nojs_display() ?>>
				<div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ) ?></div>
				<div class="ac-reply-content">
					<div class="ac-textarea">
						<textarea id="swa-ac-input-<?php bp_activity_id() ?>" class="ac-input" name="ac_input_<?php bp_activity_id() ?>"></textarea>
					</div>
					<input type="submit" name="swa_ac_form_submit" value="<?php _e( 'Post', 'buddypress' ) ?> &rarr;" /> &nbsp; <?php _e( 'or press esc to cancel.', 'buddypress' ) ?>
					<input type="hidden" name="comment_form_id" value="<?php bp_activity_id() ?>" />
				</div>
				<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ) ?>
			</form>
			<?php endif; ?>
	</div>
        <?php if(!$allow_comment){
            //remove filter
            remove_filter('bp_activity_can_comment_reply','__return_false');
        }?>
    <?php endif; ?>
</li>
<?php do_action( 'bp_after_swa_activity_entry' ) ?>

<?php
}



/** Fix error for implode issue*/
//compat with filter links, will remove when bp adds it

function cts_activity_filter_links( $args = false ) {//copy of bp_activity_filter_link
	echo cts_get_activity_filter_links( $args );
}
	function cts_get_activity_filter_links( $args = false ) {
		global $activities_template, $bp;
                
              
                $link='';
		$defaults = array(
			'style' => 'list'
		);
            //check scope, if not single entiry

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$components=swa_get_base_component_scope($include,$exclude);
                 
                if ( !$components )
			return false;
                 
		foreach ( (array) $components as $component ) {
			/* Skip the activity comment filter */
			if ( 'activity' == $component )
				continue;

			if ( isset( $_GET['afilter'] ) && $component == $_GET['afilter'] )
				$selected = ' class="selected"';
			else
				$selected='';

			$component = esc_attr( $component );
                        if($component=='xprofile')
                            $component='profile';
                        
			switch ( $style ) {
				case 'list':
					$tag = 'li';
					$before = '<li id="afilter-' . $component . '"' . $selected . '>';
					$after = '</li>';
				break;
				case 'paragraph':
					$tag = 'p';
					$before = '<p id="afilter-' . $component . '"' . $selected . '>';
					$after = '</p>';
				break;
				case 'span':
					$tag = 'span';
					$before = '<span id="afilter-' . $component . '"' . $selected . '>';
					$after = '</span>';
				break;
			}

			$link = add_query_arg( 'afilter', $component );
			$link = remove_query_arg( 'acpage' , $link );

			$link = apply_filters( 'bp_get_activity_filter_link_href', $link, $component );

			/* Make sure all core internal component names are translatable */
			$translatable_components = array( __( 'profile', 'swa'), __( 'friends', 'swa' ), __( 'groups', 'swa' ), __( 'status', 'swa' ), __( 'blogs', 'swa' ) );

			$component_links[] = $before . '<a href="' . esc_attr( $link ) . '">' . ucwords( __( $component, 'swa' ) ) . '</a>' . $after;
		}

		$link = remove_query_arg( 'afilter' , $link );

		
                 
                     if ( !empty( $_REQUEST['scope'] ) ){
                        $link .= "?afilter=";
        			$component_links[] = '<' . $tag . ' id="afilter-clear"><a href="' . esc_attr( $link ) . '"">' . __( 'Clear Filter', 'swa' ) . '</a></' . $tag . '>';
                     }

                     if(!empty($component_links))
                        return apply_filters( 'swa_get_activity_filter_links', implode( "\n", $component_links ),$component_links );
               
                 return false;
	}

?>