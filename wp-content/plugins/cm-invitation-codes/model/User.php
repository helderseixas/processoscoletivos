<?php

namespace com\cminds\registration\model;

use com\cminds\registration\controller\RegistrationController;

use com\cminds\registration\App;

class User extends Model {
	
	const ROLE_S2MEMBERS_LEVEL_PREFIX = 's2member_level';
	const META_INVITATION_CODE = 'cmreg_invitation_code';
	const META_EMAIL_VERIFICATION_STATUS = 'cmreg_email_verification_status';
	const META_EMAIL_VERIFICATION_CODE = 'cmreg_email_verification_code';
	const META_SOCIAL_LOGIN_UID_PREFIX = 'cmreg_social_login_uid_';
	
	const EMAIL_VERIFICATION_STATUS_PENDING = 'pending';
	const EMAIL_VERIFICATION_STATUS_VERIFIED = 'verified';
	
	const EMAIL_VERIFICATION_CODE_LENGTH = 30;
	
	const COOKIE_LAST_ACTIVITY = 'cmreg_last_activity';
	
	static protected $lastActivity = 0;
	
	
	static function register($email, $password, $login, $displayName, $role = null) {
		
		// Fix for S2Member Pro - prevent from redirection
		add_filter('wp_redirect', function($location, $status) { return null; }, PHP_INT_MAX, 2);
		
		$email = trim($email);
		
		if ( empty($email) OR ! is_email( $email ) ) {
			throw new \Exception(Labels::getLocalized('register_invalid_email_error_msg'));
		}
		if ( email_exists( $email ) ) {
			throw new \Exception(Labels::getLocalized('register_email_exists_error_msg'));
		}
		if ( username_exists( $login ) ) {
			throw new \Exception(Labels::getLocalized('register_login_exists_error_msg'));
		}
		if (empty($password)) {
			throw new \Exception(Labels::getLocalized('register_empty_pass_error_msg'));
		}
		if (Settings::getOption(Settings::OPTION_REGISTER_STRONG_PASS_ENABLE)) {
			if (!preg_match(Settings::STRONG_PASSWORD_REGEXP, $password)) {
				throw new \Exception(Labels::getLocalized('register_weak_pass_error_msg'));
			}
		}
		
		if (empty($login)) $login = $email;
		$sanitized_user_login = sanitize_user( $login );
		
// 		var_dump(__METHOD__);
		
		$errors = new \WP_Error();
		do_action( 'register_post', $sanitized_user_login, $email, $errors );
// 		var_dump($errors);
		if ($errors->get_error_code()) {
			throw new \Exception($errors->get_error_message());
		}
		
		$user_data = array(
			'user_login'    => $sanitized_user_login,
			'user_email'    => $email,
			'display_name'  => $displayName,
			'user_pass'     => $password,
			'first_name'    => $displayName,
			'last_name'     => '',
			'nickname'      => $displayName,
		);
		
		$userId = wp_insert_user( $user_data );
		if (is_wp_error($userId)) {
			throw new \Exception($userId->get_error_message());
		} else {
			if (empty($role)) {
				$role = Settings::getOption(Settings::OPTION_REGISTER_DEFAULT_ROLE);
			}
			User::setUserRole($userId, $role);
		}
		
		if (Settings::getOption(Settings::OPTION_REGISTER_NOTICE_ADMIN_ENABLE)) {
			wp_new_user_notification( $userId, null, 'admin' );
		}

// 		RegistrationController::register_new_user($userId);
		
		if (Settings::getOption(Settings::OPTION_REGISTER_PRVENT_SYSTEM_EMAIL)) {
			remove_action('register_new_user', 'wp_send_new_user_notifications');
		}
		
		do_action( 'register_new_user', $userId );
		
		return $userId;
	}
	
	
	
	
	static function login($login, $password, $remember) {
		
		// Fix for JetPack - prevent from protection and login cancelation
		if (class_exists('\Jetpack_Protect_Module')) {
			$jetpack = call_user_func(array('\Jetpack_Protect_Module', 'instance'));
			remove_filter('authenticate', array($jetpack, 'check_preauth'), 10);
		}
		// Fix for S2Member Pro - prevent from redirection
		remove_action('wp_login', 'c_ws_plugin__s2member_login_redirects::login_redirect', 10);
// 		add_filter('wp_redirect', function($location, $status) { return null; }, PHP_INT_MAX, 2);
		
		switch (Settings::getOption(Settings::OPTION_LOGIN_FIELD)) {
			case Settings::LOGIN_FIELD_EMAIL:
				$user = get_user_by('email', $login);
				break;
			case Settings::LOGIN_FIELD_LOGIN:
				$user = get_user_by('login', $login);
				break;
			case Settings::LOGIN_FIELD_BOTH:
			default:
				$user = get_user_by('login', $login);
				if (empty($user) OR is_wp_error($user)) {
					$user = get_user_by('email', $login);
				}
		}
		
// 		if ($user AND !is_wp_error($user)) {
			$info = array();
			$info['user_login'] = (($user AND !is_wp_error($user)) ? $user->user_login : $login);
			$info['user_password'] = $password;
			$info['remember'] = $remember;
			$user_signon = wp_signon( $info, false );
			if ( !is_wp_error($user_signon) ){
				static::updateLastActivity();
				return $user_signon;
			} else {
				$key = 'login_error_'. $user_signon->get_error_code();
				$label = Labels::getLocalized($key);
				if ($key == $label) {
					$label = $user_signon->get_error_message();
// 					$label = Labels::getLocalized('login_error_msg');
				}
				throw new \Exception($label);
			}
// 		} else {
// 			/**
// 			 * @var $user \WP_Error
// 			 */
// // 			throw new \Exception($user->get_error_message());
// 			throw new \Exception(Labels::getLocalized('login_error_msg'));
// 		}
	}
	
	
	/**
	 * Handles sending password retrieval email to user.
	 *
	 * @global wpdb         $wpdb      WordPress database abstraction object.
	 * @global PasswordHash $wp_hasher Portable PHP password hashing framework.
	 *
	 * @return bool|WP_Error True: when finish. WP_Error on error
	 */
	static function lostPasswordEmail($user_data) {
		global $wpdb, $wp_hasher;
	
		$errors = new \WP_Error();
		
		/**
		 * Fires before errors are returned from a password reset request.
		 *
		 * @since 2.1.0
		 * @since 4.4.0 Added the `$errors` parameter.
		 *
		 * @param WP_Error $errors A WP_Error object containing any errors generated
		 *                         by using invalid credentials.
		 */
		do_action( 'lostpassword_post', $errors );
	
		if ( $errors->get_error_code() )
			return $errors;
	
		if ( !$user_data ) {
			$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.'));
			return $errors;
		}
	
		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key = get_password_reset_key( $user_data );
	
		if ( is_wp_error( $key ) ) {
			return $key;
		}
	
		$message = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
		$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
	
		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			/*
			 * The blogname option is escaped with esc_html on the way into the database
		* in sanitize_option we want to reverse this for the plain text arena of emails.
		*/
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
		$title = sprintf( __('[%s] Password Reset'), $blogname );
	
		/**
		 * Filter the subject of the password reset email.
		 *
		 * @since 2.8.0
		 * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $title      Default email title.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		*/
		$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
	
		/**
		 * Filter the message body of the password reset mail.
		 *
		 * @since 2.8.0
		 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $message    Default mail message.
		 * @param string  $key        The activation key.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		*/
		$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
	
		if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
			throw new \Exception( __('The email could not be sent.') );
	
		return true;
	}
	
	
	static function roleExists( $role ) {
	
		if( ! empty( $role ) ) {
			return $GLOBALS['wp_roles']->is_role( $role );
		}
	
		return false;
	}
	
	
	static function getEmailVerificationStatus($userId) {
		if (!App::isPro()) return self::EMAIL_VERIFICATION_STATUS_VERIFIED;
		$val = get_user_meta($userId, self::META_EMAIL_VERIFICATION_STATUS, $single = true);
		if ($val != self::EMAIL_VERIFICATION_STATUS_PENDING) {
			$val = self::EMAIL_VERIFICATION_STATUS_VERIFIED;
		}
		return $val;
	}
	
	
	static function generateEmailVerificationCode($userId) {
		$code = static::generateRandomString(static::EMAIL_VERIFICATION_CODE_LENGTH);
		update_user_meta($userId, self::META_EMAIL_VERIFICATION_CODE, $code);
		update_user_meta($userId, self::META_EMAIL_VERIFICATION_STATUS, self::EMAIL_VERIFICATION_STATUS_PENDING);
		clean_user_cache($userId);
		return $code;
	}
	
	
	static function verifyEmail($code) {
		if ($userId = self::getUserByVerificationCode($code)) {
			update_user_meta($userId, self::META_EMAIL_VERIFICATION_CODE, '');
			update_user_meta($userId, self::META_EMAIL_VERIFICATION_STATUS, self::EMAIL_VERIFICATION_STATUS_VERIFIED);
			clean_user_cache($userId);
			return true;
		} else {
			return false;
		}
	}
	
	
	static function getEmailVerificationCode($userId) {
		return get_user_meta($userId, self::META_EMAIL_VERIFICATION_CODE, $single = true);
	}
	
	
	static function getUserByVerificationCode($code) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users u
			JOIN $wpdb->usermeta m ON u.ID = m.user_id AND m.meta_key = %s
			WHERE m.meta_value = %s",
			self::META_EMAIL_VERIFICATION_CODE,
			$code
		));
	}
	
	
	
	static function deleteInactiveUsers() {
		global $wpdb;
		$days = Settings::getOption(Settings::OPTION_REGISTER_DAYS_FOR_VERIFICATION);
		$date = Date('Y-m-d H:i:s', time() - $days * 3600 * 24);
		$usersIds = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->users u
			JOIN $wpdb->usermeta m ON u.ID = m.user_id AND m.meta_key = %s
			WHERE m.meta_value = %s AND u.user_registered < %s",
			self::META_EMAIL_VERIFICATION_STATUS,
			self::EMAIL_VERIFICATION_STATUS_PENDING,
			$date
		));
