<?php include 'init.php';?>
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

function getUserSettings($a_params) {
	//get all students
	$sql = " SELECT ege.guid AS group_guid,eue.guid AS user_guid,eue.name AS user_name FROM elgg_users_entity AS eue";
	$sql.= " INNER JOIN elgg_entity_relationships AS eer";
	$sql.= " INNER JOIN elgg_groups_entity as ege";
	$sql.= " WHERE eue.last_login > 0";
	$sql.= " AND eer.relationship='member'";
	$sql.= " AND eer.guid_one=eue.guid";
	$sql.= " AND eer.guid_two=ege.guid";
	if ($a_params['excludedFromDashboardUserGuids'] != '') {
		$sql.= " AND NOT eue.guid IN (" . $a_params['excludedFromDashboardUserGuids'] . ")";
	}
	if ($a_params['monitorGroupGuids'] != '') {
		$sql.= " AND ege.guid IN (" . $a_params['monitorGroupGuids'] . ")";
	}
	$sql.= " ORDER BY ege.guid,eue.name;";
	$students = get_data($sql);

	//get settings
	$sql = " SELECT ege.guid AS group_guid,eue.guid AS user_guid,eps.value AS value FROM elgg_users_entity AS eue";
	$sql.= " INNER JOIN elgg_entities AS ee";
	$sql.= " INNER JOIN elgg_private_settings AS eps";
	$sql.= " INNER JOIN elgg_entity_relationships AS eer";
	$sql.= " INNER JOIN elgg_groups_entity as ege";
	$sql.= " WHERE ee.type='object'";
	$sql.= " AND ee.subtype=3";
	$sql.= " AND ee.owner_guid=eue.guid";
	$sql.= " AND ee.guid=eps.entity_guid";
	$sql.= " AND eps.name='" . $a_params['key'] . "'";
	$sql.= " AND eue.last_login > 0";
	$sql.= " AND eer.relationship='member'";
	$sql.= " AND eer.guid_one=eue.guid";
	$sql.= " AND eer.guid_two=ege.guid";
	$sql.= " ORDER BY ege.guid,eue.guid;";
	$settings = get_data($sql);
	$result = "<table style='width:500px'><tr><td>groupid</td><td>userid</td><td>" . $a_params['key'] . "</td><td>user name</td></tr>";
	if (!empty($students) && !empty($settings)) {
		foreach ($students as $student) {
			$value = "0";
			foreach ($settings as $setting) {
				if ($student->group_guid == $setting->group_guid && $student->user_guid == $setting->user_guid) {
					$value = $setting->value;
				}
			}
			if ($value == "0") {
				$value = "no";
			}
			else {
				$value = "yes";
			}
			$result = $result . "<tr><td>" . $student->group_guid . "</td><td>" . $student->user_guid . "</td><td>" . $value . "</td><td>" . $student->user_name . "</td></tr>";
		}
	}
	$result = $result . "</table>";
	return $result;
}

$TOCStyle = "visibility:hidden;height:0px;";
$shareWithOthersStyle = "visibility:hidden;height:0px;";
$chooseGroupStyle = "visibility:hidden;height:0px;";
$setEndDateStyle = "visibility:hidden;height:0px;";
//NOTE don't show TOC user settings anymore
$showUserSettingsStyleTOC = "visibility:hidden;height:0px;";
$showUserSettingsStyle = "visibility:hidden;height:0px;";
$userSettingsForTOC = "";
$userSettingsForShareWithOthers = "";
if (isloggedin()) {
	if (!empty($groups) && sizeof($groups) > 0 && $privacySetting > 0 && !$userIsAdmin && !$userIsTutor && !$userIsExcluded) {
//		NOTE Don't show TOC choice. Widget does not handle TOC anymore.
//		$TOCStyle = "";
		$shareWithOthersStyle = "";
	}

	$TOCOptions = array(0, 1);
	$TOCOptionValues = array('No', 'Yes');

	$shareWithOthersOptions = array(0, 1);
	$shareWithOthersOptionValues = array('No', 'Yes');

	if (!empty($groups) && sizeof($groups) > 1 && !$userIsExcluded) {
		$chooseGroupStyle = "";
	}

	if (!empty($groups) && sizeof($groups) > 0 && $userIsAdmin) {
		$setEndDateStyle = "";
	}

	if ($userIsAdmin) {
		$showUserSettingsStyle = '';
		$f_params = array();
		$f_params['excludedFromDashboardUserGuids'] = $excludedFromDashboardUserGuids;
		$f_params['monitorGroupGuids'] = $monitorGroupGuids;
		$f_params['key'] = "TOC";
//		NOTE don't show TOC user settings anymore
//		$userSettingsForTOC = getUserSettings($f_params);
		$f_params['key'] = "shareWithOthers";
		$userSettingsForShareWithOthers = getUserSettings($f_params);
	}
}

?>

<p>
	<div style="<?php echo elgg_echo($TOCStyle); ?>">
		<?php echo elgg_echo("activityAndPerformanceDashboard:TOC"); ?>:<br/>
		<select name="params[TOC]">
			<?php
				foreach ($TOCOptions as $option) {
					$selected = '';
					if ($vars['entity']->TOC == $option) {
						$selected = 'selected="selected"';
					}
					echo "<option value=\"$option\" $selected>$TOCOptionValues[$option]</option>";
				}
			?>
		</select>
	</div>
	<div style="<?php echo elgg_echo($shareWithOthersStyle); ?>">
		<?php echo elgg_echo("activityAndPerformanceDashboard:shareWithOthers"); ?>:<br/>
		<select name="params[shareWithOthers]">
			<?php
				foreach ($shareWithOthersOptions as $option) {
					$selected = '';
					if ($vars['entity']->shareWithOthers == $option) {
						$selected = 'selected="selected"';
					}
					echo "<option value=\"$option\" $selected>$shareWithOthersOptionValues[$option]</option>";
				}
			?>
		</select>
	</div>
	<div style="<?php echo elgg_echo($chooseGroupStyle ); ?>">
		<?php echo elgg_echo("activityAndPerformanceDashboard:chooseGroup"); ?>:<br/>
		<select name="params[groupGuid]">
			<?php
				foreach ($groups as $group) {
					$selected = '';
					if ($group->guid == $vars['entity']->groupGuid) {
						$selected = 'selected="selected"';
					}
					echo "<option value=\"$group->guid\" $selected>$group->name</option>";
				}
			?>
		</select>
	</div>
	<div style="<?php echo elgg_echo($setEndDateStyle ); ?>">
		<?php echo elgg_echo("activityAndPerformanceDashboard:setEndDate"); ?>:<br/>
		<input type="text" id="endDate" name="params[endDate]" value="<?php echo $endDate; ?>" style="width:200px;" />
	</div>
	<div style="<?php echo elgg_echo($showUserSettingsStyleTOC ); ?>">
		<?php echo elgg_echo("activityAndPerformanceDashboard:TOCSettings"); ?>:<br/>
		<div style="width:500px;height:100px;overflow:auto;background-color:white;">
			<?php echo $userSettingsForTOC; ?>
		</div>
	</div>
	<div style="<?php echo elgg_echo($showUserSettingsStyle ); ?>">
		<?php echo elgg_echo("activityAndPerformanceDashboard:ShareWithOthersSettings"); ?>:<br/>
		<div style="width:500px;height:100px;overflow:auto;background-color:white;">
			<?php echo $userSettingsForShareWithOthers; ?>
		</div>
	</div>
</p>