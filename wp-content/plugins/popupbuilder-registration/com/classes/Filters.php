<?php
namespace sgpbregistration;
use sgpb\SGPopup;

class Filters
{
	private $popup = array();

	public function setPopup($popup)
	{
		$this->popup = $popup;
	}

	public function getPopup()
	{
		return $this->popup;
	}

	public function __construct()
	{
		add_filter('sgpbAddPopupTypePath', array($this, 'typePaths'), 10, 1);
		// by default, it's called inside after register popup builder post type but here we need it to call to get current popup type
		if (class_exists('\SgpbPopupConfig')) {
			\SgpbPopupConfig::popupTypesInit();
		}
		if (isset($_GET['post']) && class_exists('sgpb\SGPopup')) {
			$popup =  @SGPopup::find($_GET['post']);
			$this->setPopup($popup);
		}

		$this->init();
	}

	public function init()
	{
		$popup = $this->getPopup();

		if (isset($_GET['post_type']) && $_GET['post_type'] == SG_POPUP_POST_TYPE) {
			add_filter('sgpbAddPopupType', array($this, 'popupType'), 10, 1);
			add_filter('sgpbAddPopupTypeLabels', array($this, 'addPopupTypeLabels'), 11);
		}
		if ((isset($_GET['sgpb_type']) && $_GET['sgpb_type'] == SGPB_POPUP_TYPE_REGISTRATION) || (is_object($popup) && $popup->getType() == SGPB_POPUP_TYPE_REGISTRATION)) {
			add_action('sgpbPopupDefaultOptions', array($this, 'defaultOptions'), 11);
		}
	}

	public function typePaths($typePaths)
	{
		$typePaths[SGPB_POPUP_TYPE_REGISTRATION] = SGPB_REGISTRATION_CLASSES_PATH;

		return $typePaths;
	}

	public function addPopupTypeLabels($labels)
	{
		$labels[SGPB_POPUP_TYPE_REGISTRATION] = __('Registration', SG_POPUP_TEXT_DOMAIN);

		return $labels;
	}

	public function popupType($popupType)
	{
		$popupType[SGPB_POPUP_TYPE_REGISTRATION] = SGPB_REGISTRATION_AVALIABLE_VERSION;


		return $popupType;
	}

	public function defaultOptions($options)
	{
		$options[] = array('name' => 'sgpb-registration-form-bg-color', 'type' => 'text', 'defaultValue' => '#FFFFFF');
		$options[] = array('name' => 'sgpb-registration-form-bg-opacity', 'type' => 'text', 'defaultValue' => 0.8);
		$options[] = array('name' => 'sgpb-registration-form-padding', 'type' => 'number', 'defaultValue' => 2);
		$options[] = array('name' => 'sgpb-username-label', 'type' => 'text', 'defaultValue' => __('Username'));
		$options[] = array('name' => 'sgpb-email-label', 'type' => 'text', 'defaultValue' => __('Email Address'));
		$options[] = array('name' => 'sgpb-password-label', 'type' => 'text', 'defaultValue' => __('Password'));
		$options[] = array('name' => 'sgpb-confirm-password-label', 'type' => 'text', 'defaultValue' => __('Confirm Password'));
		$options[] = array('name' => 'sgpb-registration-error-message', 'type' => 'text', 'defaultValue' => __('Incorrect username or password.', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-registration-required-error', 'type' => 'text', 'defaultValue' => __('This field is required.', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-registration-text-width', 'type' => 'text', 'defaultValue' => '300px');
		$options[] = array('name' => 'sgpb-registration-text-height', 'type' => 'text', 'defaultValue' => '40px');
		$options[] = array('name' => 'sgpb-registration-text-border-width', 'type' => 'text', 'defaultValue' => '2px');
		$options[] = array('name' => 'sgpb-registration-text-border-color', 'type' => 'text', 'defaultValue' => '#CCCCCC');
		$options[] = array('name' => 'sgpb-registration-text-bg-color', 'type' => 'text', 'defaultValue' => '#FFFFFF');
		$options[] = array('name' => 'sgpb-registration-text-color', 'type' => 'text', 'defaultValue' => '#000000');
		$options[] = array('name' => 'sgpb-registration-text-placeholder-color', 'type' => 'text', 'defaultValue' => '#CCCCCC');
		$options[] = array('name' => 'sgpb-registration-btn-width', 'type' => 'text', 'defaultValue' => '300px');
		$options[] = array('name' => 'sgpb-registration-btn-height', 'type' => 'text', 'defaultValue' => '40px');
		$options[] = array('name' => 'sgpb-registration-btn-title', 'type' => 'text', 'defaultValue' => __('Register'));
		$options[] = array('name' => 'sgpb-registration-btn-progress-title', 'type' => 'text', 'defaultValue' => __('Please wait...', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-registration-btn-bg-color', 'type' => 'text', 'defaultValue' => '#4CAF50');
		$options[] = array('name' => 'sgpb-registration-btn-text-color', 'type' => 'text', 'defaultValue' => '#FFFFFF');
		$options[] = array('name' => 'sgpb-registration-success-behavior', 'type' => 'text', 'defaultValue' => 'refresh');
		$options[] = array('name' => 'sgpb-registration-success-message', 'type' => 'text', 'defaultValue' =>  __('You have successfully logged in', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-registration-success-redirect-URL', 'type' => 'text', 'defaultValue' =>  '');
		$options[] = array('name' => 'sgpb-registration-success-redirect-new-tab', 'type' => 'checkbox', 'defaultValue' =>  '');

		return $options;
	}
}
