<?php

namespace com\cminds\registration\model;

use com\cminds\registration\lib\Email;

use com\cminds\registration\App;

class Settings extends SettingsAbstract {

	public static $categories = array(
		'general' => 'General',
		'login' => 'Login',
		'register' => 'Registration',
// 		'fields' => 'Custom fields',
	);
	
	public static $subcategories = array(
		'general' => array(
			'general' => 'General',
			'api' => 'API Keys',
			'logout' => 'Logout',
			'appearance' => 'Appearance',
		),
		'login' => array(
			'login' => 'Login',
			'social-login' => 'Social Login',
			'limit' => 'Limit login attempts',
			'ip' => 'IP restrictions',
		),
		'register' => array(
			'register' => 'Registration',
			'pass' => 'Password',
			'verification' => 'Email verification',
			'ip' => 'IP restrictions',
			's2member' => 'S2Member Pro integration',
		),
// 		'fields' => array(
// 			'fields' => 'Custom fields',
// 		),
		'email' => array(
			'general' => 'General email settings',
			'welcome' => 'Welcome email',
			'activation' => 'Activation email',
			'admin' => 'Administrator notifications',
		),
		'labels' => array(
			'other' => 'Other',
		),
	);
	
	const OPTION_CUSTOM_CSS = 'cmreg_custom_css';
	const OPTION_LOGIN_FIELD = 'cmreg_login_field';
	const OPTION_WP_LOGIN_PAGE_REDIRECTION_URL = 'cmreg_wp_login_page_redirection_url';
	const OPTION_LOGIN_REDIRECT_URL = 'cmreg_login_redirect_url';
	const OPTION_LOGIN_REMEMBER_ENABLE = 'cmreg_login_remember_enable';
	const OPTION_LOGIN_LOST_PASSWORD_ENABLE = 'cmreg_login_lost_password_enable';
	const OPTION_RECAPTCHA_API_SITE_KEY = 'cmreg_recaptcha_api_site_key';
	const OPTION_RECAPTCHA_API_SECRET_KEY = 'cmreg_recaptcha_api_secret_key';
	const OPTION_LOGOUT_REDIRECT_URL = 'cmreg_logout_redirect_url';
	const OPTION_REGISTER_DISPLAY_NAME_ENABLE = 'cmreg_register_display_name_enable';
	const OPTION_REGISTER_DEFAULT_ROLE = 'cmreg_register_default_role';
	const OPTION_REGISTER_REPEAT_PASS_ENABLE = 'cmreg_register_repeat_pass_enable';
	const OPTION_S2MEMBERS_ENABLE = 'cmreg_s2members_enable';
	const OPTION_REGISTER_INVIT_CODE = 'cmreg_register_invit_code_require';
	const OPTION_REGISTER_RECAPTCHA_ENABLE = 'cmreg_register_recaptcha_enable';
	const OPTION_REGISTER_LOGIN_ENABLE = 'cmreg_register_login_enable';
	const OPTION_REGISTER_PRVENT_SYSTEM_EMAIL = 'cmreg_register_prevent_system_email';
	const OPTION_WP_REGISTER_PAGE_REDIRECTION_URL = 'cmreg_wp_register_page_redirection_url';
	const OPTION_LOGIN_RECAPTCHA_ENABLE = 'cmreg_login_recaptcha_enable';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_ACTION = 'cmreg_login_limit_attempts_action';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_NUMBER = 'cmreg_login_limit_attempts_number';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_INTERVAL_MINUTES = 'cmreg_login_limit_attempts_interval_minutes';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_SEND_USER_EMAIL = 'cmreg_login_limit_attempts_send_user_email';
	const OPTION_REGISTER_S2MEMBER_DEFAULT_LEVEL = 'cmreg_register_s2member_default_level';
	const OPTION_REGISTER_WELCOME_EMAIL_SUBJECT = 'cmreg_register_welcome_email_subject';
	const OPTION_REGISTER_WELCOME_EMAIL_BODY = 'cmreg_register_welcome_email_body';
	const OPTION_REGISTER_ACTIVATION_EMAIL_SUBJECT = 'cmreg_register_activation_email_subject';
	const OPTION_REGISTER_ACTIVATION_EMAIL_BODY = 'cmreg_register_activation_email_body';
	const OPTION_REGISTER_EMAIL_VERIFICATION_ENABLE = 'cmreg_register_email_verification_enable';
	const OPTION_REGISTER_ADMIN_NOTIFY_EMAIL = 'cmreg_register_admin_notify_email';
	const OPTION_REGISTER_ADMIN_NOTIFY_REGISTERED = 'cmreg_register_admin_notify_registered';
	const OPTION_REGISTER_ADMIN_NOTIFY_ACTIVATED = 'cmreg_register_admin_notify_activated';
	const OPTION_REGISTER_WELCOME_PAGE = 'cmreg_register_welcome_page';
	const OPTION_REGISTER_STRONG_PASS_ENABLE = 'cmreg_register_strong_pass_enable';
	const OPTION_REGISTER_DAYS_FOR_VERIFICATION = 'cmreg_register_days_for_verification';
	const OPTION_OVERLAY_OPACITY = 'cmreg_overlay_opacity';
	const OPTION_OVERLAY_PRELOAD = 'cmreg_overlay_preload';
	const OPTION_LOGOUT_INACTIVITY_TIME = 'cmreg_logout_inactivity_time_min';
	const OPTION_RELOAD_AFTER_LOGOUT = 'cmreg_reload_after_logout';
	const OPTION_REGISTER_NOTICE_ADMIN_ENABLE = 'cmreg_register_notice_admin_enable';
	const OPTION_REGISTER_EXTRA_FIELDS = 'cmreg_register_extra_fields';
	const OPTION_TERMS_OF_SERVICE_CHECKBOX_TEXT = 'cmreg_toc_checkbox_text';
	const OPTION_EMAIL_USE_HTML = 'cmreg_email_use_html';
	const OPTION_SOCIAL_LOGIN_FACEBOOK_APP_ID = 'cmreg_social_login_facebook_app_id';
	const OPTION_SOCIAL_LOGIN_FACEBOOK_APP_SECRET = 'cmreg_social_login_facebook_app_secret';
	const OPTION_SOCIAL_LOGIN_ENABLE = 'cmreg_social_login_enable';
	const OPTION_LOGIN_SHOW_SOCIAL_LOGIN_BUTTONS = 'cmreg_login_show_social_login_buttons';
	const OPTION_REGISTER_SHOW_SOCIAL_LOGIN_BUTTONS = 'cmreg_register_show_social_login_buttons';
	const OPTION_SOCIAL_LOGIN_ENABLE_ALLOW_REGISTRATION = 'cmreg_social_login_allow_registration';
	const OPTION_REGISTER_IP_ALLOW = 'cmreg_register_ip_allow';
	const OPTION_REGISTER_IP_DENY = 'cmreg_register_ip_deny';
	const OPTION_LOGIN_IP_ALLOW = 'cmreg_login_ip_allow';
	const OPTION_LOGIN_IP_DENY= 'cmreg_login_ip_deny';
	
