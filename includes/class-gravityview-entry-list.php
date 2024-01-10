<?php

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Generate linked list output for a list of entries.
 *
 * @since 1.7.2
 */
class GravityView_Entry_List {

	/**
	 * @var array
	 */
	private $entries = array();

	/**
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * @var array
	 */
	private $form = array();

	/**
	 * @var string
	 */
	private $link_format = '';

	/**
	 * HTML or text to display after the link to the entry
	 *
	 * @var string
	 */
	private $after_link = '';

	/**
	 * The message when there are no entries to display
	 *
	 * @var string
	 */
	private $empty_message = '';

	/**
	 * Whether to skip the entry currently being displayed, if any.
	 *
	 * @var bool
	 */
	private $skip_current_entry = true;

	/**
	 * Optional. Set the context for the output to allow for easier filtering output.
	 *
	 * @var string
	 */
	private $context = '';

	/**
	 * HTML tag to wrap output inside
	 *
	 * @var string
	 */
	private $wrapper_tag = 'ul';

	/**
	 * HTML tag to wrap each entry inside
	 *
	 * @var string
	 */
	private $item_tag = 'li';

	/**
	 * The context this list is operating in.
	 *
	 * @todo Deprecate this class altogether.
	 * @since 2.0
	 * @var \GV\Template_Context
	 */
	public $template_context;

	/**
	 * The ID of the View connected to the entries being displayed
	 *
	 * @since 2.7.2
	 * @var int
	 */
	public $view_id = 0;

	/**
	 * @since 2.0 Added $template_context parameter
	 * @since 2.7.2 Added $view_id parameter
	 *
	 * @param array|GV\Entry[]     $entries
	 * @param int                  $post_id
	 * @param array                $form
	 * @param string               $link_format
	 * @param string               $after_link
	 * @param \GV\Template_Context $template_context The context
	 * @param int|null             $view_id View to link to when displaying on a page with multiple Views
	 */
	function __construct( $entries = array(), $post_id = 0, $form = array(), $link_format = '', $after_link = '', $context = '', $template_context = null, $view_id = 0 ) {
		$this->entries          = $entries;
		$this->post_id          = $post_id;
		$this->form             = $form;
		$this->link_format      = $link_format;
		$this->after_link       = $after_link;
		$this->context          = $context;
		$this->template_context = $template_context;
		$this->view_id          = $view_id;
		$this->empty_message    = function_exists( 'gv_no_results' ) ? gv_no_results( $template_context ) : __( 'No entries match your request.', 'gk-gravityview' );
	}

