<?php echo do_shortcode( $before_form ); ?>
<?php

if ( empty( $form_type ) || 'default' === $form_type ) {
	$args = array();
	if ( ! empty( $hide_remember_me ) ) {
		$args['remember'] = false;
	}
	wp_login_form( $args );
} else {
	echo do_shortcode( '[ultimatemember form_id=' . absint( $form_type ) . ']' );
}
?>
<?php
echo do_shortcode( $after_form );
