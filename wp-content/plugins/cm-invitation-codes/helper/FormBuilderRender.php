<?php

namespace com\cminds\registration\helper;

use com\cminds\registration\model\ProfileField;
use com\cminds\registration\model\Labels;

class FormBuilderRender {
	
	static function render($namespace, ProfileField $field, $userId = null) {
		$out = static::getLabel($field);
		$method = 'render' . ucfirst(preg_replace_callback('~\-([a-z])~', function($val) { return strtoupper($val[1]); }, $field->getFieldType()));
		if (method_exists(__CLASS__, $method)) {
			$out .= call_user_func(array(__CLASS__, $method), $namespace, $field, $userId);
		}
		$out .= static::getDescription($field);
		return $out;
	}
	
	
	protected static function getLabel(ProfileField $field) {
		$out = '<span class="cmreg-field-label">'. $field->getLabel();
		if ($field->isRequired()) {
			$out .= ' <span class="cmreg-field-required">'. Labels::getLocalized('field_required') .'</span>';
		}
		$out .= '</span>';
		return $out;
	}
	
	
	protected static function getDescription(ProfileField $field) {
		$out = '<span class="cmreg-field-description">'. $field->getTooltip() . '</span>';
		return $out;
	}
	
	
	protected static function renderText($namespace, ProfileField $field, $userId = null) {
		
		$name = $namespace . '['. $field->getUserMetaKey() .']';
		if ($field->getFieldType() == ProfileField::FIELD_TYPE_NUMBER OR $field->getFieldType() == ProfileField::FIELD_TYPE_DATE) {
			$subtype = $field->getFieldType();
		} else {
			$subtype = $field->getSubtype();
		}
		
		$out = '<input type="'. esc_attr($subtype) .'" name="'. esc_attr($name) .'"';
		
		$out .= static::renderAttributePlaceholder($field);
		$out .= static::renderAttributeClass($field);
		$out .= static::renderAttributeMaxlength($field);
		$out .= static::renderAttributeRequired($field);
		
		if ($userId) {
			$value = $field->getValueForUser($userId);
		} else {
			$value = $field->getDefaultValue();
		}
		if (strlen($value) > 0) {
			$out .= ' value="'. esc_attr($value) .'"';
		}
		
		$max = $field->getNumberMax();
		if (strlen($max) > 0) {
			$out .= ' max="'. esc_attr($max) .'"';
		}
		
		$min = $field->getNumberMin();
		if (strlen($min) > 0) {
			$out .= ' min="'. esc_attr($min) .'"';
		}
		
		$step = $field->getNumberStep();
		if (strlen($step) > 0) {
			$out .= ' step="'. esc_attr($step) .'"';
		}
		
		$out .= '>';
		return $out;
	}
	
	
	protected static function renderNumber($namespace, ProfileField $field, $userId = null) {
		return static::renderText($namespace, $field, $userId);
	}
	
	protected static function renderDate($namespace, ProfileField $field, $userId = null) {
		return static::renderText($namespace, $field, $userId);
	}
	
	
	protected static function renderTextarea($namespace, ProfileField $field, $userId = null) {
		$name = $namespace . '['. $field->getUserMetaKey() .']';
		$out = '<textarea name="'. esc_attr($name) .'"';
		
		$out .= static::renderAttributeMaxlength($field);
		$out .= static::renderAttributeRequired($field);
		$out .= static::renderAttributePlaceholder($field);
		$out .= static::renderAttributeClass($field);
		
		$rows = $field->getTextareaRows();
		if (strlen($rows) > 0) {
			$out .= ' rows="'. esc_attr($rows) .'"';
		}
		
		$out .= '>';
		
		if ($userId) {
			$value = $field->getValueForUser($userId);
		} else {
			$value = $field->getDefaultValue();
		}
		if (strlen($value) > 0) {
			$out .= esc_html($value);
		}
		
		$out .= '</textarea>';
		return $out;
	}
	
	
	protected static function renderSelect($namespace, ProfileField $field, $userId = null) {
		$name = $namespace . '['. $field->getUserMetaKey() .']';
		if ($field->isMultipleSelectionAllowed()) {
			$name .= '[]';
		}
		$out = '<select name="'. esc_attr($name) .'"';
		
		$out .= static::renderAttributeRequired($field);
		$out .= static::renderAttributeClass($field);
		
		if ($field->isMultipleSelectionAllowed()) {
			$out .= ' multiple';
		}
		
		$out .= '>';
		
		if ($userId) {
			$userValue = $field->getValueForUser($userId);
		} else {
			$userValue = null;
		}
		
		$options = $field->getOptionsValues();
		foreach ($options as $option) {
			if ($userId) {
				$selected = (is_array($userValue) ? in_array($option['value'], $userValue) : $option['value'] == $userValue);
			} else {
				$selected = !empty($option['selected']);
			}
			$out .= sprintf('<option value="%s"%s>%s</option>', esc_attr($option['value']), selected($selected, true, $echo = false), $option['label']);
		}
		
		$out .= '</select>';
		return $out;
	}
	
