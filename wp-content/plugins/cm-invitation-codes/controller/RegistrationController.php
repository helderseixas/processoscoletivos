<?php

namespace com\cminds\registration\controller;

use com\cminds\registration\helper\Nonce;

use com\cminds\registration\App;

use com\cminds\registration\lib\Email;

use com\cminds\registration\model\Labels;

use com\cminds\registration\model\Settings;

use com\cminds\registration\model\User;

class RegistrationController extends Controller {
	
	const NONCE_REGISTRATION = 'cmreg_registration_nonce';
	const FIELD_PASS = 'cmregpw';
	const FIELD_REPEAT_PASS = 'cmregpwrepeat';
	
	static $actions = array(
		'register_new_user' => array('args' => 1, 'priority' => 500),
	);
	static $ajax = array('cmreg_registration');
	
	
	
	static function getRegistrationFormView($atts) {
		$nonce = Nonce::create(static::NONCE_REGISTRATION);
		$roleNonce = (empty($atts['role']) ? '' : Nonce::create(static::NONCE_REGISTRATION . $nonce . $atts['role']));
		return self::loadFrontendView('registration-form', compact('nonce', 'atts', 'roleNonce'));
	}
	

	static function cmreg_registration() {
		
		if (!App::isLicenseOk()) return;
		
		$userId = null;
		$response = array('success' => false, 'msg' => Labels::getLocalized('register_error_msg'));
		
		if (static::isRegistrationAction() AND !empty($_POST['email']) AND !empty($_POST[static::FIELD_PASS])) {
			
			$login = (empty($_POST['login']) ? '' : $_POST['login']);
			$email = (is_email($_POST['email']) ? $_POST['email'] : null);
			$displayName = (!empty($_POST['display_name']) ? $_POST['display_name'] : (!empty($_POST['login']) ? $_POST['login'] : $email));
			$role = filter_input(INPUT_POST, 'role');
			
			try {
				
				if (!static::verifyRoleNonce()) {
					throw new \Exception(Labels::getLocalized('register_role_nonce_error'));
				}
				
				if (Settings::getOption(Settings::OPTION_REGISTER_REPEAT_PASS_ENABLE)) {
					if (empty($_POST[static::FIELD_REPEAT_PASS]) OR $_POST[static::FIELD_REPEAT_PASS] != $_POST[static::FIELD_PASS]) {
						throw new \Exception(Labels::getLocalized('register_repeat_pass_error_msg'));
					}
				}
				
				// The email and other params will be validated inside this method and throw exception if invalid:
				
				$userId = User::register($email, $_POST[static::FIELD_PASS], $login, $displayName, $role);
				$user = get_userdata($userId);
				
				$response = array(
					'success' => true,
					'msg' => Labels::getLocalized('register_success_msg'),
				);
				
				$canLogin = apply_filters('cmreg_user_can_login', true, $userId);
				if ($canLogin) {
					try {
						User::login($email, $_POST[static::FIELD_PASS], false);
						if ($urlAfterLogin = LoginController::getLoginRedirectUrl($user)) {
							$response['redirect'] = $urlAfterLogin;
						} else {
							$response['redirect'] = 'reload';
						}
					} catch (\Exception $e) {
						
					}
				} else {
					User::logout();
					
				}
				
// 				$status = User::getEmailVerificationStatus($userId);
// 				if (User::EMAIL_VERIFICATION_STATUS_PENDING == $status) {
// 					User::logout();
// 					$response = array(
// 						'success' => true,
// 						'msg' => Labels::getLocalized('register_verification_msg'),
// 					);
// 				} else {
// 					User::login($email, $_POST[static::FIELD_PASS], false);
// 					$urlAfterLogin = LoginController::getLoginRedirectUrl($user);
// 					$response = array(
// 						'success' => true,
// 						'msg' => Labels::getLocalized('register_success_msg'),
// 						'redirect' => (empty($urlAfterLogin) ? 'reload' : $urlAfterLogin),
// 					);
// 				}
				
			} catch (\Exception $e) {
				$response['msg'] = $e->getMessage();
			}
			
		}
// 		var_dump($response);
// 		exit;

		$response = apply_filters('cmreg_registration_ajax_response', $response, $userId);
		
		header('content-type: application/json');
		echo json_encode($response);
		exit;
	}
	
	
	static function verifyRoleNonce() {
		$role = filter_input(INPUT_POST, 'role');
		$roleNonce = filter_input(INPUT_POST, 'role_nonce');
		$nonce = filter_input(INPUT_POST, 'nonce');
		if (!empty($role) AND (empty($roleNonce) OR !Nonce::verify(static::NONCE_REGISTRATION . $nonce . $role, $roleNonce))) {
			return false;
		} else {
			return true;
		}
	}
	
	
	static function isRegistrationAction() {
		return (isset($_POST['nonce'])
// 			AND wp_verify_nonce($_POST['nonce'], static::NONCE_REGISTRATION)
			AND Nonce::verify(static::NONCE_REGISTRATION, $_POST['nonce'])
		);
	}
	
	
	/**
	 * After successful registration
	 * 
	 * @param unknown $userId
	 */
	static function register_new_user($userId) {
		if (!App::isLicenseOk()) return;
		if (User::EMAIL_VERIFICATION_STATUS_VERIFIED == User::getEmailVerificationStatus($userId)) {
			Email::sendWelcomeEmail($userId);
		}
	}
	
	
	static function getWelcomeUrl() {
		if ($pageId = Settings::getOption(Settings::OPTION_REGISTER_WELCOME_PAGE)) {
			return get_permalink($pageId);
		} else {
			return site_url();
		}
	}
	
	
}
