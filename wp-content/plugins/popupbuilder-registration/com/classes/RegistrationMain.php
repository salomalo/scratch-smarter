<?php
namespace sgpbregistration;
use \SgpbPopupExtensionRegister;
use \SGPBRegistrationConfig;

class Registration
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
		add_action('init', array($this, 'wpInit'));
		$this->registerHooks();
	}

	public function includeFiles()
	{
		require_once(SGPB_REGISTRATION_HELPERS.'AdminHelper.php');
		require_once(SGPB_REGISTRATION_CLASSES_PATH.'Actions.php');
		require_once(SGPB_REGISTRATION_CLASSES_PATH.'Filters.php');
		require_once(SGPB_REGISTRATION_CLASSES_PATH.'Ajax.php');
	}

	public function wpInit()
	{
		SGPBRegistrationConfig::addDefine('SG_VERSION_POPUP_REGISTRATION', 1.2);
		$this->actions = new Actions();
		$this->filters = new Filters();
		new Ajax();
	}

	private function registerHooks()
	{
		register_activation_hook(SGPB_REGISTRATION_FILE_NAME, array($this, 'activate'));
		register_deactivation_hook(SGPB_REGISTRATION_FILE_NAME, array($this, 'deactivate'));
	}

	public function activate()
	{
		if (!defined('SG_POPUP_EXTENSION_PATH')) {
			$message = __('To enable Popup Builder Registration extension you need to activate Popup Builder plugin', SG_POPUP_TEXT_DOMAIN).'.';
			echo $message;
			wp_die();
		}

		require_once(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtensionRegister.php');
		$pluginName = SGPB_REGISTRATION_FILE_NAME;
		$classPath = SGPB_REGISTRATION_CLASSES_PATH.SGPB_REGISTRATION_EXTENSION_FILE_NAME;
		$className = SGPB_REGISTRATION_EXTENSION_CLASS_NAME;

		$options = array(
			'licence' => array(
				'key' => SGPB_REGISTRATION_KEY,
				'storeURL' => SGPB_REGISTRATION_STORE_URL,
				'file' => WP_PLUGIN_DIR.'/'.SGPB_REGISTRATION_FILE_NAME,
				'itemId' => SGPB_REGISTRATION_ITEM_ID,
				'itemName' => __('Popup Builder Registration', SG_POPUP_TEXT_DOMAIN),
				'autor' => SGPB_REGISTRATION_AUTHOR,
				'boxLabel' => __('Popup Builder Registration License', SG_POPUP_TEXT_DOMAIN)
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
		$pluginName = SGPB_REGISTRATION_FILE_NAME;
		// remove Popup Builder extension from registered extensions
		SgpbPopupExtensionRegister::remove($pluginName);

		return true;
	}
}

Registration::getInstance();