	protected static function renderRadioGroup($namespace, ProfileField $field, $userId = null) {
		$name = $namespace . '['. $field->getUserMetaKey() .']';
		$out = '<div class="cmreg-radio-group">';
		
			if ($userId) {
				$userValue = $field->getValueForUser($userId);
			} else {
				$userValue = null;
			}
	
			$options = $field->getOptionsValues();
			foreach ($options as $option) {
				$out .= '<label';
				$out .= static::renderAttributeClass($field);
				$out .= '>';
				
					if ($userId) {
						$checked = (is_array($userValue) ? in_array($option['value'], $userValue) : $option['value'] == $userValue);
					} else {
						$checked = !empty($option['selected']);
					}
				
					$out .= '<input type="radio" name="'. esc_attr($name) .'" value="'. esc_attr($option['value']) .'"';
					$out .= static::renderAttributeRequired($field);
					$out .= checked($checked, true, $echo = false);
					$out .= '><span class="cmreg-radio-label">' . $option['label'] .'</span>';
					
				$out .= '</label>';
			}
	
		$out .= '</div>';
		return $out;
	}
	
	
	protected static function renderCheckboxGroup($namespace, ProfileField $field, $userId = null) {
		$name = $namespace . '['. $field->getUserMetaKey() .']';
		$out = '<div class="cmreg-checkbox-group">';
		
		if ($userId) {
			$userValue = $field->getValueForUser($userId);
		} else {
			$userValue = null;
		}
		
		$options = $field->getOptionsValues();
		foreach ($options as $option) {
			$out .= '<label';
			$out .= static::renderAttributeClass($field);
			$out .= '>';
			
			if ($userId) {
				$checked = (is_array($userValue) ? in_array($option['value'], $userValue) : $option['value'] == $userValue);
			} else {
				$checked = !empty($option['selected']);
			}
			
			$out .= '<input type="checkbox" name="'. esc_attr($name) .'[]" value="'. esc_attr($option['value']) .'"';
			$out .= static::renderAttributeRequired($field);
			$out .= checked($checked, true, $echo = false);
			$out .= '><span class="cmreg-checkbox-label">' . $option['label'] .'</span>';
			$out .= '</label>';
		}
	
		$out .= '</div>';
		return $out;
	}
	
	
	protected static function renderAttributeMaxlength(ProfileField $field) {
		$maxlen = $field->getMaxLength();
		if (strlen($maxlen) > 0) {
			return ' maxlength="'. esc_attr($maxlen) .'"';
		}
	}
	
	protected static function renderAttributeRequired(ProfileField $field) {
		if ($field->isRequired()) {
			return ' required';
		}
	}
	
	protected static function renderAttributeClass(ProfileField $field) {
		$class = $field->getCSSClass();
		if (strlen($class) > 0) {
			return ' class="'. esc_attr($class) .'"';
		}
	}
	
	protected static function renderAttributePlaceholder(ProfileField $field) {
		$placeholder = $field->getPlaceholder();
		if (strlen($placeholder) > 0) {
			return ' placeholder="'. esc_attr($placeholder) .'"';
		}
	}
	
	
	
	
	
}