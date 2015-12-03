<?php

global $CONFIG;
function activityAndPerformanceDashboard_pagesetup(){
	global $CONFIG;
	//NOTE Must add submenu item to be able to call getData.php!
	if (get_context() == 'dashboard') {
		add_submenu_item('ActivityAndPerformanceDashboard', $CONFIG->url . 'pg/activityAndPerformanceDashboard');
	}
}

function activityAndPerformanceDashboard_init() {
	add_widget_type('activityAndPerformanceDashboard', 'Activity and Performance Dashboard', 'Activity and Performance Dashboard');
	register_elgg_event_handler('pagesetup', 'system', 'activityAndPerformanceDashboard_pagesetup');
}

register_elgg_event_handler('init', 'system', 'activityAndPerformanceDashboard_init');

?>