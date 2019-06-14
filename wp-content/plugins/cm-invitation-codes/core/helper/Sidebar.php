<?php

namespace com\cminds\registration\helper;

class Sidebar {
	
	static function addWidgetToSidebar($sidebarId, $widgetId, $where = 'prepend') {
		
		$widgetId = strtolower($widgetId);
		$sidebars = get_option('sidebars_widgets');
		
		$found = false;
		if (isset($sidebars[$sidebarId])) {
			foreach ($sidebars[$sidebarId] as $sidebarWidget) {
				if (substr($sidebarWidget, 0, strlen($widgetId)) == $widgetId) {
					$found = true;
					break;
				}
			}
		}
		
		if (!$found) {
			$instancesOption = 'widget_'. $widgetId;
			$instances = get_option($instancesOption);
			if (empty($instances)) $counter = 1;
			else $counter = max(array_keys($instances))+1;
			$newWidget = $widgetId .'-'. $counter;
			if ('prepend' == $where) array_unshift($sidebars[$sidebarId], $newWidget);
			else $sidebars[$sidebarId][] = $newWidget;
			
			$instances[$counter] = array();
				
			update_option($instancesOption, $instances);
			return update_option('sidebars_widgets', $sidebars);
			
		} else {
			return false;
		}
		
	}
	
	
	static function getWidgetSidebars($widgetId) {
		$widgetId = strtolower($widgetId);
		$results = array();
		$sidebars = get_option('sidebars_widgets');
		foreach ($sidebars as $sidebarId => $widgets) {
			if (is_array($widgets)) foreach ($widgets as $sidebarWidget) {
				if (substr($sidebarWidget, 0, strlen($widgetId)) == $widgetId) {
					if ('wp_inactive_widgets' != $sidebarId) {
						$results[] = $sidebarId;
					}
				}
			}
		}
		return $results;
	}
	
	
}
