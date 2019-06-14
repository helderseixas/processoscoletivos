<?php

namespace com\cminds\registration\helper;

use com\cminds\registration\App;

class AdminNotice {
	
	const NONCE_SUFFIX = '-admin-notice';
	const AJAX_ACTION = 'admin_notice_dismiss';
	const USER_META_NONCE_DISMISS_SUFFIX = '_admin_notice_dismiss';
	
	protected $id;
	protected $type;
	protected $msg;
	protected $dismiss;
	
	
	function __construct($id, $type, $msg, $dismiss = false) {
		$this->id = preg_replace('/[^a-z0-9]+/i', '_', $id);
		$this->type = $type;
		$this->msg = $msg;
		$this->dismiss = $dismiss;
	}
	
	
	
	static function getNonceAction() {
		return App::prefix(self::NONCE_SUFFIX);
	}
	
	
	static function processAjaxDismiss() {
		if (is_user_logged_in() AND !empty($_POST['nonce']) AND wp_verify_nonce($_POST['nonce'], self::getNonceAction()) AND !empty($_POST['id'])) {
			$data = self::getDismissData();
			$data[] = $_POST['id'];
			self::setDismissData($data);
		}
	}
	
	
	static protected function getDismissDataKey() {
		return App::prefix(self::USER_META_NONCE_DISMISS_SUFFIX);
	}
	
	static protected function getDismissData() {
		$data = get_user_meta(get_current_user_id(), self::getDismissDataKey(), true);
		if (!is_array($data)) $data = array();
		return $data;
	}
	
	
	static protected function setDismissData($data) {
		return update_user_meta(get_current_user_id(), self::getDismissDataKey(), $data);
	}
	
	
	protected function getDismissButton() {
		return sprintf('<a href="%s" data-nonce="%s" data-action="%s" data-id="%s" class="%s" title="%s">&times;</a>',
			$href = esc_attr(admin_url('admin-ajax.php')),
			$nonce = esc_attr(wp_create_nonce(self::getNonceAction($this->id))),
			$action = esc_attr(App::prefix('_'. self::AJAX_ACTION)),
			$id = esc_attr($this->id),
			$class = esc_attr(App::prefix('-dismiss')),
			$title = esc_attr(__('Dismiss'))
		);
	}
	
	
	
	protected function getBody() {
		return $this->msg;
	}
	
	
	function isDismissed() {
		$data = self::getDismissData();
		return in_array($this->id, $data);
	}
	
	
	function __toString() {
		if ($this->isDismissed()) return '';
		wp_enqueue_script(App::prefix('-backend'));
		wp_enqueue_style(App::prefix('-backend'));
		$className = App::prefix('-admin-notice');
		return sprintf('<div class="%s %s" data-id="%s"><p><strong>%s:</strong> %s%s</p></div>',
			esc_attr($this->type),
			esc_attr($className),
			esc_attr($this->id),
			App::getPluginName(),
			$this->getBody(),
			($this->dismiss ? $this->getDismissButton() : '')
		);
	}
	
	
	static function method2Id($name) {
		$name = explode('\\', $name);
		return preg_replace('/[^a-z0-9]+/i', '_', end($name));
	}
	
}
