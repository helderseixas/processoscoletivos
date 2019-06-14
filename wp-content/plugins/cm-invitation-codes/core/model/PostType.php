<?php

namespace com\cminds\registration\model;

use com\cminds\registration\App;

abstract class PostType extends Model {
	
	const POST_TYPE = '';
	
	static protected $postTypeOptions = array();
	static protected $instances;
	
	protected $post;
	
	
	static function init() {
		parent::init();
		static::registerPostType();
	}
	
	
	static function registerPostType() {
		static::$postTypeOptions['labels'] = static::getPostTypeLabels();
		register_post_type(static::POST_TYPE, static::$postTypeOptions);
	}
	
	
	static function clearInstances() {
		static::$instances = array();
	}
	

	/**
	 * Get instance
	 * 
	 * @param WP_Post|int $post Post object or ID
	 * @return com\cminds\registration\model\PostType
	 */
	static function getInstance($post) {
		if (is_scalar($post)) {
			if (!empty(static::$instances[$post])) return static::$instances[$post];
			else if (is_numeric($post)) $post = get_post($post);
			else $post = get_post(array('post_name' => $post, 'post_status' => 'any'));
		}
		if (!empty($post) AND is_object($post) AND $post->post_type == static::POST_TYPE) {
			if (empty(static::$instances[$post->ID])) {
				static::$instances[$post->ID] = new static($post);
			}
			return static::$instances[$post->ID];
		}
	}
	
	
	static protected function getPostTypeLabels() {
		return array();
	}
	
	
	function __construct($post = null) {
		if (empty($post)) $post = new \stdClass();
		if (is_array($post)) $post = (object)$post;
		$this->post = $post;
	}
	
	
	function getId() {
		if (isset($this->post->ID)) {
			return $this->post->ID;
		}
	}
	
	
	function getPostMeta($name, $single = true) {
		return get_post_meta($this->getId(), $this->getPostMetaKey($name), $single);
	}
	
	function setPostMeta($name, $value) {
		update_post_meta($this->getId(), $this->getPostMetaKey($name), $value);
		return $this;
	}
	
	
	function getPostMetaKey($name) {
		return $name;
	}
	

	function getTitle() {
		if (isset($this->post->post_title)) {
			return $this->post->post_title;
		}
	}
	
	
	function setTitle($title) {
		$this->post->post_title = $title;
		return $this;
	}
	
	
	function getSlug() {
		if (isset($this->post->post_name)) {
			return $this->post->post_name;
		}
	}
	
	function setSlug($slug) {
		$this->post->post_name = $slug;
		return $this;
	}
	

	function getContent() {
		if (isset($this->post->post_content)) {
			return $this->post->post_content;
		}
	}
	
	
	function setContent($desc) {
		$this->post->post_content = $desc;
		return $this;
	}
	
	
	function getExcerpt() {
		if (isset($this->post->post_excerpt)) {
			return $this->post->post_excerpt;
		}
	}
	
	
	function setExcerpt($val) {
		$this->post->post_excerpt = $val;
		return $this;
	}
	
	
	function save() {
		$this->post->post_type = static::POST_TYPE;
		if ($this->getId()) {
			$result = wp_update_post((array)$this->post, $wp_error = true);
// 			var_dump($result);
			if (is_numeric($result)) {
				return $result;
			} else {
				return false;
			}
		} else {
			$id = wp_insert_post((array)$this->post);
			$this->post = get_post($id);
			return $id;
		}
	}
	
	
	function getPermalink() {
		return get_permalink($this->getId());
	}
	
	
	function getPost() {
		return $this->post;
	}
	
	
	
	
	function getTags($args = array()) {
		return wp_get_post_tags($this->getId(), $args);
	}
	
	
	function setTags($tags) {
		if (!is_array($tags)) $tags = explode(',', $tags);
		$tags = array_map('trim', $tags);
		$tags = implode(',', $tags);
		return wp_set_post_tags($this->getId(), $tags, $append = false);
	}
	
	
	function setParent($id) {
		$this->post->post_parent = $id;
		return $this;
	}
	
	
	function getStatus() {
		if (!empty($this->post->post_status)) {
			return $this->post->post_status;
		}
	}
	
	function setStatus($status) {
		$this->post->post_status = $status;
		return $this;
	}
	
	function setAuthor($userId) {
		$this->post->post_author = $userId;
		return $this;
	}
	
	function getMenuOrder() {
		return $this->post->menu_order;
	}
	
	function setMenuOrder($order) {
		$this->post->menu_order = $order;
		return $this;
	}
	
	
	function getAuthorId() {
		if (!empty($this->post->post_author)) {
			return $this->post->post_author;
		}
	}
	
	
	function getAuthor() {
		if (!empty($this->post->post_author) AND $user = get_userdata($this->post->post_author)) {
			return $user;
		}
	}
	
	
	function getAuthorEmail() {
		if ($user = $this->getAuthor()) {
			return $user->user_email;
		}
	}
	
	
	function getAuthorDisplayName() {
		if ($user = $this->getAuthor()) {
			return $user->display_name;
		}
	}
	
	function getAuthorLogin() {
		if ($user = $this->getAuthor()) {
			return $user->user_login;
		}
	}
	
	
	function getCreatedDate() {
		return $this->post->post_date;
	}
	
	
	function formatCreatedDate() {
		return get_the_date(get_option('date_format'), $this->getId());
	}
	
	function getModifiedDate() {
		return $this->post->post_modified;
	}
	
	function formatModifiedDate() {
		return get_the_modified_date(get_option('date_format'), $this->getId());
	}
	
	
	function getPostParent() {
		return $this->getParentId();
	}
	
	
	function getParentId() {
		if (!empty($this->post->post_parent)) {
			return $this->post->post_parent;
		}
	}
	
}
