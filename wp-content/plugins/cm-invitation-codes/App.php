<?php

namespace com\cminds\registration;

use com\cminds\registration\model\Labels;

use com\cminds\registration\core\Core;

use com\cminds\registration\controller\SettingsController;

use com\cminds\registration\model\Settings;

require_once dirname(__FILE__) . '/core/Core.php';

class App extends Core {
	
	const VERSION = '1.9.3';
	const PREFIX = 'cmreg';
	const SLUG = 'cm-registration';
	const PLUGIN_NAME = 'CM Registration';
	const PLUGIN_WEBSITE = 'https://www.cminds.com/store/cm-registration-and-invitation-codes-plugin-for-wordpress/';
	const TESTING = false;
	
	
	static function bootstrap($pluginFile) {
		parent::bootstrap($pluginFile);
	}
	
	
	static protected function getClassToBootstrap() {
		$classToBootstrap = array_merge(
			parent::getClassToBootstrap(),
			static::getClassNames('controller'),
			static::getClassNames('model'),
			static::getClassNames('metabox')
		);
		if (static::isLicenseOk()) {
			$classToBootstrap = array_merge($classToBootstrap, static::getClassNames('shortcode'), static::getClassNames('widget'));
		}
		return $classToBootstrap;
	}
	
	
	static function init() {
		parent::init();
		
		wp_register_script('cmreg-utils', static::url('asset/js/utils.js'), array('jquery'), static::VERSION, true);
		wp_register_script('cmreg-backend', static::url('asset/js/backend.js'), array('jquery'), static::VERSION, true);
		wp_register_script('cmreg-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), static::VERSION, true);
		wp_register_script('cmreg_show_toast_message', static::url('asset/js/show-toast-message.js'), array('jquery', 'cmreg-utils'), static::VERSION, true);
		wp_register_script('cmreg-logout', static::url('asset/js/logout.js'), array('jquery', 'heartbeat'), static::VERSION, true);
		wp_register_script('cmreg-profile-edit', static::url('asset/js/profile-edit.js'), array('jquery', 'cmreg-utils'), static::VERSION, true);
		wp_register_script('cmreg-form-builder', static::url('asset/vendors/form-builder/form-builder.min.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), static::VERSION, true);
		wp_register_script('cmreg-form-builder-render', static::url('asset/vendors/form-builder/form-render.min.js'), array('jquery', 'jquery-ui-core'), static::VERSION, true);
		wp_register_script('cmreg-backend-profile-fields', static::url('asset/js/backend-profile-fields.js'), array('cmreg-form-builder'), static::VERSION, true);
		
		wp_register_style('cmreg-settings', static::url('asset/css/settings.css'), null, static::VERSION);
		wp_register_style('cmreg-backend', static::url('asset/css/backend.css'), null, static::VERSION);
		wp_register_style('cmreg-frontend', static::url('asset/css/frontend.css'), array('dashicons'), static::VERSION);
		wp_register_style('cmreg-form-builder', static::url('asset/vendors/form-builder/form-builder.min.css'), array(), static::VERSION);
		
		wp_register_script('cmreg-frontend', static::url('asset/js/frontend.js'), array('jquery', 'cmreg-utils', 'cmreg-recaptcha', 'cmreg-form-builder-render'), static::VERSION, true);
		
	}
	

	static function admin_menu() {
		parent::admin_menu();
		$name = static::getPluginName(true);
// 		$page = add_menu_page($name, $name, 'manage_options', static::PREFIX,
// 			array(App::namespaced('controller\SettingsController'), 'render'), 'dashicons-admin-users', 5679);
		if (App::isPro()) {
			add_menu_page($name, $name, 'manage_options', static::SLUG); //, array(App::namespaced('controller\SettingsController'), 'render'));
		} else {
			add_menu_page($name, $name, 'manage_options', static::SLUG, array(App::namespaced('controller\SettingsController'), 'render'));
		}
	}
	
	
}
