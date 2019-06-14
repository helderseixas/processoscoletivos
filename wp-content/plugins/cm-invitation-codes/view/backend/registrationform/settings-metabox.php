<?php

use com\cminds\registration\helper\FormHtml;

?>

<div class="cmreg-field cmreg-slug">
	<span>Slug:</span>
	<input type="text" name="slug" value="<?php echo esc_attr($form->getSlug()); ?>">
	<p class="description">Used to easily recognize the field eg. in shortcodes.</p>
</div>

<div class="cmreg-field cmreg-role">
	<span>Wordpress role:</span>
	<?php echo FormHtml::selectBox('role', $rolesOptions, $form->getRole()); ?>
	<p class="description">You can choose a role the user will receive after a registration with this form.</p>
</div>

