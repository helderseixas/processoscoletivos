<?php 

use com\cminds\registration\helper\GoogleMapsIcons;

?><?php if (!empty($term)) $template = <<<HTML
	<tr class="form-field">
		<th scope="row"><label for="cmreg_category_icon">%s</label></th>
		<td>%s</td>
	</tr>
HTML;
else $template = <<<HTML
	<div class="form-field">
		<label for="cmreg_category_icon">%s</label>
		%s
	</div>
HTML;


$options = '';
foreach ($icons as $icon) {
	$options .= sprintf('<img src="%s">', esc_attr($icon));
}


if (!empty($currentIcon)) {
	$current = '<img src="'. esc_attr($currentIcon) .'" class="cmreg_category_icon_image" />
		<input type="hidden" name="cmreg_category_icon" value="'. esc_attr($currentIcon) .'" />';
} else {
	$current = '<img class="cmreg_category_icon_image" /><input type="hidden" name="cmreg_category_icon" value="" />';
}


$content = <<<HTML
	<div class="cmreg_category_icon">
		<p>%s</p>
		<p><input type="button" value="Choose icon" class="cmreg_category_icon_choose" /></p>
		<div class="cmreg_category_icon_list" style="display:none">%s</div>
		<input type="hidden" name="%s" value="%s" />
	</div>
HTML;

$content = sprintf($content, $current, $options, $nonceField, $nonce);

printf($template, 'Default marker icon for new locations', $content);
