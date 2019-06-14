<?php

namespace com\cminds\registration\helper;

use com\cminds\registration\model\Settings;

class IPHelper {
	
	
	static function match($pattern, $ip) {
		$pattern = trim($pattern);
		$pattern = preg_quote($pattern);
		$pattern = str_replace('x', '[]+', $pattern);
		return preg_match('~^' . $pattern .'$~', $ip);
	}
	
	
	static function inList($ip, array $list) {
		foreach ($list as $pattern) {
			if (static::match($pattern, $ip)) {
				return true;
			}
		}
		return false;
	}
	
}