<?php
namespace sgpbscroll;

class AdminHelper
{
	public static function oldPluginDetected()
	{
		$hasOldPlugin = false;
		$message = '';

		if (file_exists(WP_PLUGIN_DIR.'/popup-builder-platinum')) {
			$hasOldPlugin = true;
		}
		else if (file_exists(WP_PLUGIN_DIR.'/popup-builder-gold')) {
			$hasOldPlugin = true;
		}
		else if (file_exists(WP_PLUGIN_DIR.'/popup-builder-silver')) {
			$hasOldPlugin = true;
		}

		if ($hasOldPlugin) {
			$message = __("You're using an old version of Popup Builder plugin. We have a brand-new version that you can download from your popup-builder.com account. Please, install the new version of Popup Builder plugin to be able to use it with the new extensions.", 'popupBuilder').'.';
		}
		
		$result = array(
			'status' => $hasOldPlugin,
			'message' => $message
		);

		return $result;
	}
	
	/*
	 * check allow to install current extension
	 */
	public static function isSatisfyParameters()
	{
		$hasOldPlugin = AdminHelper::oldPluginDetected();

		if (@$hasOldPlugin['status'] == true) {
			return array('status' => false, 'message' => @$hasOldPlugin['message']);
		}

		return array('status' => true, 'message' => '');
	}
}