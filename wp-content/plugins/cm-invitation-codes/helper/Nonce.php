<?php

namespace com\cminds\registration\helper;

use com\cminds\registration\App;

class Nonce {
	
	static function create($name, $seed = null) {
		if (is_null($seed)) {
			$seed = mt_rand();
		}
		return $seed .'-'. wp_create_nonce($name . $seed);
	}
	
	
	static function verify($name, $nonce) {
		$nonceArray = explode('-', $nonce);
		$value = array_pop($nonceArray);
		$seed = array_pop($nonceArray);
		return ($nonce === static::create($name, $seed));
	}
		
}
