<?php

namespace com\cminds\registration\model;

abstract class TaxonomyTerm extends Model {
	
	const TAXONOMY = '';
	
	const FIELDS_MODEL = 'model';
	const FIELDS_ALL = 'all';
	const FIELDS_IDS = 'ids';
	const FIELDS_NAMES = 'names';
	const FIELDS_COUNT = 'count';
	const FIELDS_ID_PARENT = 'id=>parent';
	const FIELDS_ID_SLUG = 'id=>slug';
	const FIELDS_ID_NAME = 'id=>name';
	
	static protected $instances;
	
	protected $term;
	
	
	static function bootstrap() {
		add_action('init', array(get_called_class(), 'init'), 3);
	}
	
	
	/**
	 * Get instance
	 * 
	 * @param object|int $term Term object or ID
	 * @return com\cminds\registration\model\TaxonomyTerm
	 */
	static function getInstance($term) {
		if (is_object($term) AND $term instanceof static) {
			return $term;
		}
		else if (is_scalar($term)) {
			if (!empty(static::$instances[$term])) return static::$instances[$term];
			else if (is_numeric($term)) $term = get_term_by('term_id', $term, static::TAXONOMY);
			else $term = get_term_by('slug', $term, static::TAXONOMY);
		}
		if (!empty($term) AND is_object($term) AND $term->taxonomy == static::TAXONOMY) {
			if (empty(static::$instances[$term->term_id])) {
				static::$instances[$term->term_id] = new static($term);
			}
			return static::$instances[$term->term_id];
		}
	}
	
	
	function __construct($term) {
		$this->term = $term;
	}
	
	
	function getId() {
		return $this->term->term_id;
	}
	
	
	function getName() {
		return $this->term->name;
	}
	
	
	function getSlug() {
		return $this->term->slug;
	}
	

	public function getPermalink() {
		return static::getTermPermalink($this->term);
// 		return get_term_link($this->term, self::TAXONOMY);
	}
	
	
	static public function getTermPermalink($term) {
		return get_term_link($term, $term->taxonomy);
	}
	

	public function getParentInstance() {
		if ($this->term->parent) {
			return static::getInstance($this->term->parent);
		}
	}
	
	
	public function getParentId() {
		return $this->term->parent;
	}
	

    static function getTree($params = array(), $depth = 0) {
    	$params = shortcode_atts(array('orderby' => 'name', 'hide_empty' => 0, 'parent' => null), $params);
        $terms = get_terms(static::TAXONOMY, $params);
        $output = array();
        foreach ($terms as $term) {
        	if ($obj = static::getInstance($term)) {
	        	$output[$term->term_id] = str_repeat('-', $depth) .' '. $term->name;
	        	$output += static::getTree(array('parent' => $term->term_id), $depth+1);
        	}
        }
        return $output;
    }
    
    
    public static function getTreeArray($params = array(), $depth = 0, $fields = self::FIELDS_MODEL) {
    	if (empty($params['orderby'])) $params['orderby'] = 'name';
    	if (empty($params['hide_empty'])) $params['hide_empty'] = 0;
    	if (empty($params['parent'])) $params['parent'] = null;
    	$terms = get_terms(static::TAXONOMY, $params);
    	$output = array();
    	foreach ($terms as $term) {
    		if ($obj = static::getInstance($term)) {
	    		$term->term_id = intval($term->term_id);
	    		switch ($fields) {
	    			case self::FIELDS_IDS:
	    				$value = $term->term_id;
	    				break;
	    			case self::FIELDS_NAMES:
    				case self::FIELDS_ID_NAME:
	    				$value = $term->name;
	    				break;
    				case self::FIELDS_ALL:
    					$value = $term;
    					break;
    				default:
    					$value = $obj;
	    		}
	    		$output[$params['parent'] ? $params['parent'] : 0][$term->term_id] = $value;
	    		$output += self::getTreeArray(array('parent' => $term->term_id), $depth+1, $fields);
    		}
    	}
    	return $output;
    }
    
	
	
	static function getAll($fields = self::FIELDS_MODEL, $params = array()) {
		$terms = get_terms(static::TAXONOMY, array_merge($params, array(
			'hide_empty' => 0,
			'fields' => ($fields == self::FIELDS_MODEL ? self::FIELDS_ALL : $fields),
		)));
		if ($fields == self::FIELDS_MODEL) {
			return static::mapTerms($terms);
		} else {
			return $terms;
		}
	}
	
	
	static function mapTerms(array $terms) {
		$output = array();
		foreach ($terms as $term) {
			$output[$term->term_id] = ($term instanceof static ? $term : static::getInstance($term));
		}
		return $output;
	}
	
	
	static function mapByParent(array $terms) {
		$out = array();
		foreach ($terms as $term) {
			$term = ($term instanceof static ? $term : static::getInstance($term));
			$out[intval($term->getParentId())][$term->getId()] = $term;
		}
		return $out;
	}
	

	static function getPostTerms($postId, $fields = TaxonomyTerm::FIELDS_MODEL, $params = array()) {
		if (in_array($fields, array(static::FIELDS_IDS, static::FIELDS_NAMES, static::FIELDS_ALL, static::FIELDS_MODEL))) {
			$terms = wp_get_post_terms($postId, static::TAXONOMY, array_merge($params, array(
				'fields' => ($fields == static::FIELDS_MODEL ? static::FIELDS_ALL : $fields),
			)));
			if ($fields == static::FIELDS_MODEL) {
				return static::mapTerms($terms);
			}  else {
				return $terms;
			}
		} else {
			$terms = wp_get_post_terms($postId, static::TAXONOMY, array_merge($params, array(
				'fields' => static::FIELDS_IDS,
			)));
			return static::getAll($fields, array_merge($params, array('include' => $terms)));
		}
	}
	
	
	function getTerm() {
		return $this->term;
	}
	
	
}