	/**
	 * @param int $post_id
	 */
	public function set_post_id( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * @param string $link_format
	 */
	public function set_link_format( $link_format ) {
		$this->link_format = $link_format;
	}

	/**
	 * @param boolean $skip_current_entry
	 */
	public function set_skip_current_entry( $skip_current_entry ) {
		$this->skip_current_entry = (bool) $skip_current_entry;
	}

	/**
	 * @param string $after_link
	 */
	public function set_after_link( $after_link ) {
		$this->after_link = $after_link;
	}

	/**
	 * Set the message when there are no entries to display
	 *
	 * @param string $empty_message
	 */
	public function set_empty_message( $empty_message ) {
		$this->empty_message = $empty_message;
	}

	/**
	 * Set the context in which this entry list is being displayed.
	 *
	 * @param string $context
	 */
	public function set_context( $context ) {
		$this->context = $context;
	}

	/**
	 * @param string $wrapper_tag
	 */
	public function set_wrapper_tag( $wrapper_tag ) {
		$this->wrapper_tag = esc_attr( $wrapper_tag );
	}

	/**
	 *
	 * @param string $item_tag
	 */
	public function set_item_tag( $item_tag ) {
		$this->item_tag = esc_attr( $item_tag );
	}

	/**
	 * Echo the output generated by get_output()
	 *
	 * @see get_output()
	 *
	 * @return string HTML output for entry list
	 */
	public function output() {

		$output = $this->get_output();

		echo $output;

		return $output;
	}

	/**
	 * Get the HTML output
	 *
	 * @return string HTML output for entry list
	 */
	public function get_output() {

		// No Entries
		if ( empty( $this->entries ) ) {
			return '<div class="gv-no-results">' . $this->empty_message . '</div>';
		}

		$output = '';

		if ( $this->template_context instanceof \GV\Template_Context ) {
			$current_entry = $this->template_context->entry->as_entry();
		} else {
			$current_entry = GravityView_View::getInstance()->getCurrentEntry();
		}

		$output .= '<' . $this->wrapper_tag . '>';

		foreach ( $this->entries as $entry ) {

			if ( $entry instanceof \GV\Entry ) {
				$entry = $entry->as_entry();
			}

			if ( $this->skip_entry( $entry, $current_entry ) ) {
				continue;
			}

			$output .= $this->get_item_output( $entry );
		}

		$output .= '</' . $this->wrapper_tag . '>';

		/**
		 * Modify the HTML of the Recent Entries widget output.
		 * @param string $output HTML to be displayed
		 * @param GravityView_Entry_List $this The current class instance
		 */
		$output = apply_filters( 'gravityview/widget/recent-entries/output', $output, $this );

		return $output;
	}

	/**
	 * Should the current entry be skipped while showing the list of entries?
	 *
	 * @param array     $entry GF Entry array
	 * @param array|int $current_entry As returned by GravityView_View::getCurrentEntry()
	 *
	 * @return bool True: Skip entry; False: don't skip entry
	 */
	private function skip_entry( $entry, $current_entry ) {

		// If skip entry is off, or there's no current entry, return false
		if ( empty( $this->skip_current_entry ) || empty( $current_entry ) ) {
			return false;
		}

		// If in Single or Edit mode, $current_entry will be an array.
		$current_entry_id = is_array( $current_entry ) ? $current_entry['id'] : $current_entry;

		// If the entry ID matches the current entry, yes: skip
		if ( $entry['id'] === $current_entry_id ) {
			return true;
		}

		// Otherwise, return false
		return false;
	}

	/**
	 * Get the output for a specific entry
	 *
	 * @param array $entry GF Entry array
	 *
	 * @since 1.7.2
	 *
	 * @uses gravityview_get_link
	 * @uses GravityView_API::entry_link
	 * @uses GravityView_API::replace_variables
	 *
	 * @return string HTML output for the entry
	 */
	private function get_item_output( $entry ) {

		$link = GravityView_API::entry_link( $entry, $this->post_id, true, $this->view_id );

		/**
		 * The link to this other entry now.
		 * @param string $link The link.
		 * @param array $entry The entry.
		 * @param \GravityView_Entry_List $this The current entry list object.
		 */
		$link = apply_filters( 'gravityview/entry-list/link', $link, $entry, $this );

		$item_output = gravityview_get_link( $link, $this->link_format );

		if ( ! empty( $this->after_link ) ) {

			/**
			 * Modify the content displayed after the entry link in an entry list.
			 * @since 1.7.2
			 * @param string $item_output The HTML output for the after_link content
			 * @param array $entry Gravity Forms entry array
			 * @param GravityView_Entry_List $this The current class instance
			 */
			$after_link = apply_filters( 'gravityview/entry-list/after-link', '<div>' . $this->after_link . '</div>', $entry, $this );

			$item_output .= $after_link;
		}

		$item_output = GravityView_API::replace_variables( $item_output, $this->form, $entry );

		$item_output = '<' . $this->item_tag . '>' . $item_output . '</' . $this->item_tag . '>';

		/**
		 * Modify each item's output in an entry list.
		 * @since 1.7.2
		 * @param string $item_output The HTML output for the item
		 * @param array $entry Gravity Forms entry array
		 * @param GravityView_Entry_List $this The current class instance
		 */
		$item_output = apply_filters( 'gravityview/entry-list/item', $item_output, $entry, $this );

		return $item_output;
	}
}
