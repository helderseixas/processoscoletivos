<?php

namespace com\cminds\registration\model;
use com\cminds\registration\App;

class ProfileField extends PostType {
	
	const POST_TYPE = 'cmreg_profile_field';
	
	const META_MAX_LENGTH = 'cmreg_max_len';
	const META_REQUIRED = 'cmreg_required';
	const META_ROLES = 'cmreg_roles';
	const META_FIELD_TYPE = 'cmreg_type';
	const META_SUBTYPE = 'cmreg_subtype';
	const META_TOOLTIP = 'cmreg_tooltip';
	const META_PLACEHOLDER = 'cmreg_placeholder';
	const META_CSS_CLASS = 'cmreg_css_class';
	const META_TEXTAREA_ROWS = 'cmreg_textare_rows';
	const META_NUMBER_MIN = 'cmreg_number_min';
	const META_NUMBER_MAX = 'cmreg_number_max';
	const META_NUMBER_STEP = 'cmreg_number_step';
	const META_MULTIPLE_SELECTION = 'cmreg_multiple_selection';
	const META_OPTIONS_VALUES = 'cmreg_options_values';
	
	const FIELD_TYPE_TEXT = 'text';
	const FIELD_TYPE_TEXTAREA = 'textarea';
	const FIELD_TYPE_NUMBER = 'number';
	const FIELD_TYPE_SELECT = 'select';
	const FIELD_TYPE_RADIO_GROUP = 'radio-group';
	const FIELD_TYPE_CHECKBOX_GROUP = 'checkbox-group';
	const FIELD_TYPE_DATE = 'date';
		
	
	static protected $postTypeOptions = array(
		'label' => 'Profile Field',
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => false,
		'show_in_admin_bar' => false,
		'show_in_menu' => App::SLUG,
		'hierarchical' => false,
		'supports' => array('title'),
		'has_archive' => false,
// 		'taxonomies' => array(Category::TAXONOMY),
	);
	
	
	
	static protected function getPostTypeLabels() {
		$singular = ucfirst('Profile field');
		$plural = ucfirst('Profile fields');
		return array(
			'name' => $plural,
			'singular_name' => $singular,
			'add_new' => sprintf(__('Add %s', App::SLUG), $singular),
			'add_new_item' => sprintf(__('Add New %s', App::SLUG), $singular),
			'edit_item' => sprintf(__('Edit %s', App::SLUG), $singular),
			'new_item' => sprintf(__('New %s', App::SLUG), $singular),
			'all_items' => $plural,
			'view_item' => sprintf(__('View %s', App::SLUG), $singular),
			'search_items' => sprintf(__('Search %s', App::SLUG), $plural),
			'not_found' => sprintf(__('No %s found', App::SLUG), $plural),
			'not_found_in_trash' => sprintf(__('No %s found in Trash', App::SLUG), $plural),
			'menu_name' => App::getPluginName()
		);
	}
	
	
	static function init() {
// 		static::$postTypeOptions['rewrite'] = array('slug' => Settings::getOption(Settings::OPTION_PERMALINK_PREFIX));
		parent::init();
	}
	
	/**
	 * Get instance
	 *
	 * @param WP_Post|int $post Post object or ID
	 * @return com\cminds\registration\model\ProfileField
	 */
	static function getInstance($post) {
		return parent::getInstance($post);
	}
	
	
	static function getJSData() {
		$fields = static::getAll();
		$data = array();
		foreach ($fields as $field) {
			/* @var $field ProfileField */
			$roles = $field->getRoles();
			$data[] = array(
				'name' => $field->getUserMetaKey(),
				'label' => $field->getLabel(),
				'type' => $field->getFieldType(),
				'required' => $field->isRequired(),
				'maxlength' => $field->getMaxLength(),
				'placeholder' => $field->getPlaceholder(),
				'description' => $field->getTooltip(),
				'value' => $field->getDefaultValue(),
				'className' => $field->getCSSClass(),
				'subtype' => $field->getSubtype(),
				'rows' => $field->getTextareaRows(),
				'min' => $field->getNumberMin(),
				'max' => $field->getNumberMax(),
				'step' => $field->getNumberStep(),
				'multiple' => $field->isMultipleSelectionAllowed(),
				'values' => $field->getOptionsValues(),
				'access' => !empty($roles),
				'role' => (is_array($roles) ? implode(',', $roles) : ''),
			);
		}
		return $data;
	}
	
	
	static function getForRegistrationForm($formId) {
		$posts = get_posts(array(
			'post_type' => ProfileField::POST_TYPE,
			'parent' => $formId,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		));
		return array_map(function($post) {
			return ProfileField::getInstance($post);
		}, $posts);
	}
	
	
	static function getAll() {
		$posts = get_posts(array(
			'post_type' => ProfileField::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		));
		return array_map(function($post) {
			return ProfileField::getInstance($post);
		}, $posts);
	}
	
	
	static function getAllIds() {
		return get_posts(array(
			'post_type' => ProfileField::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'fields' => 'ids',
		));
	}
	
	
	static function create($userMeta, $label, $type) {
		$post = array(
			'post_title' => $label,
			'post_excerpt' => $userMeta,
			'post_author' => get_current_user_id(),
			'post_status' => 'publish',
			'post_type' => static::POST_TYPE,
			'comment_status' => 'closed',
			'ping_status' => 'closed',
		);
		$postId = wp_insert_post($post);
		if (is_numeric($postId)) {
			$field = static::getInstance($postId);
			$field->setFieldType($type);
			return $field;
		}
	}
	
	
	function getLabel() {
		return $this->getTitle();
	}
	
