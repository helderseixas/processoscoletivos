<?php

namespace com\cminds\registration\model;

abstract class SettingsAbstract {
	
	const TYPE_BOOL = 'bool';
	const TYPE_INT = 'int';
	const TYPE_STRING = 'string';
	const TYPE_TEXTAREA = 'textarea';
	const TYPE_RICH_TEXT = 'rich_text';
	const TYPE_RADIO = 'radio';
	const TYPE_SELECT = 'select';
	const TYPE_MULTISELECT = 'multiselect';
	const TYPE_MULTICHECKBOX = 'multicheckbox';
	const TYPE_CSV_LINE = 'csv_line';
	const TYPE_USERS_LIST = 'users_list';
	const TYPE_CUSTOM = 'custom';
	const TYPE_PERCENT = 'percent';
	const TYPE_EXTRA_FIELDS = 'extra_fields';
	
	const PAGE_CREATE_KEY = '--create--';
	const PAGE_DEFINITION = 'newPage';
	

	public static $categories = array();
	public static $subcategories = array();
	
	
	public static function getOptionsConfig() {
		return array();
	}
	
	
	public static function getOptionsConfigByCategory($category, $subcategory = null) {
		$options = static::getOptionsConfig();
		return array_filter($options, function($val) use ($category, $subcategory) {
			if ($val['category'] == $category) {
				return (is_null($subcategory) OR $val['subcategory'] == $subcategory);
			}
		});
	}
	
	
	public static function getOptionConfig($name) {
		$options = static::getOptionsConfig();
		if (isset($options[$name])) {
			return $options[$name];
		}
	}
	
	
	public static function setOption($name, $value) {
		$options = static::getOptionsConfig();
		if (isset($options[$name])) {
			$field = $options[$name];
			\update_option($name, static::cast($value, $field['type']), $autoload = true);
			if (isset($field['afterSave']) AND is_callable($field['afterSave'])) {
				call_user_func($field['afterSave'], $field);
			}
		}
	}
	

	public static function getOption($name) {
		$options = static::getOptionsConfig();
		if (isset($options[$name])) {
			$field = $options[$name];
			$defaultValue = (isset($field['default']) ? $field['default'] : null);
			return static::cast(\get_option($name, $defaultValue), $field['type']);
		}
	}
	
	
	public static function getCategories() {
		$categories = array();
		$options = static::getOptionsConfig();
		foreach ($options as $option) {
			$categories[] = $option['category'];
		}
		return $categories;
	}
	
	
	public static function getSubcategories($category) {
		$subcategories = array();
		$options = static::getOptionsConfig();
		foreach ($options as $option) {
			if ($option['category'] == $category) {
				$subcategories[] = $option['subcategory'];
			}
		}
		return $subcategories;
	}
	
	
	protected static function boolval($val) {
		return (boolean) $val;
	}
	
	
	protected static function arrayval($val) {
		if (is_array($val)) return $val;
		else if (is_object($val)) return (array)$val;
		else return array();
	}
	
	
	protected static function cast($val, $type) {
		if ($type == static::TYPE_STRING) {
			return \trim(\strval($val));
		}
		else if ($type == static::TYPE_BOOL) {
			return (\intval($val) ? 1 : 0);
		}
		else if (in_array($type, array(static::TYPE_MULTISELECT, static::TYPE_USERS_LIST, static::TYPE_MULTICHECKBOX))) {
			if (empty($val)) return array();
			else return $val;
		}
		else if ($type == static::TYPE_MULTISELECT OR $type == static::TYPE_USERS_LIST) {
			if (empty($val)) return array();
			else return $val;
		}
		else {
			$castFunction = $type . 'val';
			if (function_exists('\\' . $castFunction)) {
				return call_user_func('\\' . $castFunction, $val);
			}
			else if (method_exists(__CLASS__, $castFunction)) {
				return call_user_func(array(__CLASS__, $castFunction), $val);
			} else {
				return $val;
			}
		}
	}
	
	
	protected static function csv_lineval($value) {
		if (is_callable($value)) $value = call_user_func($value);
		if (!is_array($value)) $value = explode(',', $value);
		return $value;
	}
	
	
	public static function processPostRequest($data) {
		
		// Create new pages
		$options = static::getOptionsConfig();
		foreach ($data as $key => &$value) {
			if ($value == static::PAGE_CREATE_KEY AND !empty($options[$key][static::PAGE_DEFINITION])) {
				$post = array_merge(array(
					'post_author' => get_current_user_id(),
					'post_status' => 'publish',
					'post_type' => 'page',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
				), $options[$key][static::PAGE_DEFINITION]);
				$postId = wp_insert_post($post);
				if (is_numeric($postId)) {
					$value = $postId;
				}
			}
		}
		
		// Save data
		$data = array_map('stripslashes_deep', $data);
		foreach ($options as $name => $optionConfig) {
			if (isset($data[$name])) {
				static::setOption($name, $data[$name]);
			} else {
				static::setOption($name, null);
			}
		}
		
	}
	
	
	public static function userId($userId = null) {
		if (empty($userId)) $userId = get_current_user_id();
		return $userId;
	}
	
	
	public static function isLoggedIn($userId = null) {
		$userId = static::userId($userId);
		return !empty($userId);
	}
	
	
	public static function getRolesOptions() {
		global $wp_roles;
		$result = array();
		if (!empty($wp_roles) AND is_array($wp_roles->roles)) foreach ($wp_roles->roles as $name => $role) {
			$result[$name] = $role['name'];
		}
		return $result;
	}
	
	
	public static function getPagesOptions() {
		$pages = \get_pages(array('number' => 100));
		$result = array(null => '--');
		if (is_array($pages)) foreach ($pages as $page) {
			$result[$page->ID] = $page->post_title;
		}
		return $result;
	}
	

	public static function getPageTemplatesOptions() {
		$theme = \wp_get_theme();
		$templates = (array)$theme->get_page_templates();
		$result = array();
		if ($pageTemplate = locate_template('page.php', false, false)) {
			$result['page.php'] = 'page.php';
		}
		// 		if ($singleTemplate = locate_template('single.php', false, false)) {
		// 			$result['single.php'] = 'Theme\'s single.php';
		// 		}
		return array_merge($result, $templates);
	}
	
	
	
}
