<?php

namespace com\cminds\registration\widget;

use com\cminds\registration\widget\Widget;

use com\cminds\registration\model\Settings;

use com\cminds\registration\shortcode\RegistrationFormShortcode;

class RegistrationFormWidget extends Widget {

	const WIDGET_NAME = 'Registration Form from CM Registration';
	const WIDGET_DESCRIPTION = 'Displays the registration form.';
	
	static protected $widgetFields = array(
		'title' => array('type' => Settings::TYPE_STRING, 'default' => 'Register', 'label' => 'Widget title'),
	);
	
	
	function getWidgetContent($args, $instance) {
		return '<div class="cmreg-sidebar-widget">' . RegistrationFormShortcode::shortcode(array()) . '</div>';
	}
	
	
	function canDisplay($args, $instance) {
		return !is_user_logged_in();
	}

}