	const LOGIN_FIELD_EMAIL = 'email';
	const LOGIN_FIELD_LOGIN = 'login';
	const LOGIN_FIELD_BOTH = 'both';
	
	const INVITATION_CODE_DISABLED = 'disabled';
	const INVITATION_CODE_OPTIONAL = 'optional';
	const INVITATION_CODE_REQUIRED = 'required';
	
	const LIMIT_ATTEMPTS_ACTION_DISABLED = 'disabled';
	const LIMIT_ATTEMPTS_ACTION_SHOW_CAPTCHA = 'show_captcha';
	const LIMIT_ATTEMPTS_ACTION_WAIT = 'wait';
	
	const STRONG_PASSWORD_REGEXP = '~^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$~';
	
	
	
	public static function getOptionsConfig() {
		
		return apply_filters('cmreg_options_config', array(
	
			self::OPTION_LOGIN_REDIRECT_URL => array(
				'type' => self::TYPE_STRING,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Redirect after login to URL address',
				'desc' => 'Enter an option URL address that users will be redirected after login. If empty user will stay on the same page.<br />'
							. 'You can use the <kbd>%userlogin%</kbd> and <kbd>%usernicename%</kbd> parameter in the URL, for example: '
							. '<kbd>/welcome/%usernicename%</kbd>.',
			),
			self::OPTION_LOGIN_REMEMBER_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Enable the "Remember me" option',
			),
			self::OPTION_LOGIN_LOST_PASSWORD_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Enable lost password form',
			),
			self::OPTION_LOGIN_FIELD => array(
				'type' => self::TYPE_RADIO,
				'default' => self::LOGIN_FIELD_BOTH,
				'options' => array(
					self::LOGIN_FIELD_EMAIL => 'email',
					self::LOGIN_FIELD_LOGIN => 'login',
					self::LOGIN_FIELD_BOTH => 'both email or login',
				),
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Login using field',
			),
			self::OPTION_WP_LOGIN_PAGE_REDIRECTION_URL => array(
				'type' => self::TYPE_STRING,
				'default' => '',
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Disable wp-login.php and redirect to this URL',
				'desc' => 'You can disable the regular Wordpress login page (wp-login.php) and redirect users to the specified URL address '
						. 'where they can find the CM Registration login form/shortcode. This will affect also the lost password page. '
						. 'Leave blank to enable the wp-login.php page.',
			),
			self::OPTION_LOGOUT_REDIRECT_URL => array(
				'type' => self::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'logout',
				'title' => 'Redirect after logout to URL address',
				'desc' => 'You can enter a custom URL address that users will be redirected after logout.',
			),
			self::OPTION_REGISTER_LOGIN_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Allow user to enter his login',
				'desc' => 'If disabled, the login will be created from the entered email address. The login is need during the singing-in.',
			),
			self::OPTION_REGISTER_DISPLAY_NAME_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Allow user to enter his publicly displayed name',
				'desc' => 'If disabled, the public name will be his email address. If enabled user can enter name that will be displayed next to '
							. 'his comments or posts.',
			),
			self::OPTION_REGISTER_NOTICE_ADMIN_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Notify admin about new registration',
				'desc' => 'If enabled then the default notification email will be send to: '. get_bloginfo('admin_email'),
			),
			self::OPTION_REGISTER_PRVENT_SYSTEM_EMAIL => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Prevent sending the standard WP welcome email',
				'desc' => 'If enabled the regular Wordpress\' welcome mail won\'t be send to the user. For some specific cases you may need to disable this option.',
			),
			self::OPTION_WP_REGISTER_PAGE_REDIRECTION_URL => array(
				'type' => self::TYPE_STRING,
				'default' => '',
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Disable WP registration page and redirect to this URL',
				'desc' => 'You can disable the regular Wordpress registration page (wp-login.php?action=register) and redirect users to the specified URL address '
						. 'where they can find the CM Registration form/shortcode. Leave blank to enable the wp-login.php page.',
			),
			self::OPTION_REGISTER_REPEAT_PASS_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'pass',
				'title' => 'Require to repeat password',
			),
			self::OPTION_REGISTER_STRONG_PASS_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'pass',
				'title' => 'Require strong passwords',
				'desc' => 'Password must be at least 8 characters long and must contain at least one lowercase letter, one uppercase letter and one digit.<br />'
							.'Regular expression: <kbd>'. Settings::STRONG_PASSWORD_REGEXP .'</kbd>',
			),
			
