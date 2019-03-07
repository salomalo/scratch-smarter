<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGContactformPopup extends SGPopup
{
	public $content;
	public $params;

	function __construct()
	{
		wp_enqueue_script('sg_contactForm', SG_APP_POPUP_URL . '/javascript/sg_contactForm.js', array( 'jquery' ), SG_POPUP_VERSION);
		wp_enqueue_script('jquery');
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function steParams($params)
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();

		$options = json_decode($data['options'], true);
		$obj->steParams($data['contactFormOptions']);
		unset($data['contactFormOptions']);

		$obj->setContent($data['sg_contactForm']);

		return parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;

		$content = $this->getContent();
		$params = $this->getParams();

		global $wpdb;
		if ($editMode) {
			$content = stripslashes($content);
			$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."sg_contact_form_popup SET content=%s, options=%s WHERE id=%d", $content, $params, $this->getId());
			$res = $wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."sg_contact_form_popup (id, content, options) VALUES (%d, %s, %s)", $this->getId(), $content, $params);
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix."sg_contact_form_popup WHERE id = %d", $id);
		$arr = $wpdb->get_row($st, ARRAY_A);

		$this->setContent($arr['content']);
		$this->steParams($arr['options']);
	}

	public function getRemoveOptions()
	{
		return array();
	}

	protected function getExtraRenderOptions()
	{
		$ajaxNonce = wp_create_nonce("sgPopupBuilderContactNonce");
		$content = "<div class='contact-content-wrapper'>";
		$content .= $this->getContent();
		$popupId = $this->getId();
		$content .= "</div>";
		$options = json_decode($this->getParams(), true);
		$formElements = '';

		$popupTitle = $this->getTitle();
		$namePlaceholder = $options['contact-name'];
		$subjectPlaceholder = $options['contact-subject'];
		$emailPlaceholder = $options['contact-email'];
		$messagePlaceholder = $options['contact-message'];
		$sgContactInputsWidth = $this->changeDimension($options['contact-inputs-width']);
		$sgContactBtnWidth = $this->changeDimension($options['contact-btn-width']);
		$sgContactInputsHeight = $this->changeDimension($options['contact-inputs-height']);
		$sgContactBtnHeight = $this->changeDimension($options['contact-btn-height']);
		$sgContactBtnProgressTitle = $options['contact-btn-progress-title'];
		$sgContactPlaceholderColor = $options['contact-placeholder-color'];
		$sgContactButtonColor = $options['contact-button-color'];
		$sgContactButtonBgcolor = $options['contact-button-bgcolor'];
		$sgContactTextInputBgcolor = $options['contact-text-input-bgcolor'];
		$sgContactInputsBorderWidth = $this->changeDimension($options['contact-inputs-border-width']);
		$sgContactInputsColor = $options['contact-inputs-color'];
		$sgContactTextBordercolor = $options['contact-text-bordercolor'];
		$sgContactValidationMessage = $options['contact-validation-message'];
		$sgContactAreaWidth = $this->changeDimension($options['contact-area-width']);
		$sgContactAreaHeight = $this->changeDimension($options['contact-area-height']);
		$sgContactResize = $options['sg-contact-resize'];
		$sgSubsValidateMessage = $options['contact-validation-message'];
		$contactSuccessMessage = $options['contact-success-message'];
		$sgContactValidateEmail= $options['contact-validate-email'];
		$sgContactReceiveMail = $options['contact-receive-email'];
		$sgContactFailMessage = $options['contact-fail-message'];
		$sgContactNameStatus = $options['contact-name-status'];
		$sgContactSubjectStatus = $options['contact-subject-status'];
		$sgContactSubjectRequired = $options['contact-subject-required'];
		$sgContactNameRequired = $options['contact-name-required'];
		$showFormToTop = $options['show-form-to-top'];
		$submitButtonTitle = $options['contact-btn-title'];
		$sgContactSuccessBehavior = @$options['contact-success-behavior'];
		$sgContactSuccessRedirectUrl = @$options['contact-success-redirect-url'];
		$sgContactSuccessPopupsList = @$options['contact-success-popups-list'];
		$sgDontShowContentToContactedUser = @$options['dont-show-content-to-contacted-user'];
		$sgContactSuccessFrequencyDays = @$options['contact-success-frequency-days'];
		$contactSuccessRedirectNewTab = @$options['contact-success-redirect-new-tab'];

		$contactParams = array(
			'popupId' => $popupId,
			'popupTitle' => $popupTitle,
			'submitButtonTitle' => $submitButtonTitle,
			'inputsWidth' => $sgContactInputsWidth,
			'buttnsWidth' => $sgContactBtnWidth,
			'inputsHeight' => $sgContactInputsHeight,
			'buttonHeight' => $sgContactBtnHeight,
			'procesingTitle' => $sgContactBtnProgressTitle,
			'placeholderColor' => $sgContactPlaceholderColor,
			'btnTextColor' => $sgContactButtonColor,
			'btnBackgroundColor' => $sgContactButtonBgcolor,
			'inputsBackgroundColor' => $sgContactTextInputBgcolor,
			'inputsColor' => $sgContactInputsColor,
			'contactInputsBorderWidth' => $sgContactInputsBorderWidth,
			'ajaxurl' => admin_url( 'admin-ajax.php'),
			'contactAreaWidth' => $sgContactAreaWidth,
			'contactAreaHeight' => $sgContactAreaHeight,
			'contactResize' => $sgContactResize,
			'inputsBorderColor' => $sgContactTextBordercolor,
			'validateMessage' => $sgContactValidationMessage,
			'receiveEmail' => $sgContactReceiveMail,
			'sgContactNameStatus' => $sgContactNameStatus,
			'sgContactSubjectStatus' => $sgContactSubjectStatus,
			'sgContactNameRequired' => $sgContactNameRequired,
			'sgContactSubjectRequired' => $sgContactSubjectRequired,
			'sgContactSuccessBehavior' => $sgContactSuccessBehavior,
			'sgContactSuccessRedirectUrl' => $sgContactSuccessRedirectUrl,
			'sgContactSuccessPopupsList' => $sgContactSuccessPopupsList,
			'sgDontShowContentToContactedUser' => $sgDontShowContentToContactedUser,
			'sgContactSuccessFrequencyDays' => $sgContactSuccessFrequencyDays,
			'contactSuccessRedirectNewTab' => $contactSuccessRedirectNewTab,
		);

		if($sgContactSuccessBehavior == 'openPopup' && $sgContactSuccessPopupsList != $popupId) {
			$content = do_shortcode('[sg_popup id="'.$sgContactSuccessPopupsList.'" event="click"] [/sg_popup]');
			add_action('wp_footer', function() use ($content) { echo $content; }, 1);
		}
		$formElements .= '<div id="sg-contact-faild" class="js-sgpb-visibility sg-js-hide">'.$sgContactFailMessage.'</div>';
		$formElements .= '<form id="sg-contact-data" class="sgpb-contact-form"><div class="sg-contact-inputs-wrapper">';

		if($sgContactNameStatus) {
			$formElements .= '<input type="text" autocomplete="off" name="contact-name" class="sg-contact-required js-contact-text-inputs js-contact-name" value="" placeholder="'.esc_html($namePlaceholder).'">';
			$formElements .= '<span class=\'sg-js-hide js-validate-required js-requierd-style\'>'.$sgSubsValidateMessage.'</span>';
		}
		if($sgContactSubjectStatus) {
			$formElements .= '<input type="text"  autocomplete="off" name="contact-subject" class=" sg-contact-required js-contact-text-inputs js-contact-subject" value="" placeholder="'.esc_html($subjectPlaceholder).'">';
			$formElements .= '<span class=\'sg-js-hide js-validate-required js-requierd-style\'>'.$sgSubsValidateMessage.'</span>';
		}

		$formElements .= '<input type="text" autocomplete="off" name="contact-email" class="sg-contact-required js-contact-text-inputs js-contact-email" value="" placeholder="'.$emailPlaceholder.'">';
		$formElements .= '<div class="sg-js-hide js-validate-email js-validate-required">'.$sgContactValidateEmail.'</div>';
		$formElements .= '<span class=\'sg-js-hide js-validate-required js-requierd-style\'>'.$sgSubsValidateMessage.'</span>';
		$formElements .= '<textarea name="content-message"  autocomplete="off" placeholder="'.esc_html($messagePlaceholder).'" class="sg-contact-required js-contact-message js-contact-text-area"></textarea>';
		$formElements .= '<span class=\'sg-js-hide js-validate-required js-requierd-style\'>'.$sgSubsValidateMessage.'</span>';
		$formElements .= '<input type="hidden"  autocomplete="off" value="'.esc_html($ajaxNonce).'" class="js-contact-nonce-string">';
		$formElements .= '<div style="position: absolute;left: -5000px;"><input style="padding: 0;" type="text" name="sg-hidden-checker" value=""></div>';
		$formElements .= '<input type="submit"  autocomplete="off" value="'.esc_html($submitButtonTitle).'" data-value="'.esc_html($submitButtonTitle).'" class="js-contact-submit-btn">';
		$formElements .= '</div></form>';
		$formElements .= '<div id="sg-contact-success" class="sg-contact-success sg-js-hide">'.$contactSuccessMessage.'</div>';
		
		/* if not checked Form must be show to bottom text */
		if($showFormToTop == '') {
			$content = $content.$formElements;
		}
		else {
			$content = $formElements.$content;
		}
		$content .= "<style>
			.sg-current-popup-$popupId .js-contact-text-inputs {
				width: $sgContactInputsWidth !important;
				height: $sgContactInputsHeight !important;
				border-width: $sgContactInputsBorderWidth !important;
				color: $sgContactInputsColor !important;
				background-color: $sgContactTextInputBgcolor !important;
				border-color: $sgContactTextBordercolor !important;
			}
			
			.sg-current-popup-$popupId .js-contact-submit-btn {
				width: $sgContactBtnWidth !important;
				height: $sgContactBtnHeight !important;
				background-color: $sgContactButtonBgcolor !important;
				color: $sgContactButtonColor !important;
			}
			
			.sg-current-popup-$popupId .js-contact-text-area {
				width: $sgContactAreaWidth !important;
				height: $sgContactAreaHeight !important;
				background-color: $sgContactTextInputBgcolor !important;
				color: $sgContactInputsColor !important;
				border-color: $sgContactTextBordercolor !important;
				border-width: $sgContactInputsBorderWidth !important;
				margin: 2px auto !important;
			}
			
			.sg-current-popup-$popupId .sg-contact-inputs-wrapper {
				text-align: center;
			}
			.sg-current-popup-$popupId .js-contact-text-inputs {
				margin: 3px auto !important;
			}
			.sg-current-popup-$popupId .js-contact-submit-btn {
				border: none !important;
			}
			.sg-current-popup-$popupId .js-contact-submit-btn,
			.sg-current-popup-$popupId .js-contact-text-inputs {
				padding: 5px !important;
				box-sizing: border-box;
				font-size: 14px !important;
				border-radius: none !important;
				 box-shadow: none !important;
			}
			.sg-current-popup-$popupId .js-subs-submit-btn {
				border:0px !important;
				margin-bottom: 2px;
			}
			.sg-current-popup-$popupId .js-contact-text-inputs {
				margin-bottom: 8px;
			}
			.sg-current-popup-$popupId .sg-js-hide {
				display: none !important;
			}
			.sg-current-popup-$popupId #sg-contact-faild {
				border: 1px solid black;
				color: red;
				text-align: center;
				background-color: #F0EFEF;
				padding: 5px 0;
				width: $sgContactInputsWidth;
				margin: 5px auto;
			}
			.sg-current-popup-$popupId #sg-contact-success {
				border: 1px solid black;
				color: black;
				background-color: #F0EFEF;
				padding: 5px;
			}
			.sg-current-popup-$popupId .js-contact-text-area {
				padding: 0px !important;
				text-indent: 3px;
			}
			.sg-current-popup-$popupId .contact-content-wrapper {
			    margin: 0;
			    height: auto;
			    display: table;
			}
			.sg-current-popup-$popupId .js-validate-email,
			.sg-current-popup-$popupId .js-requierd-style {
				'margin': '0px auto 5px auto',
				'font-size': '12px',
				'color': 'red'
			}
			.sg-current-popup-$popupId .sg-contact-required {
				display: block;
				margin: 3px auto 3px auto;
			}
			.sg-current-popup-$popupId .js-contact-submit-btn {
				margin-bottom: 8px;
				line-height: 0px !important;
			}
			.sg-current-popup-$popupId .js-requierd-style {
				margin: 0px auto 5px auto;
				font-size: 12px;
				color: red;
				display: block;
			}
			.sg-current-popup-$popupId .js-contact-text-inputs::-webkit-input-placeholder, .sg-current-popup-$popupId .js-contact-text-area::-webkit-input-placeholder {color:".$sgContactPlaceholderColor.";}
			.sg-current-popup-$popupId .js-contact-text-inputs::-moz-placeholder,.sg-current-popup-$popupId .js-contact-text-area::-moz-placeholder {color:".$sgContactPlaceholderColor.";}
			.sg-current-popup-$popupId .js-contact-text-inputs:-ms-input-placeholder,.sg-current-popup-$popupId .js-contact-text-area:-ms-input-placeholder {color:".$sgContactPlaceholderColor.";} /* ie */
			.sg-current-popup-$popupId .js-contact-text-inputs:-moz-placeholder,.sg-current-popup-$popupId .js-contact-text-area:-moz-placeholder {color:".$sgContactPlaceholderColor.";}
		</style>";
		wp_enqueue_script('sg_contactForm', SG_APP_POPUP_URL . '/javascript/sg_contactForm.js', array( 'jquery' ));
		wp_localize_script('sg_contactForm', 'contactFrontend'.$popupId, $contactParams);

		$hasShortcode = $this->hasPopupContentShortcode($content);
		
		if($hasShortcode) {

			$content = $this->improveContent($content);
		}
		$this->sgAddPopupContentToFooter($content, $popupId);

		$content = trim($content);
		return array('html'=> $content);
	}

	public  function render()
	{
		return parent::render();
	}
}
