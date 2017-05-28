<?php
/**
 * The default post_content field output template.
 *
 * @since future
 */
$display_value = $gravityview->display_value;
$entry = $gravityview->entry->as_entry();
$field_settings = $gravityview->field->as_configuration();

if ( ! empty( $field_settings['dynamic_data'] ) && ! empty( $entry['post_id'] ) ) {

	global $post, $wp_query;

	/** Backup! */
	$_the_post = $post;

	$post = get_post( $entry['post_id'] );

	if ( empty( $post ) ) {
		do_action( 'gravityview_log_debug', 'Dynamic data for post #' . $entry['post_id'] . ' doesnt exist.' );
		$post = $_the_post;
		return;
	}

	setup_postdata( $post );
	$_in_the_loop = $wp_query->in_the_loop;
	$wp_query->in_the_loop = false;
	the_content(); /** Prevent the old the_content filter from running. @todo Remove this hack along with the old filter. */
	$wp_query->in_the_loop = $_in_the_loop;
	wp_reset_postdata();

	/** Restore! */
	$post = $_the_post;

} else {
	echo $display_value;
}
