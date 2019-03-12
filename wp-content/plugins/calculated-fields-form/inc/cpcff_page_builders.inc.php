<?php
/**
 * Main class to interace with the different Content Editors: CPCFF_PAGE_BUILDERS class
 *
 */
if(!class_exists('CPCFF_PAGE_BUILDERS'))
{
	class CPCFF_PAGE_BUILDERS
	{
		private static $_instance;
		private function __construct(){}
		private static function instance()
		{
			if(!isset(self::$_instance)) self::$_instance = new self();
			return self::$_instance;
		} // End instance

		public static function run()
		{
			$instance = self::instance();
			add_action('init', array($instance, 'init'));
			add_action('after_setup_theme', array($instance, 'after_setup_theme'));
		}

		public function init()
		{
			$instance = $instance = self::instance();

			// Gutenberg editor
			add_action( 'enqueue_block_editor_assets', array($instance,'gutenberg_editor' ) );

			// Elementor
			add_action( 'elementor/widgets/widgets_registered', array($instance, 'elementor_editor') );
			add_action( 'elementor/elements/categories_registered', array($instance, 'elementor_editor_category') );
		} // End init

		public function after_setup_theme()
		{
			$instance = $instance = self::instance();

			// SiteOrigin
			add_filter('siteorigin_widgets_widget_folders', array($instance, 'siteorigin_widgets_collection'));
			add_filter('siteorigin_panels_widget_dialog_tabs', array($instance, 'siteorigin_panels_widget_dialog_tabs'));
		} // End after_setup_theme

		/**************************** GUTENBERG ****************************/

		/**
		 * Loads the javascript resources to integrate the plugin with the Gutenberg editor
		 */
		public function gutenberg_editor()
		{
			global $wpdb;

			wp_enqueue_style('cp_calculatedfieldsf_gutenberg_editor_css', plugins_url('/pagebuilders/gutenberg/assets/css/gutenberg.css', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH));
			wp_enqueue_script('cp_calculatedfieldsf_gutenberg_editor', plugins_url('/pagebuilders/gutenberg/assets/js/gutenberg.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH));

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
			require_once CP_CALCULATEDFIELDSF_BASE_PATH.'/pagebuilders/elementor/elementor_category.pb.php';
		} // End elementor_editor

		public function elementor_editor()
		{
			wp_enqueue_style('cp_calculatedfieldsf_elementor_editor_css', plugins_url('/pagebuilders/elementor/assets/elementor.css', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH));
			require_once CP_CALCULATEDFIELDSF_BASE_PATH.'/pagebuilders/elementor/elementor.pb.php';
		} // End elementor_editor

		/**************************** ELEMENTOR ****************************/

		public function siteorigin_widgets_collection($folders)
		{
			$folders[] = CP_CALCULATEDFIELDSF_BASE_PATH.'/pagebuilders/siteorigin/';
			return $folders;
		} // End siteorigin_widgets_collection

		public function siteorigin_panels_widget_dialog_tabs($tabs)
		{
			$tabs[] = array(
				'title' => __('Calculated Fields Form', 'calculated-fields-form'),
				'filter' => array(
					'groups' => array('calculated-fields-form')
				)
			);

			return $tabs;
		} // End siteorigin_panels_widget_dialog_tabs

	} // End CPCFF_PAGE_BUILDERS
}