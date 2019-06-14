<?php

namespace com\cminds\registration\widget;

use com\cminds\registration\widget\Widget;

use com\cminds\registration\model\Settings;

use com\cminds\registration\shortcode\LoginFormShortcode;

class LoginFormWidget extends Widget {

	const WIDGET_NAME = 'Login Form from CM Registration';
	const WIDGET_DESCRIPTION = 'Displays the login form.';
	
	static protected $widgetFields = array(
		'title' => array('type' => Settings::TYPE_STRING, 'default' => 'Login', 'label' => 'Widget title'),
	);
	
	
	function getWidgetContent($args, $instance) {
		return '<div class="cmreg-sidebar-widget">' . LoginFormShortcode::shortcode(array()) . '</div>';
	}
	
	
	function canDisplay($args, $instance) {
		return !is_user_logged_in();
	}
	

}
