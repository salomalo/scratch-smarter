<?php
/**
 * Main class to interace with the different Content Editors: CPCFF_PAGE_BUILDERS class
 *
 */
if(!class_exists('CPCFF_PAGE_BUILDERS'))
{
	class CPCFF_PAGE_BUILDERS
	{
		private function __construct(){}
		public static function init()
		{
			$instance = new self();

			add_action( 'enqueue_block_editor_assets', array($instance,'gutenberg_editor' ) );
			add_action( 'elementor/widgets/widgets_registered', array($instance, 'elementor_editor') );
			add_action( 'elementor/elements/categories_registered', array($instance, 'elementor_editor_category') );
		}

		/**************************** GUTENBERG ****************************/

		/**
		 * Loads the javascript resources to integrate the plugin with the Gutenberg editor
		 */
		public function gutenberg_editor()
		{
			global $wpdb;

			wp_enqueue_style('cp_calculatedfieldsf_gutenberg_editor_css', plugins_url('/css/gutenberg.css', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH));
			wp_enqueue_script('cp_calculatedfieldsf_gutenberg_editor', plugins_url('/js/cp_calculatedfieldsf_gutenberg.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH));

			$url = CPCFF_AUXILIARY::site_url();
			$url .= ((strpos($url, '?') === false) ? '?' : '&').'cff-editor-preview=1&cff-amp-redirected=1&cff-form=';
			$config = array(
				'url' => $url,
				'forms' => array(),
				'labels' => array(
					'required_form' => __('Select a form', 'calculated-fields-form'),
					'forms'			=> __('Forms', 'calculated-fields-form'),
					'attributes'	=> __('Additional attributes', 'calculated-fields-form')
				)
			);

			$forms = $wpdb->get_results( "SELECT id, form_name FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE );

			foreach ($forms as $form)
				$config['forms'][$form->id] = esc_attr('('.$form->id.') '.$form->form_name);

			wp_localize_script('cp_calculatedfieldsf_gutenberg_editor', 'cpcff_gutenberg_editor_config', $config);
		} // End gutenberg_editor

		/**************************** ELEMENTOR ****************************/

		public function elementor_editor_category()
		{
			require_once CP_CALCULATEDFIELDSF_BASE_PATH.'/pagebuilders/elementor_category.pb.php';
		} // End elementor_editor

		public function elementor_editor()
		{
			require_once CP_CALCULATEDFIELDSF_BASE_PATH.'/pagebuilders/elementor.pb.php';
		} // End elementor_editor

	} // End CPCFF_PAGE_BUILDERS
}