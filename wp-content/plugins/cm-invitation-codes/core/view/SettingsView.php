<?php

namespace com\cminds\registration\view;
use com\cminds\registration\model\Settings;

require_once dirname(__FILE__) . '/SettingsViewAbstract.php';

class SettingsView extends SettingsViewAbstract {
	
	
	public function renderSubcategory($category, $subcategory) {
		$content = parent::renderSubcategory($category, $subcategory);
		if (strlen(strip_tags($content)) > 0) {
			return sprintf('<table><caption>%s</caption>%s</table>',
				esc_html($this->getSubcategoryTitle($category, $subcategory)),
				$content
			);
		}
	}
	
	
	public function renderOption($name, array $option = array()) {
		return sprintf('<tr>%s</tr>', parent::renderOption($name, $option));
	}
	
	public function renderOptionTitle($option) {
		return sprintf('<th scope="row" class="option-title">%s:</th>', parent::renderOptionTitle($option));
	}
	
	public function renderOptionControls($name, array $option = array()) {
		return sprintf('<td class="option-controls">%s</td>', parent::renderOptionControls($name, $option));
	}
	
	public function renderOptionDescription($option) {
		return sprintf('<td class="option-description">%s</td>', parent::renderOptionDescription($option));
	}
	
	
	protected function getSubcategoryTitle($category, $subcategory) {
		$subcategories = $this->getSubcategories();
		if (isset($subcategories[$category]) AND isset($subcategories[$category][$subcategory])) {
			return __($subcategories[$category][$subcategory]);
		} else {
			return $subcategory;
		}
	}
	
	
	protected function getCategories() {
		return apply_filters('cmreg_settings_pages', Settings::$categories);
	}
	
	
	protected function getSubcategories() {
		return apply_filters('cmreg_settings_pages_groups', Settings::$subcategories);
	}
	
	
}
