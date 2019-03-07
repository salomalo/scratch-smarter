<?php
class SGPBScrollConfig
{
	public static function addDefine($name, $value)
	{
		if (!defined($name)) {
			define($name, $value);
		}
	}

	public static function init()
	{
		self::addDefine('SGPB_SCROLL_PATH', WP_PLUGIN_DIR.'/'.SGPB_SCROLL_FOLDER_NAME.'/');
		self::addDefine('SGPB_SCROLL_PUBLIC_URL', plugins_url().'/'.SGPB_SCROLL_FOLDER_NAME.'/public/');
		self::addDefine('SGPB_SCROLL_COM_PATH', SGPB_SCROLL_PATH.'com/');
		self::addDefine('SGPB_SCROLL_PUBLIC_PATH', SGPB_SCROLL_PATH.'public/');
		self::addDefine('SGPB_SCROLL_VIEWS_PATH', SGPB_SCROLL_PUBLIC_PATH.'views/');
		self::addDefine('SGPB_SCROLL_CLASSES_PATH', SGPB_SCROLL_COM_PATH.'classes/');
		self::addDefine('SGPB_SCROLL_EXTENSION_FILE_NAME', 'PopupBuilderScrollExtension.php');
		self::addDefine('SGPB_SCROLL_EXTENSION_CLASS_NAME', 'SGPBPopupBuilderScrollExtension');
		self::addDefine('SGPB_SCROLL_HELPERS', SGPB_SCROLL_COM_PATH.'helpers/');
		self::addDefine('SGPB_SCROLL_EVENT_KEY', 'onScroll');
		self::addDefine('SGPB_POPUP_TYPE_SCROLL_DISPLAY_NAME', 'Scroll');
		self::addDefine('SG_POPUP_POST_TYPE', 'popupbuilder');
		self::addDefine('SGPB_POPUP_TYPE_SCROLL', 'scroll');
		self::addDefine('SG_POPUP_TEXT_DOMAIN', 'popupBuilder');

		self::addDefine('SGPB_SCROLL_URL', plugins_url().'/'.SGPB_SCROLL_FOLDER_NAME.'/');

		self::addDefine('SGPB_SCROLL_PLUGIN_URL', 'https://wordpress.org/plugins/scroll/');
		self::addDefine('SGPB_SCROLL_JS_URL', SGPB_SCROLL_PUBLIC_URL.'js/');
		self::addDefine('SGPB_SCROLL_CSS_URL', SGPB_SCROLL_PUBLIC_URL.'css/');
		self::addDefine('SGPB_SCROLL_TEXT_DOMAIN', SGPB_SCROLL_FOLDER_NAME);
		self::addDefine('SGPB_SCROLL_PLUGIN_MAIN_FILE', 'PopupBuilderScroll.php');
		self::addDefine('SGPB_SCROLL_AVALIABLE_VERSION', 1);

		self::addDefine('SGPB_SCROLL_ACTION_KEY', 'PopupScroll');
		self::addDefine('SGPB_SCROLL_STORE_URL', 'https://popup-builder.com/');

		self::addDefine('SGPB_SCROLL_ITEM_ID', 106615);
		self::addDefine('SGPB_SCROLL_AUTHOR', 'Sygnoos');
		self::addDefine('SGPB_SCROLL_KEY', 'POPUP_SCROLL');
	}
}

SGPBScrollConfig::init();
