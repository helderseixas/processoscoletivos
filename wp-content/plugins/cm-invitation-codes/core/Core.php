<?php

namespace com\cminds\registration\core;

use com\cminds\registration\App;

abstract class Core {
	
	const VERSION = '';
	const SLUG = '';
	const PLUGIN_NAME = '';
	const PLUGIN_WEBSITE = '';
	const LICENSING_PLUGIN_MENU = '';
	
	const OPTION_TRIGGER_FLUSH_REWRITE = '_trigger_flush_rewrite';
	
	static protected $pluginFile;
	static protected $path;
	static protected $pluginUrl;
	static protected $baseNamespace;
	
	
	static function bootstrap($pluginFile) {
	
		static::$pluginFile = $pluginFile;
		static::$path = dirname(static::$pluginFile);
		static::$pluginUrl = plugins_url('', static::$pluginFile);
		static::$baseNamespace = implode('\\', array_slice(explode('\\', get_called_class()), 0, -1));
		
		add_action( 'activated_plugin', function() {
			update_option( App::prefix('plugin_error'),  ob_get_contents() );
		} );
	
		// Auto-load
		spl_autoload_register(array(get_called_class(), 'autoload'));
		
		// Licensing API
		if (static::isPro()) {
			require static::path('package/cminds-pro.php');
		} else {
			require static::path('package/cminds-free.php');
		}
		
		// Class bootstraping
		$classToBootstrap = static::getClassToBootstrap();
		foreach ($classToBootstrap as $className) {
			$method = array($className, 'bootstrap');
// 			if (strpos($className, 'Widget') !== false) {
// 				var_dump($className);
// 				var_dump(method_exists($className, 'bootstrap'));
// 				var_dump($method);
// 				var_dump(is_callable($method));
// 				call_user_func($method);
// 				exit;
// 			}
			if (method_exists($className, 'bootstrap') AND is_callable($method)) {
				call_user_func($method);
			}
		}

		// Other actions
		add_action('init', array(get_called_class(), 'init'), 1);
		add_action('admin_menu', array(get_called_class(), 'admin_menu'));
		
		register_activation_hook($pluginFile, array(get_called_class(), 'activatePlugin'));
		register_uninstall_hook($pluginFile, array(get_called_class(), 'deactivatePlugin'));
		
	}
	
	static function init() {
		if (get_option(static::prefix(static::OPTION_TRIGGER_FLUSH_REWRITE)) == 1) {
			flush_rewrite_rules(true);
			update_option(static::prefix(static::OPTION_TRIGGER_FLUSH_REWRITE), null);
		}
	}
	
	static function admin_menu() {
		
	}
	
	static protected function getClassToBootstrap() {
		return array();
	}
	
	

	static function getClassNames($namespaceFragment) {
		$files = array();
		$path = static::path($namespaceFragment);
		if (file_exists($path) AND is_dir($path) AND is_readable($path)) {
			$files = scandir($path);
			foreach ($files as &$name) {
				if (preg_match('/^([a-zA-Z0-9]+)\.php$/', $name, $match)) {
					$name = static::namespaced($namespaceFragment .'\\'. $match[1]);
				} else {
					$name = null;
				}
			}
		}
		return array_filter($files);
	}
	
	
	static function autoload($name) {
		if (substr($name, 0, strlen(static::$baseNamespace)) == static::$baseNamespace) {
			$path = str_replace('\\', DIRECTORY_SEPARATOR, substr($name, strlen(static::$baseNamespace)+1, 9999));
			$check = array(static::path($path), static::path('core/'. $path));
			foreach ($check as $file) {
				$file .= '.php';
				if (file_exists($file) AND is_readable($file)) {
					require_once $file;
					return;
				}
			}
		}
	}
	
	

	static function activatePlugin() {
		
		// Check PHP version
		if (version_compare('5.4.43', PHP_VERSION, '=')) {
			die('We are sorry, but your specific PHP version 5.4.43 contains a bug related to closures. To use this plugin you need to change the PHP version.');
		}
		
		// Check memory limit
		$memoryLimit = ini_get('memory_limit');
		$memoryLimitUnit = preg_replace('/[0-9]/', '', $memoryLimit);
		$memoryLimitNumber = preg_replace('/[^0-9]/', '', $memoryLimit);
		if ('G' == strtoupper($memoryLimitUnit)) $memoryLimitNumber *= 1024;
		if ($memoryLimitNumber < 256) {
			die(sprintf('We are sorry, but this plugin requires at least 256 MB of memory, but your php.ini memory_limit is set to %s. '
				. 'Please contact with your hosting provider and ask to increase the memory limit.', $memoryLimit));
		}
		
		flush_rewrite_rules(true);
		update_option(static::prefix(static::OPTION_TRIGGER_FLUSH_REWRITE), 1);
		
	}
	
	static function deactivatePlugin() {
		flush_rewrite_rules(true);
	}
	
	
	static function path($path = '') {
		return static::$path . DIRECTORY_SEPARATOR . $path;
	}
	
	static function prefix($value) {
		return static::PREFIX . $value;
	}
	
	static function url($url) {
		return trailingslashit(static::$pluginUrl) . $url;
	}
	
	static function namespaced($name) {
		return static::$baseNamespace . '\\' . $name;
	}
	
	
	static function stripNamespace($name) {
		return str_replace(static::$baseNamespace .'\\', '', $name);
	}
	
	static function shortClassName($name, $suffix = '') {
		preg_match('#^(\w+\\\\)*(\w+)'. $suffix .'$#', $name, $match);
		if (!empty($match[2])) return $match[2];
	}
	
	static function isPro() {
		return file_exists(static::path('package/cminds-pro.php'));
	}
	
	static function isLicenseOk() {
		global $cmreg_isLicenseOk;
		return (!static::isPro() OR $cmreg_isLicenseOk);
	}
	
	static function getPluginName($full = false) {
		return static::PLUGIN_NAME . (($full && static::isPro()) ? ' Pro' : '');
	}
	
	
	static function getPluginFile() {
		return static::$pluginFile;
	}
	
	static function getBaseNamespace() {
		return static::$baseNamespace;
	}
	
	static function getLicenseAdditionalNames() {
		return array(static::getPluginName(false), static::getPluginName(true));
	}
	
	
}
