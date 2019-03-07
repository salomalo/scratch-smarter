<?php
require_once(SG_POPUP_EXTENSION_PATH.'SgpbIPopupExtension.php');

class SGPBPopupBuilderScrollExtension implements SgpbIPopupExtension
{
	public function getScripts($page, $data)
	{
		if (empty($data['popupType']) || @$data['popupType'] != SGPB_POPUP_TYPE_SCROLL) {
			return false;
		}

		$jsFiles = array();
		$localizeData = array();

		$scriptData = array(
			'jsFiles' => apply_filters('sgpbScrollAdminJsFiles', $jsFiles),
			'localizeData' => apply_filters('sgpbScrollAdminJsLocalizedData', $localizeData)
		);

		$scriptData = apply_filters('sgpbScrollAdminJs', $scriptData);

		return $scriptData;
	}

	public function getStyles($page, $data)
	{
		$cssFiles = array();
		// for current popup type page load and for popup types pages too
		if (@$data['popupType'] == SGPB_POPUP_TYPE_SCROLL || $page == 'popupType') {
			// here we will include current popup type custom styles
		}

		$cssData = array(
			'cssFiles' => apply_filters('sgpbScrollAdminCssFiles', $cssFiles)
		);

		return $cssData;
	}

	public function getFrontendScripts($page, $data)
	{
		$jsFiles = array();
		$localizeData = array();

		$hasScrollPopup = $this->hasConditionFromLoadedPopups($data['popups']);

		if (!$hasScrollPopup) {
			return false;
		}

		$jsFiles[] = array('folderUrl'=> SGPB_SCROLL_JS_URL, 'filename' => 'Scroll.js', 'dep' => array('PopupBuilder.js'));
		$scriptData = array(
			'jsFiles' => apply_filters('sgpbScrollJsFiles', $jsFiles),
			'localizeData' => apply_filters('sgpbScrollJsLocalizedData', $localizeData)
		);

		$scriptData = apply_filters('sgpbScrollJsFilter', $scriptData);

		return $scriptData;
	}

	public function getFrontendStyles($page, $data)
	{
		$cssFiles = array();

		$hasScrollPopup = $this->hasConditionFromLoadedPopups($data['popups']);

		if (!$hasScrollPopup) {
			return false;
		}
		$cssData = array(
			'cssFiles' => apply_filters('sgpbScrollCssFiles', $cssFiles)
		);

		return $cssData;
	}

	protected function hasConditionFromLoadedPopups($popups)
	{
		$hasType = false;

		foreach ($popups as $popup) {
			if (!is_object($popup)) {
				continue;
			}
			$events = $popup->getEvents();

			if (empty($events)) {
				continue;
			}

			foreach ($events as $event) {
				if (isset($event['param']) && $event['param'] == SGPB_SCROLL_EVENT_KEY) {
					$hasType = true;
					break;
				}
			}
		}

		return $hasType;
	}
}
