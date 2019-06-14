<?php

namespace com\cminds\registration\controller;

use com\cminds\registration\App;
use com\cminds\registration\model\ProfileField;

class UpdateController extends Controller {
	
	static $actions = array(
		'init' => array('priority' => 1),
	);
	
	const OPTION_NAME = 'cmreg_update_methods';

	static function init() {
		global $wpdb;
		
		if (defined('DOING_AJAX') && DOING_AJAX) return;
		
		$updates = get_option(self::OPTION_NAME);
		if (empty($updates)) $updates = array();
		$count = count($updates);
		
		$methods = get_class_methods(__CLASS__);
		foreach ($methods as $method) {
			if (preg_match('/^update((_[0-9]+)+)/', $method, $match)) {
				if (!in_array($method, $updates)) {
					call_user_func(array(__CLASS__, $method));
					$updates[] = $method;
				}
			}
		}
		
		if ($count != count($updates)) {
			update_option(self::OPTION_NAME, $updates, true);
		}
		
	}
	
	
	static function update_1_9_0_profile_fields() {
		
		$fields = get_option('cmreg_register_extra_fields');
		
		if (is_array($fields)) foreach ($fields as $i => $field) {
			if ($i == 0) continue;
			$obj = ProfileField::create($field['meta_name'], $field['label'], ProfileField::FIELD_TYPE_TEXT);
			if ($obj) {
				$obj->setMenuOrder($i)->save();
				if (!empty($field['role'])) $obj->setRoles(array($field['role']));
				if (!empty($field['maxlen'])) $obj->setMaxLength($field['maxlen']);
			}
		}
		
	}
	
	
}
