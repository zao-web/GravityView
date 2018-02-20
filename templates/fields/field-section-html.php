<?php
/**
 * The default form section output template.
 *
 * @global \GV\Template_Context $gravityview
 * @since future
 */
$field = $gravityview->field->field;
$form = $gravityview->view->form->form;
$entry = $gravityview->entry->as_entry();

if ( ! empty( $field['description'] ) ) {
	echo GravityView_API::replace_variables( $field['description'], $form, $entry );
}
