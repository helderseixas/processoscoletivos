<?php

namespace com\cminds\registration\shortcode;

use com\cminds\registration\model\Settings;

use com\cminds\registration\model\Labels;

use com\cminds\registration\controller\FrontendController;

class LoginAndRegisterButtonShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-login';
	
	
	static function shortcode($atts, $loginButtonText = null) {
		return FrontendController::getLoginButton($loginButtonText, $atts);
	}
	
	
}
