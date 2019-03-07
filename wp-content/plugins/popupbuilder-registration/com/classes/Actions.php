<?php
namespace sgpbregistration;

class Actions
{
	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		add_action('wp_head', array($this, 'hideShowForLoggedInUsers'), 100);
	}

	public function hideShowForLoggedInUsers()
	{
		$hideForloggedInUsers = 'display: inherit;';
		$showForloggedInUsers = 'display: none;';

		if (is_user_logged_in()) {
			$hideForloggedInUsers = 'display: none;';
			$showForloggedInUsers = 'display: inherit;';
		}

		$content = '<style>';
		$content .= '.sgpb-hide-for-loggdin {';
		$content .= $hideForloggedInUsers.'';
		$content .= '}';
		$content .= '.sgpb-show-for-loggdin {';
		$content .= $showForloggedInUsers.'';
		$content .= '}';
		$content .= '</style>';

		echo $content;
	}
}
