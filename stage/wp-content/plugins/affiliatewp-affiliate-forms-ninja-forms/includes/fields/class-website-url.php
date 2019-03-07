<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AFNF_Website_URL class.
 *
 * @since  1.1
 * @uses   NF_Abstracts_Input
 */
class AFNF_Website_URL extends NF_Abstracts_Input {
	protected $_name       = 'affwp_afnf_website_url';

	protected $_section    = 'affiliatewp';

	protected $_icon       = 'text-width';

	protected $_aliases    = array( 'input' );

	protected $_type       = 'affwp_afnf_website_url';

	protected $_templates  = 'textbox';

	protected $_test_value = '';

	protected $_settings   = array( 'disable_browser_autocomplete', 'mask', 'custom_mask' );

	public function __construct() {
		parent::__construct();

		$this->_nicename = __( 'Website URL', 'affiliatewp-afnf' );
	}
}
