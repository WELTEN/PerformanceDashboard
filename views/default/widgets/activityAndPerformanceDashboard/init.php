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

if (isloggedin()) {
	$privacySetting = get_plugin_setting('privacySetting', 'activityAndPerformanceDashboard');
	if (!$privacySetting) {
		$privacySetting = 0;
	}

//	NOTE Set TOC value default to 1 (true). Widget does not handle TOC anymore.
	if (!isset($vars['entity']->TOC)) {
//		$vars['entity']->TOC = 0;
		$vars['entity']->TOC = 1;
	}
	$TOC = $vars['entity']->TOC;
	//on some servers $TOC can be empty. If so, set it to 0
	if ($TOC == '') {
//		$TOC = 0;
		$TOC = 1;
	}

	$defaultShareWithOthers = get_plugin_setting('defaultShareWithOthers', 'activityAndPerformanceDashboard');
	if (!$defaultShareWithOthers) {
		$defaultShareWithOthers = 0;
	}
	if (!isset($vars['entity']->shareWithOthers)) {
		$vars['entity']->shareWithOthers = $defaultShareWithOthers;
	}
	$shareWithOthers = $vars['entity']->shareWithOthers;

	$defaultEndDate = get_plugin_setting('defaultEndDate', 'activityAndPerformanceDashboard');
	if (!$defaultEndDate) {
		$defaultEndDate = '';
	}
	if (!isset($vars['entity']->endDate) || $vars['entity']->endDate == '') {
		$vars['entity']->endDate = $defaultEndDate;
	}
	$endDate = $vars['entity']->endDate;

	//get widget settings for groups and users
	$monitorGroupGuids = get_plugin_setting('monitorGroupGuids', 'activityAndPerformanceDashboard');
	if (!$monitorGroupGuids) {
		$monitorGroupGuids = '';
	}
	$monitorGroupGuidsArr = array_map('trim',explode(",",$monitorGroupGuids));

	$tutorUserGuids = get_plugin_setting('tutorUserGuids', 'activityAndPerformanceDashboard');
	if (!$tutorUserGuids) {
		$tutorUserGuids = '';
	}
	$tutorUserGuidsArr = array_map('trim',explode(",",$tutorUserGuids));

	$excludedUserGuids = get_plugin_setting('excludedUserGuids', 'activityAndPerformanceDashboard');
	if (!$excludedUserGuids) {
		$excludedUserGuids = '';
	}
	$excludedUserGuidsArr = array_map('trim',explode(",",$excludedUserGuids));

	$excludedFromDashboardUserGuids = '';

	//determine if current user is admin, tutor, user or excluded user
	$userIsAdmin = elgg_is_admin_user(get_loggedin_user()->guid);

	$userIsTutor = false;
	foreach ($tutorUserGuidsArr as $tutorUserGuid) {
		if ($tutorUserGuid > 0) {
			if (get_loggedin_user()->guid == $tutorUserGuid) {
				$userIsTutor = true;
			}
			if ($excludedFromDashboardUserGuids != '') {
				$excludedFromDashboardUserGuids .= ",";
			}
			$excludedFromDashboardUserGuids .= $tutorUserGuid;
		}
	}

	$userIsExcluded = false;
	foreach ($excludedUserGuidsArr as $excludedUserGuid) {
		if ($excludedUserGuid > 0) {
			if (get_loggedin_user()->guid == $excludedUserGuid) {
				$userIsExcluded = true;
			}
			if ($excludedFromDashboardUserGuids != '') {
				$excludedFromDashboardUserGuids .= ",";
			}
			$excludedFromDashboardUserGuids .= $excludedUserGuid;
		}
	}

	if ($userIsExcluded) {
		//empty array
		$groups = array();
		$vars['entity']->groupGuid = 0;
	}
	else {
		//get all groups
		$sql = " SELECT * FROM elgg_groups_entity";
		if (!$userIsAdmin) {
			//if not admin filter on group membership
			$sql.= " WHERE guid IN";
			$sql.= " (SELECT guid_two FROM elgg_entity_relationships";
			$sql.= " WHERE guid_one=" . get_loggedin_user()->guid;
			$sql.= " AND relationship='member')";
		}
		$groups = get_data($sql);

		if (!empty($groups)) {
			//filter $groups on groups to monitor
			$newgroups = array();
			$counter = 0;
			foreach ($groups as $group) {
				foreach ($monitorGroupGuidsArr as $monitorGroupGuid) {
					if ($group->guid == $monitorGroupGuid) {
						$newgroups[$counter] = $group;
						$counter = $counter + 1;
					}
				}
			}
			$groups = $newgroups;

			//set initially shown group
			if (!isset($vars['entity']->groupGuid)) {
				$vars['entity']->groupGuid = $groups[0]->guid;
			}
			else {
				$exists = false;
				foreach ($groups as $group) {
					if ($group->guid == $vars['entity']->groupGuid) {
						$exists = true;
					}
				}
				if (!$exists) {
					$vars['entity']->groupGuid = $groups[0]->guid;
				}
			}
		}
	}
	$groupGuid = $vars['entity']->groupGuid;
	if ($groupGuid == null) {
		$groupGuid = 0;
	}

	$numberOfMonths = get_plugin_setting('numberOfMonths', 'activityAndPerformanceDashboard');
	if (!$numberOfMonths) {
		$numberOfMonths = 12;
	}


	$privacySetting = get_plugin_setting('privacySetting', 'activityAndPerformanceDashboard');
	if (!$privacySetting) {
		$privacySetting = 0;
	}

	$memberPlusGroupRating = get_plugin_setting('memberPlusGroupRating', 'activityAndPerformanceDashboard');
	if (!$memberPlusGroupRating || $memberPlusGroupRating == '') {
		//empty array
		$memberPlusGroupRating = '[]';
	}

	$generalGroupRating = get_plugin_setting('generalGroupRating', 'activityAndPerformanceDashboard');
	if (!$generalGroupRating || $generalGroupRating == '') {
		//empty json array
		$generalGroupRating = '[]';
	}

}
?>