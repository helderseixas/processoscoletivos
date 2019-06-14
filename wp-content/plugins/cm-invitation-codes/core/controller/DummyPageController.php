<?php

namespace com\cminds\registration\controller;

use com\cminds\registration\model\Settings;

use com\cminds\registration\App;

use com\cminds\registration\helper\DateTimeHelper;

use com\cminds\registration\model\User;

abstract class DummyPageController extends Controller {
	
	const QUERY_DUMMY_PAGE = 'cmreg_dummy_page';
	const DUMMY_POST_TYPE = 'page';
	const DUMMY_POST_ID = -1444672;
	const TITLE_SEPARATOR = '&gt;';
	
	
	/**
	 * Original query instance.
	 *
	 * @var \WP_Query
	 */
	static $query;
	
	
	static function bootstrap() {
		// Add extra filters
		static::$filters = array_merge(array(
// 				array('name' => 'template_include', 'priority' => 0, 'method' => 'grabQuery'),
				array('name' => 'template_include', 'priority' => PHP_INT_MAX),
				array('name' => 'posts_results', 'args' => 2, 'priority' => PHP_INT_MAX),
				array('name' => 'the_title', 'args' => 2, 'priority' => PHP_INT_MAX),
				array('name' => 'wp_title', 'args' => 3, 'priority' => PHP_INT_MAX),
				array('name' => 'the_content', 'priority' => PHP_INT_MAX),
			),
			static::$filters
		);
		static::$actions = array_merge(array(
				array('name' => 'template_redirect', 'priority' => 0, 'method' => 'grabQuery')
			),
			static::$actions);
		parent::bootstrap();
	}
	
	
	static function grabQuery() {
		global $wp_query;
		static::$query = $wp_query;
	}
	
	
	static function template_include($template) {
		global $wp_query, $wp_the_query, $post, $page;
		if (static::isDummyPageRequired()) {
			
			// Call this filter to set the WP SEO title before the $wp_query instance will be replaced:
			$wp_seo_title = apply_filters('wp_title', static::getDummyPostTitle(), '', '');

			// Replace the archive query with single-page query
			$newQuery = new \WP_Query(array(static::QUERY_DUMMY_PAGE => 1, 'ignore_sticky_posts' => 1));
			$posts = $newQuery->get_posts();
			$newQuery->is_singular = true;
			$newQuery->is_single = true;
			$newQuery->is_page = true;
			$post = $page = $posts[0];
			$wp_query = $newQuery;
			$wp_the_query = $newQuery;
			
			// Get template path from child theme or parent theme if doesn't exists.
			$pageTemplate = Settings::getPageTemplate();
			$template = get_stylesheet_directory() . '/' . $pageTemplate;
			if (!file_exists($template)) {
				$template = get_template_directory() . '/' . $pageTemplate;
			}
			
		}
	
		return $template;
	}
	
	
	static function posts_results($posts, $query) {
		if ($query->get(static::QUERY_DUMMY_PAGE)) {
			
			$post = (object)array(
				'ID' => static::getDummyPostId(),
				'post_title' => static::getDummyPostTitle(),
				'post_content' => static::getDummyPostContent(),
				'post_type' => static::DUMMY_POST_TYPE,
				'post_author' => User::getSomeAdminUserId(),
				'post_date' => DateTimeHelper::getMysqlDatetime(),
				'post_name' => Settings::getOption(Settings::OPTION_PERMALINK_PREFIX),
			);
			$posts = array($post);
		}
		return $posts;
	}
	
	
	static function getDummyPostId() {
		return static::DUMMY_POST_ID;
	}
	
	static function getDummyPostTitle() {
		return App::getPluginName();
	}
	
	
	static function getDummyPostContent() {
		return App::getPluginName();
	}
	
	
	static function the_title($title, $postId = null) {
		if ($postId == static::getDummyPostId()) {
			$title = static::getDummyPostTitle();
		}
		return $title;
	}
	
	
	static function wp_title($title, $sep = '', $seplocation = 'right') {
		if (static::isDummyPageRequired()) {
			$title = static::getDummyPostTitle();
			$title .= ' | ' . get_option('blogname');
		}
		return $title;
	}
	
	
	static function the_content($content) {
		return $content;
	}
	
	
	static function isDummyPageRequired(\WP_Query $query = null) {
		return false;
	}
	
	
}
