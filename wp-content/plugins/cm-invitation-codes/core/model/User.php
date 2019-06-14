<?php

namespace com\cminds\registration\model;

class User extends Model {
	

	static function getSomeAdminUserId() {
		$admins = get_users(array('role' => 'administrator'));
		return ($admins[0]->ID);
	}
	
	
	static function setPassword($userId, $password) {
		return wp_set_password($password, $userId);
	}
	
}