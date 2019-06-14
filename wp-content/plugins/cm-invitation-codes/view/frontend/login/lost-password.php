<?php

use com\cminds\registration\controller\LoginController;

use com\cminds\registration\model\Labels;

$lostPassNonce = wp_create_nonce(LoginController::LOST_PASS_NONCE);

?>

<form class="cmreg-lost-password-form" data-ajax-url="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>">
	<h2><?php echo Labels::getLocalized('lost_pass_header'); ?></h2>
	<div class="cmreg-lost-password-text"><?php echo Labels::getLocalized('lost_pass_text'); ?></div>
	<div class="cmreg-lost-password-fieldset">
		<input type="email" class="text" name="email" required placeholder="<?php echo esc_attr(Labels::getLocalized('field_email')); ?>" />
		<input type="hidden" name="action" value="cmreg_lost_password" />
		<input type="hidden" name="nonce" value="<?php echo $lostPassNonce; ?>" />
		<button type="submit"><?php echo Labels::getLocalized('lost_pass_submit_btn'); ?></button>
	</div>
</form>