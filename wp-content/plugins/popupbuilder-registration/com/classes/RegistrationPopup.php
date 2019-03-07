<?php
namespace sgpb;
use \sgpbregistration\AdminHelper as RegisterAdminHelper;
require_once(SG_POPUP_CLASSES_POPUPS_PATH.'SGPopup.php');

class RegistrationPopup extends SGPopup
{
	private $data;
	private $validateObj;

	public function __construct()
	{
		add_filter('sgpbAdminJsFiles', array($this, 'adminJsFilter'), 1, 1);
		add_filter('sgpbAdminCssFiles', array($this, 'adminCssFilter'), 1, 1);
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function adminJsFilter($jsFiles)
	{
		$jsFiles[] = array('folderUrl' => SGPB_REGISTRATION_JS_URL, 'filename' => 'RegistrationAdmin.js');

		return $jsFiles;
	}

	public function setValidateObj($validateObj)
	{
		$this->validateObj = $validateObj;
	}

	public function getValidateObj()
	{
		return $this->validateObj;
	}

	public function getExtraRenderOptions()
	{
		return array();
	}

	public function getPopupTypeMainView()
	{
		return array(
			'filePath' => SGPB_REGISTRATION_VIEWS_PATH.'mainView.php',
			'metaboxTitle' => __('Registration Options', SG_POPUP_TEXT_DOMAIN)
		);
	}

	public function adminCssFilter($cssFiles)
	{
		$cssFiles[] = array(
			'folderUrl' => SG_POPUP_CSS_URL,
			'filename' => 'ResetFormStyle.css'
		);

		$cssFiles[] = array(
			'folderUrl'=> SGPB_REGISTRATION_CSS_URL,
			'filename' => 'registration.css'
		);

		return $cssFiles;
	}

	public function cssFilter($cssFiles)
	{
		$cssFiles[] = array(
			'folderUrl' => SG_POPUP_CSS_URL,
			'filename' => 'ResetFormStyle.css'
		);

		$cssFiles[] = array(
			'folderUrl'=> SGPB_REGISTRATION_CSS_URL,
			'filename' => 'registration.css'
		);

		return $cssFiles;
	}

	public function getOptionValue($optionName, $forceDefaultValue = false)
	{
		return parent::getOptionValue($optionName, $forceDefaultValue);
	}

	private function frontendFilters()
	{
		add_filter('sgpbFrontendJs', array($this, 'jsFilter'), 1, 1);
		add_filter('sgpbFrontendCssFiles', array($this, 'cssFilter'), 1, 1);
	}

	public function frontJsFilter($jsFiles)
	{
		$jsFiles[] = array(
			'folderUrl' => SGPB_REGISTRATION_JS_URL,
			'filename' => 'Validate.js'
		);
		$jsFiles[] = array(
			'folderUrl' => SGPB_REGISTRATION_JS_URL,
			'filename' => 'Registration.js'
		);

		return $jsFiles;
	}

	public static function allowToOpen($options, $args)
	{
		$popupObj = @$args['popupObj'];
		$status = true;
		$userStatus = is_user_logged_in();

		if ($popupObj->getType() == SGPB_POPUP_TYPE_REGISTRATION && $userStatus) {
			$status = false;
		}

		return $status;
	}

	public function jsFilter($jsFiles)
	{
		$popupId = $this->getId();

		$jsFiles['jsFiles'][] = array(
			'folderUrl' => SGPB_REGISTRATION_JS_URL,
			'filename' => 'Validate.js'
		);

		$jsFiles['jsFiles'][] = array(
			'folderUrl' => SGPB_REGISTRATION_JS_URL,
			'filename' => 'Registration.js'
		);

		$jsFiles['localizeData'][] = array(
			'handle' => 'Registration.js',
			'name' => 'SGPB_VALIDATE_JSON_'.$popupId,
			'data' => $this->getValidateObj()
		);

		return $jsFiles;
	}

	public function localizedData($localized)
	{

		$localizeData[] = '';

		return $localized;
	}

	public function getPopupTypeContent()
	{
		$id = $this->getId();
		$content = $this->getContent();
		$this->setRegistrationFormData($id);
		$formData = $this->createFormFieldsData();
		$forceRtlClass = '';


		$styleData = array(
			'placeholderColor' => $this->getOptionValue('sgpb-registration-text-placeholder-color')
		);
		$content .= '<div class="sgpb-registration-form-'.$id.' sgpb-registration-form-admin-wrapper'.$forceRtlClass.'">';
		$content .= $this->getFormMessages();
		$content .= RegisterAdminHelper::renderForm(@$formData);
		$content .= '</div>';

		$content .= $this->getFormCustomStyles(@$styleData);

		$validateObj = $this->createValidateObj($formData);
		$this->setValidateObj($validateObj);

		$this->frontendFilters();

		return $content;
	}

	public function createValidateObj($contactFields)
	{
		$validateObj = '';
		$requiredMessage = $this->getOptionValue('sgpb-registration-required-error');

		if (empty($contactFields)) {
			return $validateObj;
		}

		$rules = '"rules": { ';
		$messages = '"messages": { ';
		$validateObj = '{ ';

		foreach ($contactFields as $contactField) {

			if (empty($contactField['attrs'])) {
				continue;
			}

			$attrs = $contactField['attrs'];
			$type = 'text';
			$name = '';
			$required = false;

			if (!empty($attrs['type'])) {
				$type = $attrs['type'];
			}
			if (!empty($attrs['name'])) {
				$name = $attrs['name'];
			}
			if (!empty($attrs['data-required'])) {
				$required = $attrs['data-required'];
			}

			if ($type == 'email') {
				$rules .= '"'.$name.'": {"required": true, "email": true},';
				continue;
			}

			if ($name == 'sgpb-registration-confirm-password') {
				$rules .= '"'.$name.'": {"required": true, "equalTo": "#sgpb-registration-password"},';
				continue;
			}

			if (!$required) {
				continue;
			}

			$rules .= '"'.$name.'" : "required",';
			$messages .= '"'.$name.'" : "'.$requiredMessage.'",';
		}

		$rules = rtrim($rules, ',');
		$messages = rtrim($messages, ',');

		$rules .= '},';
		$messages .= '}';
		$validateObj .= $rules;
		$validateObj .= $messages;
		$validateObj .= '}';

		return $validateObj;
	}

	public function setRegistrationFormData($formId)
	{
		$savedData = array();

		if (!empty($formId)) {
			$savedData = SGPopup::getSavedData($formId);
		}

		$this->setData($savedData);
	}

	private function getFieldValue($optionName)
	{
		$optionValue = '';
		$postData = $this->getData();

		if (!empty($postData[$optionName])) {
			return $postData[$optionName];
		}

		$defaultData = $this->getDefaultDataByName($optionName);

		// when saved data does not exist we try find inside default values
		if (empty($postData) && !empty($defaultData)) {
			return $defaultData['defaultValue'];
		}

		return $optionValue;
	}

	public function createFormFieldsData()
	{
		$formData = array();
		$inputStyles = array();
		$submitStyles = array();
		$postData = $this->getData();
		$usernameLabel = false;
		$emailLabel = false;
		$passwordLabel = false;
		$confirmPasswordLabel = false;
		if ($this->getFieldValue('sgpb-username-label'))  {
			$usernameLabel = $this->getFieldValue('sgpb-username-label');
		}
		if ($this->getFieldValue('sgpb-email-label'))  {
			$emailLabel = $this->getFieldValue('sgpb-email-label');
		}
		if ($this->getFieldValue('sgpb-password-label'))  {
			$passwordLabel = $this->getFieldValue('sgpb-password-label');
		}
		if ($this->getFieldValue('sgpb-confirm-password-label'))  {
			$confirmPasswordLabel = $this->getFieldValue('sgpb-confirm-password-label');
		}

		if ($this->getFieldValue('sgpb-registration-text-width'))  {
			$inputWidth = $this->getFieldValue('sgpb-registration-text-width');
			$inputStyles['width'] = AdminHelper::getCSSSafeSize($inputWidth);
		}
		if ($this->getFieldValue('sgpb-registration-text-height')) {
			$inputHeight = $this->getFieldValue('sgpb-registration-text-height');
			$inputStyles['height'] = AdminHelper::getCSSSafeSize($inputHeight);
		}
		if ($this->getFieldValue('sgpb-registration-text-border-width')) {
			$inputBorderWidth = $this->getFieldValue('sgpb-registration-text-border-width');
			$inputStyles['border-width'] = AdminHelper::getCSSSafeSize($inputBorderWidth);
		}
		if ($this->getFieldValue('sgpb-registration-text-border-color')) {
			$inputStyles['border-color'] = $this->getFieldValue('sgpb-registration-text-border-color');
		}
		if ($this->getFieldValue('sgpb-registration-text-bg-color')) {
			$inputStyles['background-color'] = $this->getFieldValue('sgpb-registration-text-bg-color');
		}
		if ($this->getFieldValue('sgpb-registration-text-color')) {
			$inputStyles['color'] = $this->getFieldValue('sgpb-registration-text-color');
		}

		if ($this->getFieldValue('sgpb-registration-btn-width')) {
			$submitWidth = $this->getFieldValue('sgpb-registration-btn-width');
			$submitStyles['width'] = AdminHelper::getCSSSafeSize($submitWidth);
		}
		if ($this->getFieldValue('sgpb-registration-btn-height')) {
			$submitHeight = $this->getFieldValue('sgpb-registration-btn-height');
			$submitStyles['height'] = AdminHelper::getCSSSafeSize($submitHeight);
		}
		if ($this->getFieldValue('sgpb-registration-btn-bg-color')) {
			$submitStyles['background-color'] = $this->getFieldValue('sgpb-registration-btn-bg-color');
		}
		if ($this->getFieldValue('sgpb-registration-btn-text-color')) {
			$submitStyles['color'] = $this->getFieldValue('sgpb-registration-btn-text-color');
		}
		$submitStyles['text-transform'] = 'none !important';

		$firstNamePlaceholder = $this->getFieldValue('sgpb-username-placeholder');
		$emailPlaceholder = $this->getFieldValue('sgpb-email-placeholder');
		$passwordPlaceholder = $this->getFieldValue('sgpb-password-placeholder');
		$confirmPasswordPlaceholder = $this->getFieldValue('sgpb-confirm-password-placeholder');
		$requiredField = true;
		if (is_admin()) {
			$requiredField = false;
		}

		$formData['username'] = array(
			'isShow' => true,
			'attrs' => array(
				'type' => 'text',
				'hasLabel' => $usernameLabel,
				'data-required' => $requiredField,
				'autocomplete' => 'off',
				'name' => 'sgpb-registration-username',
				'data-username' => 'sgpb-registration-username',
				'placeholder' => $firstNamePlaceholder,
				'class' => 'js-registration-text-inputs js-registration-username-input',
				'labelClass' => 'js-registration-username-label-edit',
				'data-error-message-class' => 'sgpb-registration-username-error-message'
			),
			'style' => $inputStyles,
			'errorMessageBoxStyles' => $inputStyles['width']
		);

		$formData['email'] = array(
			'isShow' => true,
			'attrs' => array(
				'type' => 'email',
				'hasLabel' => $emailLabel,
				'data-required' => $requiredField,
				'autocomplete' => 'off',
				'name' => 'sgpb-registration-email',
				'data-email' => 'sgpb-registration-email',
				'placeholder' => $emailPlaceholder,
				'class' => 'js-registration-text-inputs js-registration-email-input',
				'labelClass' => 'js-registration-email-label-edit',
				'data-error-message-class' => 'sgpb-registration-email-error-message'
			),
			'style' => $inputStyles,
			'errorMessageBoxStyles' => $inputStyles['width']
		);

		$formData['password'] = array(
			'isShow' => true,
			'attrs' => array(
				'type' => 'password',
				'hasLabel' => $passwordLabel,
				'data-required' => true,
				'name' => 'sgpb-registration-password',
				'id' => 'sgpb-registration-password',
				'data-password' => 'sgpb-registration-password',
				'placeholder' => $passwordPlaceholder,
				'class' => 'js-registration-text-inputs js-registration-password-input',
				'labelClass' => 'js-registration-password-label-edit',
				'data-error-message-class' => 'sgpb-registration-password-error-message'
			),
			'style' => $inputStyles,
			'errorMessageBoxStyles' => $inputStyles['width']
		);

		$formData['confirm-password'] = array(
			'isShow' => true,
			'attrs' => array(
				'type' => 'password',
				'hasLabel' => $confirmPasswordLabel,
				'data-required' => true,
				'name' => 'sgpb-registration-confirm-password',
				'data-confirm-password' => 'sgpb-registration-confirm-password',
				'placeholder' => $confirmPasswordPlaceholder,
				'class' => 'js-registration-text-inputs js-registration-confirm-password-input',
				'labelClass' => 'js-registration-confirm-password-label-edit',
				'data-error-message-class' => 'sgpb-registration-confirm-password-error-message'
			),
			'style' => $inputStyles,
			'errorMessageBoxStyles' => $inputStyles['width']
		);

		$hiddenChecker['position'] = 'absolute';
		// For protected bots and spams
		$hiddenChecker['left'] = '-5000px';
		$hiddenChecker['padding'] = '0';
		$formData['hidden-checker'] = array(
			'isShow' => false,
			'attrs' => array(
				'type' => 'hidden',
				'data-required' => false,
				'name' => 'sgpb-registration-hidden-checker',
				'value' => '',
				'class' => 'js-registration-text-inputs js-registration-last-name-input'
			),
			'style' => $hiddenChecker
		);

		$submitTitle = $this->getFieldValue('sgpb-registration-btn-title');
		$progressTitle = $this->getFieldValue('sgpb-registration-btn-progress-title');
		$formData['submit'] = array(
			'isShow' => true,
			'attrs' => array(
				'type' => 'submit',
				'name' => 'sgpb-registration-submit',
				'value' => $submitTitle,
				'data-title' => $submitTitle,
				'data-progress-title' => $progressTitle,
				'class' => 'js-registration-submit-btn'
			),
			'style' => $submitStyles
		);

		return $formData;
	}

	public function getFormCustomStyles($styleData)
	{
		$placeholderColor = $styleData['placeholderColor'];
		$formBackgroundColor = $this->getFieldValue('sgpb-registration-form-bg-color');
		$formPadding = $this->getFieldValue('sgpb-registration-form-padding');
		$formBackgroundOpacity = $this->getFieldValue('sgpb-registration-form-bg-opacity');
		$popupId = $this->getId();
		if (isset($styleData['formBackgroundOpacity'])) {
			$formBackgroundOpacity = $styleData['formBackgroundOpacity'];
		}
		if (isset($styleData['formColor'])) {
			$formBackgroundColor = $styleData['formColor'];
		}
		if (isset($styleData['formPadding'])) {
			$formPadding = $styleData['formPadding'];
		}
		$formBackgroundColor = AdminHelper::hexToRgba($formBackgroundColor, $formBackgroundOpacity);

		ob_start();
		?>
			<style type="text/css">
				.sgpb-registration-form-<?php echo $popupId; ?> {background-color: <?php echo $formBackgroundColor; ?>;padding: <?php echo $formPadding.'px'; ?>}
				.sgpb-registration-form-<?php echo $popupId; ?> .js-registration-text-inputs::-webkit-input-placeholder {color: <?php echo $placeholderColor; ?>;font-weight: lighter;}
				.sgpb-registration-form-<?php echo $popupId; ?> .js-registration-text-inputs::-moz-placeholder {color:<?php echo $placeholderColor; ?>;font-weight: lighter;}
				.sgpb-registration-form-<?php echo $popupId; ?> .js-registration-text-inputs:-ms-input-placeholder {color:<?php echo $placeholderColor; ?>;font-weight: lighter;} /* ie */
				.sgpb-registration-form-<?php echo $popupId; ?> .js-registration-text-inputs:-moz-placeholder {color:<?php echo $placeholderColor; ?>;font-weight: lighter;}
			</style>
		<?php
		$styles = ob_get_contents();
		ob_get_clean();

		return $styles;
	}

	public function getSubPopupObj()
	{
		$options = $this->getOptions();
		$subPopups = parent::getSubPopupObj();
		if ($options['sgpb-registration-success-behavior'] == 'openPopup') {
			$subPopupId = (!empty($options['sgpb-registration-success-popup'])) ? (int)$options['sgpb-registration-success-popup']: null;

			if (empty($subPopupId)) {
				return $subPopups;
			}

			$subPopupObj = SGPopup::find($subPopupId);
			if (!empty($subPopupObj) && ($subPopupObj instanceof SGPopup)) {
				// We remove all events because this popup will be open after successful registration
				$subPopupObj->setEvents(array('param' => 'click', 'value' => ''));
				$subPopups[] = $subPopupObj;
			}
		}

		return $subPopups;
	}

	public function getFormMessages()
	{
		$errorMessage = $this->getOptionValue('sgpb-registration-error-message');
		$messages = '<div class="registration-form-messages sgpb-alert sgpb-alert-danger sg-hide-element">';
		$messages .= '<p>'.$errorMessage.'</p>';
		$messages .= '</div>';

		return $messages;
	}
}
