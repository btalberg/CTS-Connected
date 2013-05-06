<?php

do_action( 'bp_before_group_header' );

?>

<div id="item-header-containter">
	<div class="top group-meta-data group-top">
		<div class="group-type"><?php bp_group_type(); ?></div>
		<div class="group-activity info-box cts-button unclickable"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></div>
	</div>
	
	<div class="group-header">
		<div class="title"> <?php bp_group_name(); ?> </div>

		<?php do_action( 'bp_before_group_header_meta' ); ?>

		<div id="item-meta">

			<?php bp_group_description(); ?>

			<div id="item-buttons">

				<?php do_action( 'bp_group_header_actions' ); ?>

			</div><!-- #item-buttons -->

			<?php do_action( 'bp_group_header_meta' ); ?>

		</div>
	</div>
</div><!-- #item-header-content -->

<?php
do_action( 'bp_after_group_header' );
do_action( 'template_notices' );
?>