	function setLabel($val) {
		$this->post->post_title = $val;
		$this->save();
		return $this;
	}
	
	
	function setUserMetaKey($val) {
		$this->post->post_excerpt = $val;
		$this->save();
		return $this;
	}
	
	function getUserMetaKey() {
		return $this->getExcerpt();
	}
	
	
	function getMaxLength() {
		return $this->getPostMeta(static::META_MAX_LENGTH);
	}
	
	function setMaxLength($val) {
		return $this->setPostMeta(static::META_MAX_LENGTH, $val);
	}
	
	function getFieldType() {
		return $this->getPostMeta(static::META_FIELD_TYPE);
	}
	
	function setFieldType($val) {
		return $this->setPostMeta(static::META_FIELD_TYPE, $val);
	}
	
	function getSubtype() {
		return $this->getPostMeta(static::META_SUBTYPE);
	}
	
	function setSubtype($val) {
		return $this->setPostMeta(static::META_SUBTYPE, $val);
	}
	
	
	function getTooltip() {
		return $this->getPostMeta(static::META_TOOLTIP);
	}
	
	function setTooltip($val) {
		return $this->setPostMeta(static::META_TOOLTIP, $val);
	}
	
	function getDefaultValue() {
		return $this->getContent();
	}
	
	function setDefaultValue($val) {
		return $this->setContent($val)->save();
	}
	
	function getPlaceholder() {
		return $this->getPostMeta(static::META_PLACEHOLDER);
	}
	
	function setPlaceholder($val) {
		return $this->setPostMeta(static::META_PLACEHOLDER, $val);
	}
	
	function getCSSClass() {
		return $this->getPostMeta(static::META_CSS_CLASS);
	}
	
	function setCSSClass($val) {
		return $this->setPostMeta(static::META_CSS_CLASS, $val);
	}
	
	function isRequired() {
		return $this->getPostMeta(static::META_REQUIRED);
	}
	
	function setRequired($val) {
		return $this->setPostMeta(static::META_REQUIRED, $val);
	}
	
	function getTextareaRows() {
		return $this->getPostMeta(static::META_TEXTAREA_ROWS);
	}
	
	function setTextareaRows($val) {
		return $this->setPostMeta(static::META_TEXTAREA_ROWS, $val);
	}
	
	function getNumberMin() {
		return $this->getPostMeta(static::META_NUMBER_MIN);
	}
	
	function setNumberMin($val) {
		return $this->setPostMeta(static::META_NUMBER_MIN, $val);
	}
	
	function getNumberMax() {
		return $this->getPostMeta(static::META_NUMBER_MAX);
	}
	
	function setNumberMax($val) {
		return $this->setPostMeta(static::META_NUMBER_MAX, $val);
	}
	
	function getNumberStep() {
		return $this->getPostMeta(static::META_NUMBER_STEP);
	}
	
	function setNumberStep($val) {
		return $this->setPostMeta(static::META_NUMBER_STEP, $val);
	}
	
	function isMultipleSelectionAllowed() {
		return $this->getPostMeta(static::META_MULTIPLE_SELECTION);
	}
	
	function setMultipleSelectionAllowed($val) {
		return $this->setPostMeta(static::META_MULTIPLE_SELECTION, $val);
	}
	
	function getOptionsValues() {
		return $this->getPostMeta(static::META_OPTIONS_VALUES);
	}
	
	function setOptionsValues($val) {
		return $this->setPostMeta(static::META_OPTIONS_VALUES, $val);
	}
	
	function getRoles() {
		return $this->getPostMeta(static::META_ROLES);
	}
	
	function setRoles($val) {
		return $this->setPostMeta(static::META_ROLES, $val);
	}
	
	
	function setValueForUser($userId, $value) {
		$types = array(static::FIELD_TYPE_SELECT, static::FIELD_TYPE_CHECKBOX_GROUP);
		if (in_array($this->getFieldType(), $types) AND $this->isMultipleSelectionAllowed() AND !is_array($value)) {
			$value = array($value);
		}
		return update_user_meta($userId, $this->getUserMetaKey(), $value);
	}
	
	function getValueForUser($userId) {
		$value = get_user_meta($userId, $this->getUserMetaKey(), $single = true);
		
		// Make empty array
		$types = array(static::FIELD_TYPE_SELECT, static::FIELD_TYPE_CHECKBOX_GROUP, static::FIELD_TYPE_RADIO_GROUP);
		if (in_array($this->getFieldType(), $types)) {
			if ($this->isMultipleSelectionAllowed() AND is_scalar($value)) {
				if (strlen($value) > 0) $value = array($value);
				else $value = array();
			}
			if (empty($value)) {
				$options = $this->getOptionsValues();
				foreach ($options as $option) {
					if (!empty($option['selected'])) {
						$value[] = $option['value'];
					}
				}
			}
		} else {
			if (strlen($value) == 0) {
				$value = $this->getDefaultValue();
			}
		}
		
		return $value;
	}
		
}