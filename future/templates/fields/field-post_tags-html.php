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

	$term_list = gravityview_get_the_term_list( $entry['post_id'], $field_settings['link_to_term'] );

	if ( empty( $term_list ) ) {
		do_action( 'gravityview_log_debug', 'Dynamic data for post #' . $entry['post_id'] . ' doesnt exist.' );
	}

	echo $term_list;

} else {

	if ( empty( $field_settings['link_to_term'] ) ) {

		echo esc_html( $display_value );

	} else {

		echo gravityview_convert_value_to_term_list( $display_value );
	}
}
