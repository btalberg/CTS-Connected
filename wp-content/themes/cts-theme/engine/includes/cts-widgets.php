<?php
/**
 * Recent_Posts CTS widget class
 *
 * @since 2.8.0
 */

include_once( 'cts-widgets-templates.php' );

class CTS_Widget_Recent_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_entries', 'description' => __( "The most recent posts") );
		parent::__construct('recent-posts', __('Recent Posts'), $widget_ops);
		$this->alt_option_name = 'widget_recent_entries';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
			<?php if ( $show_date ) : ?>
				<span class="post-date"><?php echo get_the_date(); ?></span>
			<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (bool) $new_instance['show_date'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_posts', 'widget');
	}

	function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
<?php
	}
}

class CTS_Widget_Categories extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_categories', 'description' => __( "A list or dropdown of categories." ) );
		parent::__construct('cts-categories', __('CTS Categories'), $widget_ops);
	}

	function widget( $args, $instance ) {
		
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'], $instance, $this->id_base);
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$columns = empty( $instance['columns'] ) ? 1 : $instance['columns'];
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('orderby' => 'name', 'show_count' => $c);
?>
		<ul class="dynamic-columns <?php echo convert_list_column_number_to_string($columns) ?>">
<?php
		$cat_args['title_li'] = '';
		cts_list_categories(apply_filters('widget_categories_args', $cat_args));
?>
		</ul>
<?php

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['columns'] = (int) $new_instance['columns'];
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$columns    = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 1;
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e( 'Number of columns:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" type="text" value="<?php echo $columns; ?>" size="3" /></p>
		
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />

<?php
	}

}

class CTS_Widget_Groups extends WP_Widget {
	
	function cts_widget_groups() {
		$this->_construct();
	}

	function __construct() {
		$widget_ops = array( 'description' => __( 'A dynamic list of recently active, popular, and newest groups', 'cts' ) );
		parent::__construct( false, __( 'CTS Groups', 'cts' ), $widget_ops );

		if ( is_active_widget( false, false, $this->id_base ) && !is_admin() && !is_network_admin() ) {
			wp_enqueue_script( 'cts_groups_widget_groups_list-js', INFINITY_THEME_PATH . 'assets/js/cts-widget-groups.js', array( 'jquery' ), bp_get_version() );
		}
	}

	function widget( $args, $instance ) {
		$user_id = apply_filters( 'bp_group_widget_user_id', '0' );

		extract( $args );

		if ( empty( $instance['group_default'] ) )
			$instance['group_default'] = 'popular';

		if ( empty( $instance['title'] ) )
			$instance['title'] = __( 'Groups', 'cts' );

		echo $before_widget;
		
		$title = $instance['link_title'] ? '<a href="' . trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ) . '">' . $instance['title'] . '</a>' : $instance['title'];
		
		echo $before_title
		   . $title
		   . $after_title; ?>

		<?php if ( bp_has_groups( 'user_id=' . $user_id . '&type=' . $instance['group_default'] . '&max=' . $instance['max_groups'] . '&avatar=' . $instance['show_avatar'] ) ) : ?>
			<div class="item-options" id="cts-groups-list-options">
				<a href="<?php echo site_url( bp_get_groups_root_slug() ); ?>" id="newest-groups"<?php if ( $instance['group_default'] == 'newest' ) : ?> class="selected"<?php endif; ?>><?php _e("Newest", 'cts') ?></a> |
				<a href="<?php echo site_url( bp_get_groups_root_slug() ); ?>" id="recently-active-groups"<?php if ( $instance['group_default'] == 'active' ) : ?> class="selected"<?php endif; ?>><?php _e("Active", 'cts') ?></a> |
				<a href="<?php echo site_url( bp_get_groups_root_slug() ); ?>" id="popular-groups" <?php if ( $instance['group_default'] == 'popular' ) : ?> class="selected"<?php endif; ?>><?php _e("Popular", 'cts') ?></a>
			</div>

			<ul id="groups-list" class="dynamic-columns single" ">
				<?php while ( bp_groups() ) : bp_the_group(); ?>
					<li>
						<?php if ( $instance['show_avatar'] ) { ?>
							<div class="item-avatar">
								<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_avatar_thumb() ?></a>
							</div>
						<?php } ?>

