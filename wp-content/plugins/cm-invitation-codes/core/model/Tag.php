<?php

namespace com\cminds\registration\model;

class Tag extends TaxonomyTerm {

	const TAXONOMY = 'post_tag';
	
	
    /**
	 * Get instance
	 * 
	 * @param object|int $term Term object or ID
	 * @return com\cminds\registration\model\Tag
	 */
	static function getInstance($term) {
		return parent::getInstance($term);
	}
	
	
    
}
