<?php
class SGPBRegistrationConfig
{
	public static function addDefine($name, $value)
	{
		if (!defined($name)) {
			define($name, $value);
		}
	}

	public static function init()
	{
		self::addDefine('SGPB_REGISTRATION_PATH', WP_PLUGIN_DIR.'/'.SGPB_REGISTRATION_FOLDER_NAME.'/');
		self::addDefine('SGPB_REGISTRATION_PUBLIC_URL', plugins_url().'/'.SGPB_REGISTRATION_FOLDER_NAME.'/public/');
		self::addDefine('SGPB_REGISTRATION_COM_PATH', SGPB_REGISTRATION_PATH.'com/');
		self::addDefine('SGPB_REGISTRATION_PUBLIC_PATH', SGPB_REGISTRATION_PATH.'public/');
		self::addDefine('SGPB_REGISTRATION_VIEWS_PATH', SGPB_REGISTRATION_PUBLIC_PATH.'views/');
		self::addDefine('SGPB_REGISTRATION_CLASSES_PATH', SGPB_REGISTRATION_COM_PATH.'classes/');
		self::addDefine('SGPB_REGISTRATION_EXTENSION_FILE_NAME', 'PopupBuilderRegistrationExtension.php');
		self::addDefine('SGPB_REGISTRATION_EXTENSION_CLASS_NAME', 'SGPBPopupBuilderRegistrationExtension');
		self::addDefine('SGPB_REGISTRATION_HELPERS', SGPB_REGISTRATION_COM_PATH.'helpers/');
		self::addDefine('SGPB_POPUP_TYPE_REGISTRATION', 'registration');
		self::addDefine('SGPB_POPUP_TYPE_REGISTRATION_DISPLAY_NAME', 'REGISTRATION');
		self::addDefine('SGPB_REGISTRATION_CONDITION_KEY', 'registrationConditions');
		self::addDefine('SG_POPUP_POST_TYPE', 'popupbuilder');
		self::addDefine('SG_POPUP_TEXT_DOMAIN', 'popupBuilder');

		self::addDefine('SGPB_REGISTRATION_URL', plugins_url().'/'.SGPB_REGISTRATION_FOLDER_NAME.'/');

		self::addDefine('SGPB_REGISTRATION_JS_URL', SGPB_REGISTRATION_PUBLIC_URL.'js/');
		self::addDefine('SGPB_REGISTRATION_CSS_URL', SGPB_REGISTRATION_PUBLIC_URL.'css/');
		self::addDefine('SGPB_REGISTRATION_TEXT_DOMAIN', SGPB_REGISTRATION_FOLDER_NAME);
		self::addDefine('SGPB_REGISTRATION_PLUGIN_MAIN_FILE', 'PopupBuilderRegistration.php');
		self::addDefine('SGPB_REGISTRATION_AVALIABLE_VERSION', 1);

		self::addDefine('SGPB_REGISTRATION_ACTION_KEY', 'PopupRegistration');
		self::addDefine('SGPB_REGISTRATION_STORE_URL', 'https://popup-builder.com/');

		self::addDefine('SGPB_REGISTRATION_ITEM_ID', 125282);
		self::addDefine('SGPB_REGISTRATION_AUTHOR', 'Sygnoos');
		self::addDefine('SGPB_REGISTRATION_KEY', 'POPUP_REGISTRATION');
	}
}

SGPBRegistrationConfig::init();
