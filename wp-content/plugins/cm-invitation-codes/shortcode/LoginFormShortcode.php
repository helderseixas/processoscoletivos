<?php

namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\LoginController;

use com\cminds\registration\model\Settings;

use com\cminds\registration\model\Labels;

use com\cminds\registration\controller\FrontendController;

class LoginFormShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-login-form';
	
	
	static function shortcode($atts, $text = '') {
		
		$atts = shortcode_atts(array(
			'registration-url' => '',
			'registration-link' => '',
			'social-login' => (Settings::getOption(Settings::OPTION_LOGIN_SHOW_SOCIAL_LOGIN_BUTTONS) ? 1 : 0),
		), $atts);
		
		if (!is_user_logged_in()) {
			return LoginController::getLoginFormView($atts);
		} else {
			echo $text;
		}
	}
	
	
}
