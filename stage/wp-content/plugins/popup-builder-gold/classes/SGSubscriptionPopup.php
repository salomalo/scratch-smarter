<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGSubscriptionPopup extends SGPopup
{
	public $content;
	public $subscriptionOptions;
	public $title;

	function __construct()
	{

	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setSubscriptionOptions($options)
	{
		$this->subscriptionOptions = $options;
	}

	public function getSubscriptionOptions()
	{
		return $this->subscriptionOptions;
	}

	public function setSubscriptionTitle($title)
	{
		$this->title = $title;
	}

	public function getSubscriptionTitle()
	{
		return $this->title;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();

		$title = $data['title'];
		$subscriptionOptions = $data['subscriptionOptions'];
		unset($data['subscriptionOptions']);

		$obj->setContent($data['sg_subscription']);
		$obj->setSubscriptionOptions($subscriptionOptions);
		$obj->setSubscriptionTitle($title);

		return parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;

		$content = $this->getContent();
		$options = $this->getSubscriptionOptions();

		global $wpdb;
		if ($editMode) {
			$content = stripslashes($content);
			$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."sg_subscription_popup SET content=%s, options=%s WHERE id=%d", $content, $options, $this->getId());
			$res = $wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."sg_subscription_popup (id, content, options) VALUES (%d, %s, %s)", $this->getId(), $content, $options);
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix."sg_subscription_popup WHERE id = %d", $id);
		$arr = $wpdb->get_row($st, ARRAY_A);
		$this->setContent($arr['content']);
		$this->setSubscriptionOptions($arr['options']);
	}

	public function getRemoveOptions()
	{
		return array();
	}

	protected function getExtraRenderOptions()
	{
		$ajaxNonce = wp_create_nonce("sgPopupBuilderSubsNonce");
		$popupId = $this->getId();
		$options = json_decode($this->getSubscriptionOptions(), true);
		$title = $this->getSubscriptionTitle();
		$textInputsWidth = $this->changeDimension($options['subs-text-width']);
		$emailPlaceholder = $options['subscription-email'];
		$sgSubsFirstName = $options['subs-first-name'];
		$sgSubsLastName = $options['subs-last-name'];
		$textInputsBgColor = $options['subs-text-input-bgColor'];
		$submitButtonBgColor = $options['subs-button-bgColor'];
		$subsButtonColor = $options['subs-button-color'];
		$sgSubsBtnTitle = $options['subs-btn-title'];
		$sgSubsTextBorderColor = $options['subs-text-borderColor'];
		$sgSubsInputsColor = $options['subs-inputs-color'];
		$sgSubsPlaceholderColor = $options['subs-placeholder-color'];
		$sgSubsTextHeight = $this->changeDimension($options['subs-text-height']);
		$sgSubsBtnWidth = $this->changeDimension($options['subs-btn-width']);
		$sgSubsBtnHeight = $this->changeDimension($options['subs-btn-height']);
		$sgSubsFirstNameStatus = $options['subs-first-name-status'];
		$sgSubsLastNameStatus = $options['subs-last-name-status'];
		$sgSubsValidateMessage = $options['subs-validation-message'];
		$sgSubsBtnProgressTitle = $options['subs-btn-progress-title'];
		$sgSubsSuccessMessage = $options['subs-success-message'];
		$sgSubsTextBorderWidth = $this->changeDimension($options['subs-text-border-width']);
		$subsSuccessBehavior = $options['subs-success-behavior'];
		$subsSuccessRedirectUrl = $options['subs-success-redirect-url'];
		$subsSuccessPopupsList = (int)$options['subs-success-popups-list'];
		$subsFirstNameRequired = $options['subs-first-name-required'];
		$subsLastNameRequired = $options['subs-last-name-required'];
		$subsSuccessRedirectNewTab = $options['subs-success-redirect-new-tab'];

		$subscriptionParams = array(
			"popupId" => $popupId,
			"ajaxurl" => admin_url( 'admin-ajax.php'),
			"textInputsWidth" => $textInputsWidth,
			"sgSubsBtnWidth" => $sgSubsBtnWidth,
			"sgSubsBtnHeight" => $sgSubsBtnHeight,
			"sgSubsTextHeight" => $sgSubsTextHeight,
			"textInputsBgColor" => $textInputsBgColor,
			"submitButtonBgColor" => $submitButtonBgColor,
			"sgSubsTextBorderColor" => $sgSubsTextBorderColor,
			"sgSubsInputsColor" => $sgSubsInputsColor,
			"subsButtonColor" => $subsButtonColor,
			"sgSubsBtnTitle" => $sgSubsBtnTitle,
			"sgSubsBtnProgressTitle" => $sgSubsBtnProgressTitle,
			"sgSubsTextBorderWidth" => $sgSubsTextBorderWidth,
			'subsSuccessBehavior' => $subsSuccessBehavior,
			'subsSuccessRedirectUrl' => $subsSuccessRedirectUrl,
			'subsSuccessPopupsList' => $subsSuccessPopupsList,
			'subsSuccessRedirectNewTab' => $subsSuccessRedirectNewTab
		);
		$subscriptionParamsStr = json_encode($subscriptionParams);

		$subscription = "<form id='sg-subscribers-data' class='sg-subscription-form' data-subs-params='$subscriptionParamsStr'><div class=\"sg-subs-inputs-wrapper\">";
		$subscription .= "<input type=\"text\" name='subs-email-name' class=\"js-subs-text-inputs js-subs-email-name sgpb-subs-required-field\" placeholder=\"".esc_attr($emailPlaceholder)."\"><br>";
		$subscription .= "<div class='sg-js-hide js-validate-email'>Invalid email</div>";
		$subscription .= "<div class='sg-js-hide js-validate-required js-validate-message'>$sgSubsValidateMessage</div>";
		if($sgSubsFirstNameStatus) {
			$firstNameRequiredClassName = '';
			if($subsFirstNameRequired) {
				$firstNameRequiredClassName = 'sgpb-subs-required-field';
			}
			$subscription .= "<input type=\"text\"  autocomplete=\"off\" name='subs-first-name' class=\"js-subs-first-name js-subs-text-inputs $firstNameRequiredClassName\" placeholder=\"".esc_attr($sgSubsFirstName)."\"><br>";
			$subscription .= "<div class='sg-js-hide js-validate-required js-validate-message'>$sgSubsValidateMessage</div>";
		}
		if($sgSubsLastNameStatus) {
			$lastNameRequiredClassName = '';
			if($subsLastNameRequired) {
				$lastNameRequiredClassName = 'sgpb-subs-required-field';
			}
			$subscription .= "<input type=\"text\"  autocomplete=\"off\" name='subs-last-name' class=\"js-subs-last-name js-subs-text-inputs $lastNameRequiredClassName\" placeholder=\"".esc_attr($sgSubsLastName)."\"><br>";
			$subscription .= "<div class='sg-js-hide js-validate-required js-validate-message'>$sgSubsValidateMessage</div>";
		}
		$subscription .= '<div style="position: absolute;left: -5000px;"><input type="text"  style="padding: 0;" name="sg-subs-hidden-checker" value=""></div>';
		$subscription .= "<input type=\"hidden\"  autocomplete=\"off\" name=\"subs-popup-title\" value=\"$title\">";
		$subscription .= '<input type="hidden"  autocomplete="off" value="'.esc_html($ajaxNonce).'" class="js-subs-nonce-string">';
		$subscription .= '<input type="submit"  autocomplete="off" value="Submit" class="js-subs-submit-btn">';
		$subscription .= "</div></form>";
		$subscription .= "<div class='sg-js-hide sg-subs-success'>".$sgSubsSuccessMessage."</div>";

		$content = $this->getContent();
		if($subsSuccessBehavior == 'openPopup' && $subsSuccessPopupsList != $popupId) {
			$childPopupContent = do_shortcode('[sg_popup id="'.$subsSuccessPopupsList.'" event="click"] [/sg_popup]');
			add_action('wp_footer', function() use ($childPopupContent) { echo $childPopupContent; }, 1);
		}

		$content .= $subscription;

		wp_enqueue_script('sg-subscription', SG_APP_POPUP_URL . '/javascript/sg_subscription.js', array( 'jquery' ), SG_POPUP_VERSION);
		wp_localize_script('sg-subscription', 'SgSubscriptionParams', $subscriptionParams);
		wp_enqueue_script('jquery');
		
		$content .= "<style>
			.sg-subs-inputs-wrapper {
				text-align: center;
			}
			.js-subs-text-inputs {
				width: $textInputsWidth !important;
				height: $sgSubsTextHeight !important;
				background-color: $textInputsBgColor !important;
				border-color: $sgSubsTextBorderColor !important;
				color: $sgSubsInputsColor !important;
				border-width: $sgSubsTextBorderWidth !important;
				max-width: 100% !important;
			}
			.js-subs-submit-btn {
				width: $sgSubsBtnWidth !important;
				height: $sgSubsBtnHeight !important;
				background-color: $submitButtonBgColor !important;
				color: $subsButtonColor !important;
				max-width: 100% !important;
			}
			
			.js-subs-submit-btn,
			.js-subs-text-inputs {
				padding: 5px !important;
				box-sizing: border-box;
				font-size: 14px !important;
				border-radius: 0 !important;
				box-shadow: none !important;
				margin: 2px auto !important;
				display: inline-block !important;
			}
			.js-subs-submit-btn {
				border:0 !important;
				margin-bottom: 2px;
				line-height: 0.4 !important;
			}
			.js-subs-text-inputs {
				margin-bottom: 8px;
			}
			.sg-js-hide {
				display: none;
			}
			.js-validate-required {
				width: $textInputsWidth !important;
				font-size: 12px;
				margin: 0px auto 5px auto;
				color: red;
			}
			.sg-subs-success {
				border: 1px solid black;
				color: black;
				background-color: #F0EFEF;
				padding: 5px;
			}
			.js-subs-text-inputs::-webkit-input-placeholder {color:".$sgSubsPlaceholderColor.";}
			.js-subs-text-inputs::-moz-placeholder {color:".$sgSubsPlaceholderColor.";}
			.js-subs-text-inputs:-ms-input-placeholder {color:".$sgSubsPlaceholderColor.";} /* ie */
			.js-subs-text-inputs:-moz-placeholder {color:".$sgSubsPlaceholderColor.";}
		</style>";

		$hasShortcode = $this->hasPopupContentShortcode($content);

		if($hasShortcode) {
			$content = $this->improveContent($content);
		}

		$this->sgAddPopupContentToFooter($content, $popupId);
		$content = trim($content);

		return array('html'=>$content);
	}

	public  function render()
	{
		return parent::render();
	}
}