// 		var_dump($usersIds);
// 		return;

		require_once(ABSPATH.'wp-admin/includes/user.php' );
		foreach ($usersIds as $id) {
			$res = \wp_delete_user($id);
		}
		
	}
	
	
	static function generateRandomString($len = 20) {
		$str = '';
		while (strlen($str) < $len) {
			$str .= base_convert(floor(mt_rand(0, PHP_INT_MAX)), 10, 26);
		}
		return substr($str, 0, $len);
	}
	
	
	static function setUserRole($userId, $role) {
		if (User::roleExists($role)) {
			$wp_user_object = new \WP_User($userId);
			$wp_user_object->set_role($role);
		}
	}
	
	
	
	static function logout() {
		wp_destroy_current_session();
		wp_clear_auth_cookie();
	}
	
	
	static function updateLastActivity() {
		if (session_id()) {
			$_SESSION[self::COOKIE_LAST_ACTIVITY] = time();
		}
	}
	
	
	static function getLastActivity() {
		return (isset($_SESSION[self::COOKIE_LAST_ACTIVITY]) ? $_SESSION[self::COOKIE_LAST_ACTIVITY] : null);
	}
	
	
	static function setExtraField($userId, $name, $value) {
		update_user_meta($userId, $name, $value);
	}
	
	
	static function getExtraField($userId, $name) {
		return get_user_meta($userId, $name, $single = true);
	}
	
	
	static function getAllExtraFields($userId) {
		
		$result = array();
		$fields = Settings::getOption(Settings::OPTION_REGISTER_EXTRA_FIELDS);
		if (is_array($fields)) foreach ($fields as $i => $field) {
			if ($i == 0) continue;
			$result[$field['meta_name']] = User::getExtraField($userId, $field['meta_name']);
		}
		
		return $result;
	}
	
	
	static function hasRole($role, $userId = null) {
		if (empty($userId)) $userId = get_current_user_id();
		if ($userId AND $user = get_userdata($userId)) {
			if (is_array($role)) $roles = $role;
			else $roles = array($role);
			$inner = array_intersect($roles, $user->roles);
			return !empty($inner);
		}
		return false;
	}
	
	
	static function hasCapability($cap, $userId = null) {
		if (empty($userId)) $userId = get_current_user_id();
		return user_can($userId, $cap);
	}
	
	
	static function updateUserData($userdata) {
		if (empty($userdata->display_name)) {
			throw new \Exception('Display name cannot be empty.');
		}
		if (empty($userdata->user_email)) {
			throw new \Exception('Email cannot be empty.');
		}
		if (!is_email($userdata->user_email)) {
			throw new \Exception('Invalid email address.');
		}
		wp_update_user($userdata);
	}
	
	
	static function getUserData($userId = null) {
		if (is_null($userId)) $userId = get_current_user_id();
		return get_userdata($userId);
	}
	
	
	static function setSocialLoginUID($userId, $provider, $uid) {
		$key = static::META_SOCIAL_LOGIN_UID_PREFIX . $provider;
		return update_user_meta($userId, $key, $uid);
	}
	
	
	static function getBySocialLoginUID($provider, $uid) {
		global $wpdb;
		
		$key = static::META_SOCIAL_LOGIN_UID_PREFIX . $provider;
		return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users u
			JOIN $wpdb->usermeta m ON u.ID = m.user_id AND m.meta_key = %s
			WHERE m.meta_value = %s",
			$key, $uid));
		
	}
	
	
	static function getByEmail($email) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users u WHERE user_email = %s", $email));
	}
	
	
	static function loginById($userId) {
		wp_clear_auth_cookie();
		wp_set_auth_cookie($userId);
	}
	
	
	static function setPassword($userId, $password) {
		
		if (Settings::getOption(Settings::OPTION_REGISTER_STRONG_PASS_ENABLE)) {
			if (!preg_match(Settings::STRONG_PASSWORD_REGEXP, $password)) {
				throw new \Exception(Labels::getLocalized('register_weak_pass_error_msg'));
			}
		}
		
		$userdata = array();
		$userdata['ID'] = $userId; //user ID
		$userdata['user_pass'] = $password;
		return wp_update_user( $userdata );
		
	}
	
	
}
