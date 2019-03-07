<?php
require_once(SG_POPUP_EXTENSION_PATH.'SgpbIPopupExtension.php');

class SGPBPopupBuilderRegistrationExtension implements SgpbIPopupExtension
{
	public function getScripts($page, $data)
	{
		if (empty($data['popupType']) || @$data['popupType'] != SGPB_POPUP_TYPE_REGISTRATION) {
			return false;
		}

		$jsFiles = array();
		$localizeData = array();

		$scriptData = array(
			'jsFiles' => apply_filters('sgpbRegistrationAdminJsFiles', $jsFiles),
			'localizeData' => apply_filters('sgpbRegistrationAdminJsLocalizedData', $localizeData)
		);

		$scriptData = apply_filters('sgpbRegistrationAdminJs', $scriptData);

		return $scriptData;
	}

	public function getStyles($page, $data)
	{
		$cssFiles = array();
		// for current popup type page load and for popup types pages too
		if (@$data['popupType'] == SGPB_POPUP_TYPE_REGISTRATION || $page == 'popupType') {
			// here we will include current popup type custom styles
		}

		$cssData = array(
			'cssFiles' => apply_filters('sgpbRegistrationAdminCssFiles', $cssFiles)
		);

		return $cssData;
	}

	public function getFrontendScripts($page, $data)
	{
		$scriptData = array();

		return $scriptData;
	}

	public function getFrontendStyles($page, $data)
	{
		$cssData = array();

		return $cssData;
	}
}
