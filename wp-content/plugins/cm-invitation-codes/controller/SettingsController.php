<?php

namespace com\cminds\registration\controller;

use com\cminds\registration\helper\AdminNotice;

use com\cminds\registration\model\Labels;

use com\cminds\registration\App;

use com\cminds\registration\model\Settings;

class SettingsController extends Controller {
	
	const ACTION_CLEAR_CACHE = 'clear-cache';
	
	protected static $actions = array(
		array('name' => 'admin_menu', 'priority' => 15),
		'cmreg_display_available_shortcodes',
	);
	protected static $filters = array(array('name' => 'cmreg-settings-category', 'args' => 2, 'method' => 'settingsLabels'));
	protected static $ajax = array(
		'cmreg_admin_notice_dismiss',
	);
	
	
	static function admin_menu() {
		if (App::isPro()) {
			add_submenu_page(App::SLUG, App::getPluginName() . ' Settings', 'Settings', 'manage_options', self::getMenuSlug(), array(get_called_class(), 'render'));
		}
	}
	
	
	static function getMenuSlug() {
		return App::SLUG . (App::isPro() ? '-settings' : '');
	}
	
	
	
	static function render() {
		wp_enqueue_style('cmreg-backend');
		wp_enqueue_style('cmreg-settings');
		wp_enqueue_script('cmreg-backend');
		echo self::loadView('backend/template', array(
			'title' => App::getPluginName() . ' Settings',
			'nav' => self::getBackendNav(),
			'content' => self::loadBackendView('licensing-box') . self::loadBackendView('settings', array(
				'clearCacheUrl' => self::createBackendUrl(self::getMenuSlug(), array('action' => self::ACTION_CLEAR_CACHE), self::ACTION_CLEAR_CACHE),
			)),
		));
	}
	
	
	static function settingsLabels($result, $category) {
		if ($category == 'labels') {
			$result = self::loadBackendView('labels');
		}
		return $result;
	}
	
	
	static function processRequest() {
		$fileName = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		if (is_admin() AND $fileName == 'admin.php' AND !empty($_GET['page']) AND $_GET['page'] == self::getMenuSlug()) {
			
			if (!empty($_POST)) {
				
				// CSRF protection
		        if ((empty($_POST['nonce']) OR !wp_verify_nonce($_POST['nonce'], self::getMenuSlug()))) {
		        	// Invalid nonce
		        } else {
			        Settings::processPostRequest($_POST);
			        Labels::processPostRequest();
			        $response = array('status' => 'ok', 'msg' => 'Settings have been updated.');
			        wp_redirect(self::createBackendUrl(self::getMenuSlug(), $response));
			        exit;
		        }
	            
			}
			else if (!empty($_GET['action']) AND !empty($_GET['nonce']) AND wp_verify_nonce($_GET['nonce'], $_GET['action'])) switch ($_GET['action']) {
				case self::ACTION_CLEAR_CACHE:
					wp_redirect(self::createBackendUrl(self::getMenuSlug(), array('status' => 'ok', 'msg' => 'Cache has been removed.')));
					exit;
					break;
			}
	        
		}
	}
	
	
	static function cmreg_display_available_shortcodes() {
		echo self::loadBackendView('shortcodes');
	}
		
}
