<?php

namespace com\cminds\registration\controller;

use com\cminds\registration\App;

use com\cminds\registration\model\Labels;

use com\cminds\registration\model\User;

use com\cminds\registration\model\Settings;

class LoginController extends Controller {
	
	const LOGIN_NONCE = 'cmreg_login_nonce';
	const LOST_PASS_NONCE = 'cmreg_lost_pass_nonce';
	const FIELD_PASS = 'cmregpw';
	
	static $actions = array(
		'wp_logout',
		'login_form_login',
		'login_form_lostpassword' => array('method' => 'login_form_login'),
		'login_form_register',
	);
	static $filters = array(
		'wishlistmember_login_redirect_override', // Fix conflict with WishList Member
	);
	static $ajax = array('cmreg_login', 'cmreg_lost_password');
	
	
	
	static function getLoginFormView($atts = array()) {
		if (!App::isLicenseOk()) return;
		FrontendController::includeAssets();
		$nonce = wp_create_nonce(self::LOGIN_NONCE);
		return self::loadFrontendView('login-form', compact('atts', 'nonce'));
	}
	
	
	static function getLostPasswordView($atts = array()) {
		return self::loadFrontendView('lost-password', $atts);
	}
	
	
	static function cmreg_login() {
		
		if (!App::isLicenseOk()) return;
		
		$response = array('success' => false, 'msg' => Labels::getLocalized('login_error_msg'));
		
		// Fix for S2Member Pro
// 		register_shutdown_function(function() use (&$response) {
// 			header('content-type: application/json');
// 			echo json_encode($response);
// 			exit;
// 		});
		
		if (isset($_POST['nonce']) AND wp_verify_nonce($_POST['nonce'], self::LOGIN_NONCE) AND !empty($_POST['login']) AND !empty($_POST[static::FIELD_PASS])) {
			$remember = (Settings::getOption(Settings::OPTION_LOGIN_REMEMBER_ENABLE) AND !empty($_POST['remember']));
			$redirectUrl = filter_var(filter_input(INPUT_POST, 'cmreg_redirect_url'), FILTER_VALIDATE_URL);
			try {
				$user = User::login($_POST['login'], $_POST[static::FIELD_PASS], $remember);
				$response = array(
					'success' => true,
					'msg' => Labels::getLocalized('login_success_msg'),
					'redirect' => static::getLoginRedirectUrl($user, $redirectUrl),
				);
			} catch (\Exception $e) {
				$response['msg'] = $e->getMessage();
			}
		}
		
		$response = apply_filters('cmreg_login_ajax_response', $response);
		
		header('content-type: application/json');
		echo json_encode($response);
		exit;
		
	}
	
	
	static function getLoginRedirectUrl(\WP_User $user, $url = null) {
		if (empty($url)) {
			$url = Settings::getOption(Settings::OPTION_LOGIN_REDIRECT_URL);
		}
		if ($user) {
			$url = str_replace('%userlogin%', $user->user_login, $url);
			$url = str_replace('%usernicename%', $user->user_nicename, $url);
		}
		return $url;
	}
	
	
	static function wp_logout() {
		if ($url = Settings::getOption(Settings::OPTION_LOGOUT_REDIRECT_URL)) {
			wp_redirect($url);
			exit;
		}
	}
	
	
	static function cmreg_lost_password() {
		if (!App::isLicenseOk()) return;
		$response = array('success' => false, 'msg' => Labels::getLocalized('lost_pass_error_msg'));
		if (Settings::getOption(Settings::OPTION_LOGIN_LOST_PASSWORD_ENABLE)) {
			if (isset($_POST['nonce']) AND wp_verify_nonce($_POST['nonce'], self::LOST_PASS_NONCE) AND !empty($_POST['email'])) {
				if ($user = get_user_by('email', $_POST['email']) AND !is_wp_error($user)) {
					if (User::lostPasswordEmail($user) === true) {
						$response = array('success' => true, 'msg' => Labels::getLocalized('lost_pass_email_sent_msg'));
					} else $response['error'] = 'cannot_send_email';
				} else $response['error'] = 'user_not_found';
			} else $response['error'] = 'invalid_nonce';
		} else $response['error'] = 'feature_disabled';
		header('content-type: application/json');
		echo json_encode($response);
		exit;
	}
	
	
	static function debugActions() {
		global $wp_filter;
		echo '<pre>';
		foreach ($wp_filter as $actionName => $names) {
			foreach ($names as $priority => $filters) {
				foreach ($filters as $name => $filter) {
					echo PHP_EOL . '-----------------------------' . PHP_EOL;
					echo $actionName . PHP_EOL;
					// 					echo '-----------------------------' . PHP_EOL;
					if (is_array($filter['function'])) {
						if (is_object($filter['function'][0])) {
							echo get_class($filter['function'][0]);
						} else {
							echo $filter['function'][0];
						}
						echo $filter['function'][1];
					} else {
						var_dump($filter['function']);
					}
					echo PHP_EOL;
				}
			}
		}
	}
	
	
	static function login_form_login() {
		if ($url = Settings::getOption(Settings::OPTION_WP_LOGIN_PAGE_REDIRECTION_URL)) {
			if (!isset($_REQUEST['interim-login'])) {
				wp_redirect($url);
				exit;
			}
		}
	}
	
	
	static function login_form_register() {
		if ($url = Settings::getOption(Settings::OPTION_WP_REGISTER_PAGE_REDIRECTION_URL)) {
			wp_redirect($url);
			exit;
		}
	}
	
	
	/**
	 * Fix conflict with the WishList Member plugin
	 * @param boolean $result
	 * @return boolean
	 */
	static function wishlistmember_login_redirect_override($result) {
		return true;
	}
	
	
	static function authenticateAddError($errorCode, $msg, &$user) {
		if (is_wp_error($user)) {
			$user->add($errorCode, $msg);
		} else {
			$user = new \WP_Error($errorCode, $msg);
		}
	}
	
	
}
