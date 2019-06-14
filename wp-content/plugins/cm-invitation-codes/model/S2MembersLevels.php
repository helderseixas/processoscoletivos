<?php

namespace com\cminds\registration\model;

class S2MembersLevels extends Model {
	
	const MEMBERSHIP_LEVELS = 'MEMBERSHIP_LEVELS';
	const MEMBER_LEVEL_LABEL = 'S2MEMBER_LEVEL%d_LABEL';
	
	static function getAll() {
		$result = array();
		$max = (defined(self::MEMBERSHIP_LEVELS) ? constant(self::MEMBERSHIP_LEVELS) : 0);
		for($n = 1; $n <= $max; $n++) {
			$const = sprintf(self::MEMBER_LEVEL_LABEL, $n);
			if (defined($const)) {
				$result[$n] = constant($const);
			} else {
				$result[$n] = 'Level #'. $n;
			}
		}
		return $result;
	}
	
	
	static function getLevelName($n) {
		$levels = self::getAll();
		return (isset($levels[$n]) ? $levels[$n] : $n);
	}
	
	
	
}