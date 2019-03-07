<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Promotion Method field class.
 *
 * @since 1.1
 * @uses  NF_Abstracts_Input
 */
class AFNF_Promotion_Method extends NF_Abstracts_Input {
	protected $_name       = 'affwp_afnf_promotion_method';

	protected $_section    = 'affiliatewp';

	protected $_icon       = 'paragraph';

	protected $_type       = 'affwp_afnf_promotion_method';

	protected $_templates  = 'textarea';

	protected $_test_value = '';

	protected $_settings = array(
		'input_limit_set',
		'rte_enable',
		'rte_media',
		'rte_mobile',
		'disable_browser_autocomplete',
		'textarea_rte',
		'disable_rte_mobile',
		'textarea_media'
	);

	public function __construct() {
			parent::__construct();

			$this->_nicename = __( 'Promotion Method', 'affiliatewp-afnf' );

			$this->_settings[ 'default' ][ 'type' ]     = 'textarea';
			$this->_settings[ 'placeholder' ][ 'type' ] = 'textarea';

			$this->_test_value = __(
				'List the ways you\'ll be promoting.',
				'Placeholder text for the Ninja Forms Promotion Method field.',
				'affiliatewp-afnf'
			);
	}

	public function admin_form_element( $id, $value ) {
			return "<textarea class='widefat' name='fields[$id]'>$value</textarea>";
	}
}
