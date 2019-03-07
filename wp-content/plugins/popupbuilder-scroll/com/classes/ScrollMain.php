<?php
namespace sgpbscroll;
use \SgpbPopupExtensionRegister;
use \SGPBScrollConfig;

class Scroll
{
	private static $instance = null;
	private $actions;
	private $filters;

	private function __construct()
	{
		$this->init();
	}

	private function __clone()
	{

	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init()
	{
		$this->includeFiles();
		add_action('init', array($this, 'wpInit'), 1);
		$this->registerHooks();
	}

	public function includeFiles()
	{
		require_once(SGPB_SCROLL_HELPERS.'AdminHelper.php');
		require_once(SGPB_SCROLL_CLASSES_PATH.'Actions.php');
		require_once(SGPB_SCROLL_CLASSES_PATH.'Filters.php');
	}

	public function wpInit()
	{
		SGPBScrollConfig::addDefine('SG_VERSION_POPUP_SCROLL', 1.1);
		$this->actions = new Actions();
		$this->filters = new Filters();
	}

	private function registerHooks()
	{
		register_activation_hook(SGPB_SCROLL_FILE_NAME, array($this, 'activate'));
		register_deactivation_hook(SGPB_SCROLL_FILE_NAME, array($this, 'deactivate'));
	}

	public function activate()
	{
		if (!defined('SG_POPUP_EXTENSION_PATH')) {
			$message = __('To enable Popup Builder '.SGPB_POPUP_TYPE_SCROLL_DISPLAY_NAME.' extension you need to activate Popup Builder plugin', SG_POPUP_TEXT_DOMAIN).'.';
			echo $message;
			wp_die();
		}

		require_once(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtensionRegister.php');
		$pluginName = SGPB_SCROLL_FILE_NAME;
		$classPath = SGPB_SCROLL_CLASSES_PATH.SGPB_SCROLL_EXTENSION_FILE_NAME;
		$className = SGPB_SCROLL_EXTENSION_CLASS_NAME;

		$options = array(
			'licence' => array(
				'key' => SGPB_SCROLL_KEY,
				'storeURL' => SGPB_SCROLL_STORE_URL,
				'file' => WP_PLUGIN_DIR.'/'.SGPB_SCROLL_FILE_NAME,
				'itemId' => SGPB_SCROLL_ITEM_ID,
				'itemName' => __('Popup Builder '.SGPB_POPUP_TYPE_SCROLL_DISPLAY_NAME, SG_POPUP_TEXT_DOMAIN),
				'autor' => SGPB_SCROLL_AUTHOR,
				'boxLabel' => __('Popup Builder '.SGPB_POPUP_TYPE_SCROLL_DISPLAY_NAME.' License', SG_POPUP_TEXT_DOMAIN)
			)
		);

		SgpbPopupExtensionRegister::register($pluginName, $classPath, $className, $options);
	}

	public function deactivate()
	{
		if (!file_exists(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtensionRegister.php')) {
			return false;
		}

		require_once(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtensionRegister.php');
		$pluginName = SGPB_SCROLL_FILE_NAME;
		// remove Popup Builder Scroll extension from registered extensions
		SgpbPopupExtensionRegister::remove($pluginName);

		return true;
	}
}

Scroll::getInstance();
