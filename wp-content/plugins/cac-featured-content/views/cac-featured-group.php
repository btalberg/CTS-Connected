<?php
/**
 * This file is responsible for displaying a featured group on the front end of 
 * the site. It is required by the core view template whenever the 
 * featured content type has been set to 'group'
 *
 * @author Dominic Giglio
 *
 */

?>

<?php // echo out the widget title using the element selected in the widget admin
  echo "<{$cfcw_view->title_element} class='widgettitle'>";
  esc_html_e( $cfcw_view->title );
  echo "</{$cfcw_view->title_element}>";
?>

<div class="cfcw-content">
  <?php
    if ( $cfcw_view->display_images ) {
      if ( $cfcw_view->image_url )
        echo $cfcw_view->image_url;
      else
        echo $cfcw_view->avatar;
    }
  ?>

  <p class="cfcw-title">
    <a href="<?php echo esc_url( $cfcw_view->group->permalink ) ?>"><?php esc_html_e( $cfcw_view->group->name ) ?></a>
  </p>

  <p class="cfcw-description"><?php echo $cfcw_view->group->description ?></p>

  <?php if ( $cfcw_view->read_more ) : ?>
    <p class="more">
      <?php _e( 'Status:', 'cac-featured-content' ) ?> <?php esc_html_e( $cfcw_view->group->status ) ?> | <?php esc_html_e( $cfcw_view->group->total_member_count ) ?> <?php _e( 'Members', 'cac-featured-content' ) ?>
    </p>
  <?php endif ?>
</div>
