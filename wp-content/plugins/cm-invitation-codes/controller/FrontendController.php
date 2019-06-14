<?php

namespace com\cminds\registration\controller;

use com\cminds\registration\shortcode\LoginFormShortcode;

use com\cminds\registration\shortcode\RegistrationFormShortcode;

use com\cminds\registration\model\Settings;

use com\cminds\registration\App;

use com\cminds\registration\model\Labels;

class FrontendController extends Controller {
	
	static $actions = array(
		'wp_head',
		'wp_enqueue_scripts' => array('method' => 'includeAssets'),
		'login_enqueue_scripts' => array('method' => 'includeAssets'),
	);
	static $ajax = array('cmreg_login_overlay');
	
	
	static function includeAssets() {
		
		if (!App::isLicenseOk()) return;
		if (static::isAjax()) return;
		
		wp_enqueue_script('cmreg-frontend');
		wp_enqueue_style('cmreg-frontend');
		
		wp_localize_script('cmreg-frontend', 'CMREG_Settings', array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'isUserLoggedIn' => intval(is_user_logged_in()),
			'logoutUrl' => wp_logout_url(),
			'logoutButtonLabel' => Labels::getLocalized('logout_button'),
			'overlayPreload' => intval(Settings::getOption(Settings::OPTION_OVERLAY_PRELOAD)),
		));
		
	}
	
	
	static function wp_head() {
		
		echo '<style type="text/css">';
		if ($css = Settings::getOption(Settings::OPTION_CUSTOM_CSS)) {
			echo $css;
		}
		$opacity = Settings::getOption(Settings::OPTION_OVERLAY_OPACITY);
// 		var_dump($opacity);exit;
		if (!is_numeric($opacity) OR !$opacity) {
			$opacity = 70;
		}
		echo PHP_EOL;
		echo '.cmreg-overlay {background-color: rgba(0,0,0,'. ($opacity/100) .') !important;}' . PHP_EOL;
		echo '.cmreg-loader-overlay {background-color: rgba(0,0,0,'. ($opacity/100) .') !important;}' . PHP_EOL;
		echo '</style>';
	}
	
	
	static function getOverlayView($atts = array()) {
// 		$content = LoginController::getLoginFormView($atts) . RegistrationController::getRegistrationFormView($atts);
		$content = LoginFormShortcode::shortcode($atts) . RegistrationFormShortcode::shortcode($atts);
		return self::loadFrontendView('overlay', compact('content', 'atts'));
	}
	
	
	static function getLoginButton($loginButtonText, $atts, $extraClass = '') {
		if (is_user_logged_in()) {
			$loginButtonText = Labels::getLocalized('logout_button');
			$href = wp_logout_url();
		} else {
			if (empty($loginButtonText)) {
				$loginButtonText = Labels::getLocalized('login_button');
			}
			if (isset($atts['href'])) {
				$href = $atts['href'];
			} else {
				$href = '#';
			}
		}
		return self::loadFrontendView('login-button', compact('loginButtonText', 'atts', 'href', 'extraClass'));
	}
	
	
	static function cmreg_login_overlay() {
		echo self::getOverlayView();
		exit;
	}
	
	
}