// 			self::OPTION_REGISTER_ADMIN_NOTIFY_EMAIL => array(
// 				'type' => self::TYPE_CSV_LINE,
// 				'default' => function() { return array(get_bloginfo('admin_email')); },
// 				'category' => 'email',
// 				'subcategory' => 'admin',
// 				'title' => 'Administrators emails for notifications',
// 				'desc' => 'Enter comma separated email addresses.',
// 			),
// 			self::OPTION_REGISTER_ADMIN_NOTIFY_REGISTERED => array(
// 				'type' => self::TYPE_BOOL,
// 				'default' => 0,
// 				'category' => 'email',
// 				'subcategory' => 'admin',
// 				'title' => 'Notify administrator about new registration',
// 				'desc' => 'Send email when user has registered his email account (even if not confirmed yet).',
// 			),
// 			self::OPTION_REGISTER_ADMIN_NOTIFY_ACTIVATED => array(
// 				'type' => self::TYPE_BOOL,
// 				'default' => 0,
// 				'category' => 'email',
// 				'subcategory' => 'admin',
// 				'title' => 'Notify administrator about account activation',
// 				'desc' => 'Send email when user has confirmed his email address (works only when email confirmation is required).',
// 			),
			
			
			self::OPTION_CUSTOM_CSS => array(
				'type' => self::TYPE_TEXTAREA,
				'category' => 'general',
				'subcategory' => 'appearance',
				'title' => 'Custom CSS',
				'desc' => 'You can enter a custom CSS which will be embeded on every page that contains a CM Registration interface.',
			),
			
		));
		
	}
	
	
	
	static function listShortcodes($vars) {
		$out = '';
		foreach ($vars as $name => $val) {
			$out .= $name . '<br />';
		}
		return $out;
	}
	
	
}
