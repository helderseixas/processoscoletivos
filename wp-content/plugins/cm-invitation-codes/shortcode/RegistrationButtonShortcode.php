<?php

namespace com\cminds\registration\shortcode;

use com\cminds\registration\model\Settings;

use com\cminds\registration\model\Labels;

use com\cminds\registration\controller\FrontendController;

class RegistrationButtonShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-registration-btn';
	
	
	static function shortcode($atts, $buttonText = null) {
		if (empty($buttonText)) {
			$buttonText = 'Registration';
		}
		if (!is_user_logged_in()) {
			$atts['href'] = '#cmreg-only-registration-click';
			return FrontendController::getLoginButton($buttonText, $atts);
		}
	}
	
	
}
