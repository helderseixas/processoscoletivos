<?php

namespace com\cminds\registration\helper;

use com\cminds\registration\App;

class FormHtml {
	
	static function checkboxTree($fieldName, array $current, array $values, $parentId = 0) {
		$output = '';
		if (!empty($values[$parentId])) {
			$output .= '<ul class="'. App::prefix('-form-checkbox-tree') .'">';
			foreach ($values[$parentId] as $id => $label) {
				$output .= sprintf('<li><label><input type="checkbox" name="%s" value="%s"%s /><span>%s</span></label>%s</li>',
					$fieldName,
					esc_attr($id),
					checked(in_array($id, $current), true, false),
					esc_html($label),
					static::checkboxTree($fieldName, $current, $values, $id)
				);
			}
			$output .= '</ul>';
		}
		return $output;
	}
	
	
	static function selectBox($fieldName, array $options, $currentValue) {
		$content = '';
		foreach ($options as $value => $label) {
			$content .= sprintf('<option value="%s"%s>%s</option>',
				esc_attr($value),
				selected($value, $currentValue, false),
				esc_html($label)
			);
		}
		return sprintf('<select name="%s">%s</select>',
			esc_attr($fieldName),
			$content
		);
	}
	
}
