<?php

use com\cminds\registration\model\User;

use com\cminds\registration\model\Labels;

$writeTextField = function($label, $name, $value, $type = 'text', $required = false, $maxlen = null) {
	$required = ($required ? ' required' : '');
	$maxlen = ($maxlen ? ' maxlength="'. $maxlen .'"' : '');
	printf('<p class="cmreg-field-%s"><label>%s</label><input type="%s" name="%s" value="%s"%s /></p>',
		esc_attr($name), $label, esc_attr($type), esc_attr($name), esc_attr($value), $required . $maxlen);
};

$writeTextareaField = function($label, $name, $value, $type = 'text') {
	printf('<p class="cmreg-field-%s"><label>%s</label><textarea name="%s">%s</textarea></p>', esc_attr($name), $label, esc_attr($name), esc_html($value));
};

$user = User::getUserData();
// var_dump($user);

?>

<form action="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>" method="post" class="cmreg-form cmreg-profile-edit-form">

	<?php if ($atts['showheader']): ?>
		<h3><?php echo Labels::getLocalized('user_profile_edit_form_header'); ?></h3>
	<?php endif; ?>

	<?php $writeTextField(Labels::getLocalized('user_profile_display_name'), 'display_name', $user->display_name, 'text', $required = true, $maxlength = 255); ?>
	<?php $writeTextField(Labels::getLocalized('user_profile_email'), 'email', $user->user_email, 'email', $required = true, $maxlength = 255); ?>
	<?php $writeTextField(Labels::getLocalized('user_profile_website'), 'website', $user->website); ?>
	<?php $writeTextareaField(Labels::getLocalized('user_profile_description'), 'description', $user->description); ?>
	
	<?php do_action('cmreg_profile_edit_form', $user->ID); ?>
	
	<div class="form-summary">
		<input type="hidden" name="action" value="cmreg_user_profile_edit" />
		<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
		<input type="submit" value="<?php echo esc_attr(Labels::getLocalized('user_profile_save_btn')); ?>" class="button button-primary" />
	</div>

</form>