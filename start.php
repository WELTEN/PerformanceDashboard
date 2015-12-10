<?php

/*
 * Activity and performance dashboard.
 * Monitors group member activity and performance for a number of indicators and shows the results in spider diagrams and bar charts.
 * Copyright (C) 2015 Aad Slootmaker
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this program (/license.txt); if not,
 * write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 *     Contact information:
 *     Open University of the Netherlands
 *     Valkenburgerweg 177 Heerlen
 *     PO Box 2960 6401 DL Heerlen
 *     e-mail: aad.slootmaker@ou.nl
 *
 *
 * Open Universiteit Nederland, hereby disclaims all copyright interest
 * in the program Emergo written by
 * Aad Slootmaker
 *
 */

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