						<div>
							<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a>
							<div id="group-meta">
								<span class="activity">
								<?php
									if ( 'newest' == $instance['group_default'] )
										printf( __( 'created %s', 'cts' ), bp_get_group_date_created() );
									if ( 'active' == $instance['group_default'] )
										printf( __( 'active %s', 'cts' ), bp_get_group_last_active() );
									else if ( 'popular' == $instance['group_default'] )
										bp_group_member_count();
								?>
								</span>
							</div>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'cts_groups_widget_groups_list', '_wpnonce-groups' ); ?>
			<input type="hidden" name="groups_widget_max" id="groups_widget_max" value="<?php echo esc_attr( $instance['max_groups'] ); ?>" />
			<input type="hidden" name="groups_widget_avatar" id="groups_widget_avatar" value="<?php echo (bool)$instance['show_avatar'] ? 'true' : 'false'; ?>" />

		<?php else: ?>

			<div class="widget-error">
				<?php _e('There are no groups to display.', 'cts') ?>
			</div>

		<?php endif; ?>

		<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['max_groups']    = strip_tags( $new_instance['max_groups'] );
		$instance['group_default'] = strip_tags( $new_instance['group_default'] );
		$instance['link_title']    = (bool)$new_instance['link_title'];
		$instance['show_avatar']    = (bool)$new_instance['show_avatar'];
		
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'         => __( 'Groups', 'cts' ),
			'max_groups'    => 5,
			'group_default' => 'active',
			'link_title'    => false,
			'show_avatar'	=> false
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title 	       = strip_tags( $instance['title'] );
		$max_groups    = strip_tags( $instance['max_groups'] );
		$group_default = strip_tags( $instance['group_default'] );
		$link_title    = (bool)$instance['link_title'];
		$show_avatar    = (bool)$instance['show_avatar'];
		?>

		<p><label for="bp-groups-widget-title"><?php _e('Title:', 'cts'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>
		
		<p><label for="<?php echo $this->get_field_name('link_title') ?>"><input type="checkbox" name="<?php echo $this->get_field_name('link_title') ?>" value="1" <?php checked( $link_title ) ?> /> <?php _e( 'Link widget title to Groups directory', 'cts' ) ?></label></p>
		
		<p><label for="<?php echo $this->get_field_name('show_avatar') ?>"><input type="checkbox" name="<?php echo $this->get_field_name('show_avatar') ?>" value="1" <?php checked( $show_avatar ) ?> /> <?php _e( 'Show avatar', 'cts' ) ?></label></p>
		
		<p><label for="bp-groups-widget-groups-max"><?php _e('Max groups to show:', 'cts'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_groups' ); ?>" name="<?php echo $this->get_field_name( 'max_groups' ); ?>" type="text" value="<?php echo esc_attr( $max_groups ); ?>" style="width: 30%" /></label></p>

		<p>
			<label for="bp-groups-widget-groups-default"><?php _e('Default groups to show:', 'buddypress'); ?>
			<select name="<?php echo $this->get_field_name( 'group_default' ); ?>">
				<option value="newest" <?php if ( $group_default == 'newest' ) : ?>selected="selected"<?php endif; ?>><?php _e( 'Newest', 'buddypress' ) ?></option>
				<option value="active" <?php if ( $group_default == 'active' ) : ?>selected="selected"<?php endif; ?>><?php _e( 'Active', 'buddypress' ) ?></option>
				<option value="popular"  <?php if ( $group_default == 'popular' ) : ?>selected="selected"<?php endif; ?>><?php _e( 'Popular', 'buddypress' ) ?></option>
			</select>
			</label>
		</p>
	<?php
	}
}

