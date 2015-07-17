<?php

/**
 * WDS Network Require Login Network Admin
 * @version 0.1.0
 * @package WDS Network Require Login
 */
abstract class WDSNRL_Admin_Base {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	protected $key = '';

	/**
 	 * Settings page metabox id
 	 * @var string
 	 */
	protected $metabox_id = '';

	/**
	 * Settings Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Menu title
	 * @var string
	 */
	protected $menu_title = '';

	/**
	 * Settings Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * The hook where the option menu should be attached
	 * @var string
	 */
	protected $admin_menu_hook = 'admin_menu';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	protected function __construct( $title ) {
		// Set our title
		$this->title = $title;
		// Set our title
		$this->menu_title = __( 'Require Login', 'wds-network-require-login' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( $this->admin_menu_hook, array( $this, 'add_options_page' ) );
		add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Need to extend and should return result of add_submenu_page or similar
	 *
	 * @since  0.1.0
	 * @return string Admin page hook
	 */
	abstract protected function add_menu_page();

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = $this->add_menu_page();

		// Include CMB CSS in the head to avoid FOUT
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		foreach ( $this->fields() as $field ) {
			$cmb->add_field( $field );
		}
	}

	/**
	 * Need to extend and should array of CMB2 field config arrays
	 *
	 * @since  0.1.0
	 * @return array Array of CMB2 field config arrays
	 */
	abstract protected function fields();

	/**
	 * Replaces get_option with get_site_option
	 * @since  0.1.0
	 */
	public function _get_override( $test, $default = false ) {
		return get_site_option( $this->key, $default );
	}

	/**
	 * Replaces update_option with update_site_option
	 * @since  0.1.0
	 */
	public function _update_override( $test, $option_value ) {
		return update_site_option( $this->key, $option_value );
	}

	/**
	 * Wrapper function around cmb2_get_option
	 * @since  0.1.0
	 * @param  string  $key Options array key
	 * @return mixed        Option value
	 */
	public function get_option( $key = '' ) {
		return cmb2_get_option( $this->key, $key );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}