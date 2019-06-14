<?php

namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\RegistrationController;

use com\cminds\registration\controller\LoginController;

use com\cminds\registration\model\Settings;

use com\cminds\registration\model\Labels;

use com\cminds\registration\controller\FrontendController;

class RegistrationFormShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-registration-form';
	
	
	static function shortcode($atts, $text = '') {
		
		$atts = shortcode_atts(array(
			'role' => '',
			'login-url' => '',
			'login-link' => '',
			'social-login' => (Settings::getOption(Settings::OPTION_REGISTER_SHOW_SOCIAL_LOGIN_BUTTONS) ? 1 : 0),
		), $atts);
		
		if (!is_user_logged_in()) {
			return RegistrationController::getRegistrationFormView($atts);
		} else {
			echo $text;
		}
	}
	
	
}
