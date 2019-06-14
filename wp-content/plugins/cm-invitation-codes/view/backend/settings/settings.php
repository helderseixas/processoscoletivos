<?php

use com\cminds\registration\controller\SettingsController;
use com\cminds\registration\view\SettingsView;
use com\cminds\registration\App;
use com\cminds\registration\model\Settings;


if (!empty($_GET['status']) AND !empty($_GET['msg'])) {
	printf('<div id="message" class="%s"><p>%s</p></div>', ($_GET['status'] == 'ok' ? 'updated' : 'error'), esc_html($_GET['msg']));
}

$settingsView = new SettingsView();

?>

<form method="post" id="settings">

<ul class="cmreg-settings-tabs"><?php

$tabs = apply_filters('cmreg_settings_pages', Settings::$categories);
foreach ($tabs as $tabId => $tabLabel) {
	printf('<li><a href="#tab-%s">%s</a></li>', $tabId, $tabLabel);
}

?></ul>

<div class="inner"><?php

echo $settingsView->render();

?></div>

<p class="form-finalize">
	<a href="<?php echo esc_attr($clearCacheUrl); ?>" class="right button">Clear cache</a>
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(SettingsController::getMenuSlug()); ?>" />
	<input type="submit" value="Save" class="button button-primary" />
</p>

</form>