function cts_groups_ajax_widget_groups_list() {
	
	check_ajax_referer('cts_groups_widget_groups_list');

	switch ( $_POST['filter'] ) {
		case 'newest-groups':
			$type = 'newest';
		break;
		case 'recently-active-groups':
			$type = 'active';
		break;
		case 'popular-groups':
			$type = 'popular';
		break;
	}

	if ( bp_has_groups( 'type=' . $type . '&per_page=' . $_POST['max_groups'] . '&max=' . $_POST['max_groups'] . '&avatar=' . $instance['show_avatar'] ) ) : ?>
		<?php echo "0[[SPLIT]]"; ?>

		<?php while ( bp_groups() ) : bp_the_group(); ?>
			<li>
				<?php _log( "test" ); _log($_POST['show_avatar']); if ( $_POST['show_avatar'] === 'true' ) { ?>
					<div class="item-avatar">
						<a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar_thumb() ?></a>
					</div>
				<?php } ?>	

				<div>
					<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a>
					<div id="group-meta">
						<span class="activity">
							<?php
							if ( 'newest-groups' == $_POST['filter'] ) {
								printf( __( 'created %s', 'cts' ), bp_get_group_date_created() );
							} else if ( 'recently-active-groups' == $_POST['filter'] ) {
								printf( __( 'active %s', 'cts' ), bp_get_group_last_active() );
							} else if ( 'popular-groups' == $_POST['filter'] ) {
								bp_group_member_count();
							}
							?>
						</span>
					</div>
				</div>
			</li>

		<?php endwhile; ?>
			
		<?php wp_nonce_field( 'cts_groups_widget_groups_list', '_wpnonce-groups' ); ?>
		<input type="hidden" name="groups_widget_max" id="groups_widget_max" value="<?php echo esc_attr( $_POST['max_groups'] ); ?>" />
		<input type="hidden" name="groups_widget_avatar" id="groups_widget_avatar" value="<?php echo esc_attr( $_POST['show_avatar'] ); ?>" />
	<?php else: ?>

		<?php echo "-1[[SPLIT]]<li>" . __("No groups matched the current filter.", 'cts'); ?>

	<?php endif;

}
add_action( 'wp_ajax_cts_widget_groups_list', 'cts_groups_ajax_widget_groups_list' );
add_action( 'wp_ajax_nopriv_cts_widget_groups_list', 'cts_groups_ajax_widget_groups_list' );

class CTS_Widget_Events extends WP_Widget {
	
	var $defaults;
	
