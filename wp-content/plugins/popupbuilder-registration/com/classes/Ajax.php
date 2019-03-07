<?php
namespace sgpbregistration;

class Ajax
{
	public function __construct()
	{
		$this->init();
	}

	private function init()
	{
		add_action('wp_ajax_sgpb_register_action', array($this, 'registerAction'));
		add_action('wp_ajax_nopriv_sgpb_register_action', array($this, 'registerAction'));
	}

	public function registerAction()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		$userForm = $_POST['userForm'];
		parse_str($userForm, $userForm);

		$result = array(
			'status' => 200,
			'message' => __('You have successful registered.', SG_POPUP_TEXT_DOMAIN)
		);

		$userName = sanitize_text_field($_POST['userName']);
		$emailName = sanitize_text_field($_POST['emailName']);
		$passwordName = sanitize_text_field($_POST['passwordName']);

		$userLoginValue = sanitize_text_field($userForm[$userName]);
		$password = sanitize_text_field($userForm[$passwordName]);
		$email = sanitize_text_field($userForm[$emailName]);

		$registrationResult = wp_create_user($userLoginValue, $password, $email);

		if (is_wp_error($registrationResult)) {
			$result['status'] = 400;
			$result['message'] = __('unable to register', SG_POPUP_TEXT_DOMAIN);
			echo json_encode($result);
			wp_die();
		}

		echo json_encode($result);
		wp_die();
	}
}
