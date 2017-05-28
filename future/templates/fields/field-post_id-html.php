<?php
/**
 * The default post ID field output template.
 *
 * @since future
 */
$display_value = $gravityview->display_value;
$entry = $gravityview->entry->as_entry();

// Link to the post URL?
if ( $gravityview->field->link_to_post && ! empty( $entry['post_id'] ) ) {

	echo gravityview_get_link( get_permalink( $entry['post_id'] ), esc_html( $display_value ) );

} else {

	echo esc_html( $display_value );

}