    /** constructor */
    function cts_widget_events() {
    	$this->defaults = array(
    		'title' => __('CTS Events','cts'),
    		'scope' => 'future',
    		'order' => 'ASC',
    		'limit' => 5,
    		'category' => 0,
    		'format' => '#_EVENTLINK<ul><li>#j #M #y</li><li>#_LOCATIONTOWN</li></ul>',
    		'nolistwrap' => false,
    		'orderby' => 'event_start_date,event_start_time,event_name',
			'all_events' => 0,
			'all_events_text' => __('all events', 'cts'),
			'no_events_text' => __('No events', 'cts')
    	);
		$this->em_orderby_options = apply_filters('em_settings_events_default_orderby_ddm', array(
			'event_start_date,event_start_time,event_name' => __('start date, start time, event name','cts'),
			'event_name,event_start_date,event_start_time' => __('name, start date, start time','cts'),
			'event_name,event_end_date,event_end_time' => __('name, end date, end time','cts'),
			'event_end_date,event_end_time,event_name' => __('end date, end time, event name','cts'),
		)); 
    	$widget_ops = array('description' => __( "Display a list of events on Events Manager.", 'cts') );
        parent::WP_Widget(false, $name = 'CTS Events', $widget_ops);	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	$instance = array_merge($this->defaults, $instance);
    	$instance = $this->fix_scope($instance); // depcreciate	

    	echo $args['before_widget'];
    	if( !empty($instance['title']) ){
		    echo $args['before_title'];
		    echo apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
		    echo $args['after_title'];
    	}
    	
		$instance['owner'] = false;
		//orderby fix for previous versions with old orderby values
		if( !array_key_exists($instance['orderby'], $this->em_orderby_options) ){
			//replace old values
			$old_vals = array(
				'name' => 'event_name',
				'end_date' => 'event_end_date',
				'start_date' => 'event_start_date',
				'end_time' => 'event_end_time',
				'start_time' => 'event_start_time'
			);
			foreach($old_vals as $old_val => $new_val){
				$instance['orderby'] = str_replace($old_val, $new_val, $instance['orderby']);
			}
		}
		
		$events = EM_Events::get(apply_filters('em_widget_events_get_args',$instance));
		echo "<ul class=\"dynamic-columns single\">";
		$li_wrap = !preg_match('/^<li>/i', trim($instance['format']));
		if ( count($events) > 0 ){
			foreach($events as $event){				
				if( $li_wrap ){
					echo '<li>'. $event->output($instance['format']) .'</li>';
				}else{
					echo $event->output($instance['format']);
				}
			}
		}else{
			echo '<li>'.$instance['no_events_text'].'</li>';
		}
		if ( !empty($instance['all_events']) ){
			$events_link = (!empty($instance['all_events_text'])) ? em_get_link($instance['all_events_text']) : em_get_link(__('all events','dbem'));
			echo '<li class="all-events-link">'.$events_link.'</li>';
		}
		echo "</ul>";
		
	    echo $args['after_widget'];
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	foreach($this->defaults as $key => $value){
    		if( !isset($new_instance[$key]) ){
    			$new_instance[$key] = $value;
    		}
    	}
    	return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    	$instance = array_merge($this->defaults, $instance);
    	$instance = $this->fix_scope($instance); // depcreciate
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'dbem'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of events','dbem'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" size="3" value="<?php echo esc_attr($instance['limit']); ?>" />
		</p>
		<p>
			
			<label for="<?php echo $this->get_field_id('scope'); ?>"><?php _e('Scope','dbem'); ?>: </label><br/>
			<select id="<?php echo $this->get_field_id('scope'); ?>" name="<?php echo $this->get_field_name('scope'); ?>" >
				<?php foreach( em_get_scopes() as $key => $value) : ?>   
				<option value='<?php echo $key ?>' <?php echo ($key == $instance['scope']) ? "selected='selected'" : ''; ?>>
					<?php echo $value; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order By','dbem'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
				<?php  
					echo $this->em_orderby_options;
				?>
				<?php foreach($this->em_orderby_options as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['orderby']) && $key == $instance['orderby']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order','dbem'); ?>: </label>
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<?php 
				$order_options = apply_filters('em_widget_order_ddm', array(
					'ASC' => __('Ascending','dbem'),
					'DESC' => __('Descending','dbem')
				)); 
				?>
				<?php foreach( $order_options as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ($key == $instance['order']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category IDs','dbem'); ?>: </label>
            <input type="text" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" size="3" value="<?php echo esc_attr($instance['category']); ?>" /><br />
            <em><?php _e('1,2,3 or 2 (0 = all)','dbem'); ?> </em>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php _e('List item format','dbem'); ?>: </label>
			<textarea rows="5" cols="24" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>"><?php echo $instance['format']; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('all_events'); ?>"><?php _e('Show all events link at bottom?','dbem'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('all_events'); ?>" name="<?php echo $this->get_field_name('all_events'); ?>" <?php echo (!empty($instance['all_events']) && $instance['all_events']) ? 'checked':''; ?> >
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('all_events'); ?>"><?php _e('All events link text?','dbem'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('all_events_text'); ?>" name="<?php echo $this->get_field_name('all_events_text'); ?>" value="<?php echo (!empty($instance['all_events_text'])) ? $instance['all_events_text']:__('all events','dbem'); ?>" >
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('no_events_text'); ?>"><?php _e('No events text','dbem'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('no_events_text'); ?>" name="<?php echo $this->get_field_name('no_events_text'); ?>" value="<?php echo (!empty($instance['no_events_text'])) ? $instance['no_events_text']:__('No events', 'dbem'); ?>" >
		</p>
        <?php 
    }
    
    /**
     * Backwards compatability for an old setting which is now just another scope.
     * @param unknown_type $instance
     * @return string
     */
    function fix_scope($instance){
    	if( !empty($instance['time_limit']) && is_numeric($instance['time_limit']) && $instance['time_limit'] > 1 ){
    		$instance['scope'] = $instance['time_limit'].'-months';
    	}elseif( !empty($instance['time_limit']) && $instance['time_limit'] == 1){
    		$instance['scope'] = 'month';
    	}elseif( !empty($instance['time_limit']) && $instance['time_limit'] == 'no-limit'){
    		$instance['scope'] = 'all';
    	}
    	return $instance;
    }
}

class CTS_Widget_Activity_Feed extends WP_Widget {
	function __construct() {
		parent::__construct( false, $name = __( 'CTS Site Wide Activity', 'cts' ) );
	}

	function widget($args, $instance) {
		global $bp;
                if($instance['is_personal']=='yes'&&!is_user_logged_in())
                    return;//do  not show anything
		extract( $args );
                $included_components=$instance["included_components"];
                $excluded_components=$instance["excluded_components"];
                if(empty($included_components))
                    $included_components=BP_Activity_Activity::get_recorded_components();
                
                //let us assume that the scope is selected components
                $scope=$included_components;
                
                //if the user has excluded some of the components , let us remove it from scope
                if(!empty($scope)&&is_array($excluded_components))
                    $scope=array_diff($scope,$excluded_components);
                
                //ok, now we will create a comma separated list
                if(!empty($scope))
                    $scope=join(",",$scope);
                

                if(!empty ($included_components)&&  is_array($included_components))
                    $included_components=join(",",$included_components);
                
                 if(!empty ($excluded_components)&&  is_array($excluded_components))
                    $excluded_components=join(",",$excluded_components);
                 
                 //find scope
                 

		echo $before_widget;
		echo $before_title
		   . $instance['title'] ;
                if($instance['show_feed_link']=="yes")
		echo	 ' <a class="swa-rss" href="' . bp_get_sitewide_activity_feed_link() . '" title="' . __( 'Site Wide Activity RSS Feed', 'cts' ) . '">' . __( '[RSS]', 'cts' ) . '</a>';
		echo    $after_title;
		 
                $args=$instance;
                $args['page']=1;
                $args['scope']=$scope;
                $args['max']=$instance['max_items'];
                $args['show_filters']=$instance["show_activity_filters"];
                $args['included']=$included_components;
                $args['excluded']=$excluded_components;
               //is_personal, is_blog_admin activity etc are set in the  
                
                   bp_cts_list_activities($args);
		  ?>
		<input type='hidden' name='max' id='swa_max_items' value="<?php echo  $instance['max_items'];?>" />  
		<input type='hidden' name='max' id='swa_per_page' value="<?php echo  $instance['per_page'];?>" />  
		<input type='hidden' name='show_avatar' id='swa_show_avatar' value="<?php echo  $instance['show_avatar'];?>" />
		<input type='hidden' name='show_filters' id='swa_show_filters' value="<?php echo  $instance['show_activity_filters'];?>" />
		<input type='hidden' name='included_components' id='swa_included_components' value="<?php echo  $included_components;?>" />
		<input type='hidden' name='excluded_components' id='swa_excluded_components' value="<?php echo  $excluded_components;?>" />
		<input type='hidden' name='is_personal' id='swa_is_personal' value="<?php echo  $instance['is_personal'];?>" />
		<input type='hidden' name='is_blog_admin_activity' id='swa_is_blog_admin_activity' value="<?php echo  $instance['is_blog_admin_activity'];?>" />
		<input type='hidden' name='show_post_form' id='swa_show_post_form' value="<?php echo  $instance['show_post_form'];?>" />

	<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_items'] = strip_tags( $new_instance['max_items'] );
		$instance['per_page'] = strip_tags( $new_instance['per_page'] );
		$instance['show_avatar'] =  $new_instance['show_avatar']; //avatar should be visible or not
		$instance['allow_reply'] = $new_instance['allow_reply']; //allow reply inside widget or not
		$instance['show_post_form'] = $new_instance['show_post_form']; //should we show the post form or not
		$instance['show_activity_filters'] =$new_instance['show_activity_filters'] ; //activity filters should be visible or not
		$instance['show_feed_link'] =  $new_instance['show_feed_link'] ; //feed link should be visible or not
                $instance["show_activity_content"]=$new_instance["show_activity_content"];
               
                $instance["included_components"]=$new_instance["included_components"];
                $instance["excluded_components"]=$new_instance["excluded_components"];
                $instance["is_blog_admin_activity"]=$new_instance["is_blog_admin_activity"];
                $instance["is_personal"]=$new_instance["is_personal"];
                  

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title'=>__('Site Wide Activities','cts'),'max_items' => 200, 'per_page' => 25,'is_personal'=>'no','is_blog_admin_activity'=>'no','show_avatar'=>'yes','show_activity_content'=>1,'show_feed_link'=>'yes','show_post_form'=>'no','allow_reply'=>'no','show_activity_filters'=>'yes','included_components'=>false,'excluded_components'=>false,'allow_comment' ) );
		$per_page = strip_tags( $instance['per_page'] );
		$max_items = strip_tags( $instance['max_items'] );
		$title = strip_tags( $instance['title'] );
                extract($instance);
              
		?>
                <div class="swa-widgte-block">
                <p><label for="bp-swa-title"><strong><?php _e('Title:', 'cts'); ?> </strong><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>
		<p><label for="bp-swa-per-page"><?php _e('Number of Items Per Page:', 'cts'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'per_page' ); ?>" name="<?php echo $this->get_field_name( 'per_page' ); ?>" type="text" value="<?php echo esc_attr( $per_page ); ?>" style="width: 30%" /></label></p>
		<p><label for="bp-swa-max"><?php _e('Max items to show:', 'cts'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_items' ); ?>" name="<?php echo $this->get_field_name( 'max_items' ); ?>" type="text" value="<?php echo esc_attr( $max_items ); ?>" style="width: 30%" /></label></p>
                </div>  
                <div class="swa-widgte-block">
                <p><label for="bp-swa-is-personal"><strong><?php _e("Limit to Logged In user's activity:", 'cts'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'is_personal' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'is_personal' ); ?>_yes" name="<?php echo $this->get_field_name( 'is_personal' ); ?>" type="radio" <?php if($is_personal=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'is_personal' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'is_personal' ); ?>_no" name="<?php echo $this->get_field_name( 'is_personal' ); ?>" type="radio" <?php if($is_personal!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
                <p><label for="bp-swa-is-blog-admin-activity"><strong><?php _e("List My Activity Only:", 'cts'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_yes" name="<?php echo $this->get_field_name( 'is_blog_admin_activity' ); ?>" type="radio" <?php if($is_blog_admin_activity=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_no" name="<?php echo $this->get_field_name( 'is_blog_admin_activity' ); ?>" type="radio" <?php if($is_blog_admin_activity!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
                  </div>
               <div class="swa-widgte-block">
                <p><label for="bp-swa-show-avatar"><strong><?php _e('Show Avatar:', 'cts'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_avatar' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" type="radio" <?php if($show_avatar=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_avatar' ); ?>_no" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" type="radio" <?php if($show_avatar!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>
                    
                    </label>
               </p>
               <p><label for="bp-swa-show-feed-link"><?php _e('Show Feed Link:', 'cts'); ?>
                       <label for="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_feed_link' ); ?>" type="radio" <?php if($show_feed_link=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_no" name="<?php echo $this->get_field_name( 'show_feed_link' ); ?>" type="radio" <?php if($show_feed_link!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
               <p><label for="bp-swa-show-activity-content"><?php _e('Show Activity Content:', 'cts'); ?>
                       <label for="<?php echo $this->get_field_id( 'show_activity_content' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_activity_content' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_activity_content' ); ?>" type="radio" <?php echo checked($show_activity_content,1) ?> value="1" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_activity_content' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_activity_content' ); ?>_no" name="<?php echo $this->get_field_name( 'show_activity_content' ); ?>" type="radio" <?php echo checked($show_activity_content,0) ?> value="0" style="width: 10%" />No</label>

                    </label>
               </p>
               <p><label for="bp-swa-show-post-form"><strong><?php _e('Show Post Form', 'cts'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'show_post_form' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_post_form' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_post_form' ); ?>" type="radio" <?php if($show_post_form=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_post_form' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_post_form' ); ?>_no" name="<?php echo $this->get_field_name( 'show_post_form' ); ?>" type="radio" <?php if($show_post_form!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
               <!-- <p><label for="bp-swa-show-reply-link"><?php _e('Allow reply to activity item:', 'cts'); ?>
                       <label for="<?php echo $this->get_field_id( 'allow_reply' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'allow_reply' ); ?>_yes" name="<?php echo $this->get_field_name( 'allow_reply' ); ?>" type="radio" <?php if($show_feed_link=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'allow_reply' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'allow_reply' ); ?>_no" name="<?php echo $this->get_field_name( 'allow_reply' ); ?>" type="radio" <?php if($show_feed_link!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>-->
               <p><label for="bp-swa-show-activity-filters"><strong><?php _e('Show Activity Filters:', 'cts'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_activity_filters' ); ?>" type="radio" <?php if($show_activity_filters=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_no" name="<?php echo $this->get_field_name( 'show_activity_filters' ); ?>" type="radio" <?php if($show_activity_filters!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
               
               </div>
               <div class="swa-widgte-block">
               <p><label for="bp-swa-included-filters"><strong><?php _e('Include only following Filters:', 'cts'); ?></strong></label></p>
                 <p>     <?php $recorded_components=BP_Activity_Activity::get_recorded_components();
                      foreach((array)$recorded_components as $component):?>
                         <label for="<?php echo $this->get_field_id( 'included_components' ).'_'.$component ?>" ><?php echo ucwords($component);?> <input id="<?php echo $this->get_field_id( 'included_components' ).'_'.$component ?>" name="<?php echo $this->get_field_name( 'included_components' ); ?>[]" type="checkbox" <?php if(is_array($included_components)&&in_array($component, $included_components)) echo "checked='checked'";?> value="<?php echo $component;?>" style="width: 10%" /></label>
                       <?php endforeach;?>
                   
               </p>
               </div>
               <div class="swa-widgte-block">
                   
              <p><label for="bp-swa-included-filters"><strong><?php _e('Exclude following Components activity', 'cts'); ?></strong></label></p>
                 <p>     <?php $recorded_components=BP_Activity_Activity::get_recorded_components();
                      foreach((array)$recorded_components as $component):?>
                         <label for="<?php echo $this->get_field_id( 'excluded_components' ).'_'.$component ?>" ><?php echo ucwords($component);?> <input id="<?php echo $this->get_field_id( 'excluded_components' ).'_'.$component ?>" name="<?php echo $this->get_field_name( 'excluded_components' ); ?>[]" type="checkbox" <?php if(is_array($excluded_components)&&in_array($component, $excluded_components)) echo "checked='checked'";?> value="<?php echo $component;?>" style="width: 10%" /></label>
                       <?php endforeach;?>

               </p>
               </div>
	<?php
	}
}

/**
 * Register all of the default WordPress widgets on startup.
 *
 * Calls 'widgets_init' action after all of the WordPress widgets have been
 * registered.
 *
 * @since 2.2.0
 */
function cts_widgets_init() {
	if ( !is_blog_installed() )
		return;

	unregister_widget('WP_Widget_Pages');

	unregister_widget('WP_Widget_Calendar');

	unregister_widget('WP_Widget_Archives');

	if ( get_option( 'link_manager_enabled' ) )
		unregister_widget('WP_Widget_Links');

	unregister_widget('WP_Widget_Meta');

	unregister_widget('WP_Widget_Search');

	unregister_widget('WP_Widget_Categories');

	unregister_widget('WP_Widget_Recent_Posts');

	unregister_widget('WP_Widget_Recent_Comments');

	unregister_widget('WP_Widget_RSS');

	unregister_widget('WP_Nav_Menu_Widget');
	
	register_widget('CTS_Widget_Recent_Posts');

	register_widget('CTS_Widget_Categories');
	
	register_widget('CTS_Widget_Groups');
	
	register_widget('CTS_Widget_Events');
	
	register_widget('CTS_Widget_Activity_Feed');
	
	do_action('cts_widgets_init');
}

add_action('widgets_init', 'cts_widgets_init');
