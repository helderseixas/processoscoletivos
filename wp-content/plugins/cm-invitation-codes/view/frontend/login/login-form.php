<?php

use com\cminds\registration\controller\LoginController;

use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;

$loginField = Settings::getOption(Settings::OPTION_LOGIN_FIELD);
$loginFieldLabel = Labels::getLocalized('login_field_' . $loginField);
$loginFieldType = ($loginField == Settings::LOGIN_FIELD_EMAIL ? 'email' : 'text');
$redirectUrl = filter_input(INPUT_GET, 'cmreg_redirect_url');

?>
<div class="cmreg-login cmreg-wrapper">
	<form method="post" data-ajax-url="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>" class="cmreg-login-form">
		<h2><?php echo Labels::getLocalized('login_form_header'); ?></h2>
		<div class="cmreg-form-text"><?php echo Labels::getLocalized('login_form_text'); ?></div>
		<div class="cmreg-login-field"><input type="<?php echo $loginFieldType; ?>" class="text" name="login" required placeholder="<?php
			echo esc_attr($loginFieldLabel); ?>" /></div>
		<div class="cmreg-password-field"><input type="password" class="text" name="<?php echo LoginController::FIELD_PASS;
			?>" required placeholder="<?php echo esc_attr(Labels::getLocalized('field_password')); ?>" /></div>
		<?php if (Settings::getOption(Settings::OPTION_LOGIN_REMEMBER_ENABLE)): ?>
			<div class="cmreg-remember-field"><label><input type="checkbox" name="remember" value="1" /> <?php
				echo Labels::getLocalized('login_form_remember'); ?></label></div>
		<?php endif; ?>
		<?php do_action('login_form', 'cmreg_overlay'); ?>
		<div class="cmreg-buttons-field">
			<input type="hidden" name="action" value="cmreg_login" />
			<input type="hidden" name="cmreg_redirect_url" value="<?php echo esc_attr($redirectUrl); ?>" />
			<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
			<button type="submit"><span class="dashicons dashicons-admin-users"></span><?php echo Labels::getLocalized('login_form_submit_btn'); ?></button>
		</div>
		<?php do_action('login_footer'); ?>
		<?php do_action('cmreg_login_form_bottom', $atts); ?>
	</form>
	<?php if (Settings::getOption(Settings::OPTION_LOGIN_LOST_PASSWORD_ENABLE)): ?>
		<div class="cmreg-lost-password-link"><a href=""><?php echo Labels::getLocalized('lost_pass_btn'); ?></a></div>
		<?php echo LoginController::getLostPasswordView(); ?>
	<?php endif; ?>
	<?php if (isset($atts['registration-url'])): ?>
		<div class="cmreg-registration-link"><a href="<?php echo esc_attr($atts['registration-url']); ?>"><?php
			echo (isset($atts['registration-link']) ? $atts['registration-link'] : Labels::getLocalized('login_registration_btn')); ?></a></div>
	<?php endif; ?>
	<?php do_action('cmreg_login_wrapper_bottom', $atts); ?>
</div>