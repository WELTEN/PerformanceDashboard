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

function getParams($a_params) {
	//indicates number of activity indicator, now in [0..5]
	$indicatorNr = $a_params['indicatorNr'];

	//indicates number for sub activity indicator, possible value depends on value of indicatorNr
	$subIndicatorNr = $a_params['subIndicatorNr'];

	$f_params = array();

	//indicates for what other users data is visible. 0=mean other, 1=anonymous others, 2=others
	$f_params['privacyLevel'] = $a_params['privacyLevel'];

	//0=all groups, so elgg total. If >0 then data is filtered for group guid
	$f_params['groupGuid'] = $a_params['groupGuid'];

	//indicates if activity and performance should be shared with other users. 0=no, 1=yes
	$f_params['shareWithOthers'] = $a_params['shareWithOthers'];

	//indicates if activity and performance should always shared. 0=no, 1=yes
	$f_params['alwaysShowOthers'] = $a_params['alwaysShowOthers'];

	//comma separated list of user guids that must be excluded from the data
	//if one of these users is the current user, me should be excluded from the data
	$f_params['excludedFromDashboardUserGuids'] = $a_params['excludedFromDashboardUserGuids'];

	//if >0 get all data starting from startEpoch
	$f_params['startEpoch'] = $a_params['startEpoch'];

	//if >0 get all data till endEpoch
	$f_params['endEpoch'] = $a_params['endEpoch'];

	//get all data in intervals between startEpoch and endEpoch
	$f_params['numberOfMonths'] = $a_params['numberOfMonths'];

	//get all data in intervals between startEpoch and endEpoch
	$f_params['numberOfIntervals'] = $a_params['numberOfIntervals'];

	//get end date, it is in format yyyy-mm-dd
	$f_params['endDate'] = $a_params['endDate'];

	//indicates for which users data should be given. 0=all users, 1=current user, 2=all but current user
	$f_params['userCollectionNr'] = $a_params['userCollectionNr'];

	//minimal 1 interval
	if ($f_params['numberOfIntervals'] == 0) {
		$f_params['numberOfIntervals'] = 1;
	}

	//determine endEpoch if not set
	if ($f_params['endEpoch'] == 0) {
		if ($f_params['endDate'] != '') {
			$f_params['endEpoch'] = strtotime($f_params['endDate']);
		}
		else {
			//set it to now
			$f_params['endEpoch'] = time();
		}
	}
	//if numberOfMonths > 0, set endEpoch to end of month so intervals roughly correspond to months
	if ($f_params['numberOfMonths'] > 0) {
		$month = date("m",$f_params['endEpoch']);
		$year = date("Y",$f_params['endEpoch']);
		$f_params['endEpoch'] = mktime(23, 59, 59, ++$month, 0, $year);
	}

	//determine $startEpoch
	if ($f_params['numberOfMonths'] > 0) {
		$f_params['startEpoch'] = strtotime("-" . $f_params['numberOfMonths'] . " Months", $f_params['endEpoch']);
	}
	else {
		if ($f_params['startEpoch'] == 0) {
			//get oldest timestamp in database
			$sql = " SELECT posted AS time FROM elgg_river ORDER BY posted ASC LIMIT 1";
			$f_params['startEpoch'] = getTime($sql);
		}
	}
	$f_params['intervalTime'] = ($f_params['endEpoch'] - $f_params['startEpoch']) / $f_params['numberOfIntervals'];

	$users = getUsers($f_params);
	$f_params['users'] = $users;

	$showUsers = getShowUsers($f_params);
	$f_params['showUsers'] = $showUsers;

	$userGuids = getUserGuids($f_params);
	$f_params['userGuids'] = $userGuids;

	$userNames = getUserNames($f_params);
	$f_params['userNames'] = $userNames;

//	$numberOfCreatedUsers = getNumberOfIntervalUsers($f_params, "create");
//	$numberOfBannedUsers = getNumberOfIntervalUsers($f_params, "ban");
	$numberOfUsers = getNumberOfUsers($f_params);
//	$f_params['numberOfCreatedUsers'] = $numberOfCreatedUsers;
//	$f_params['numberOfBannedUsers'] = $numberOfBannedUsers;
	$f_params['numberOfUsers'] = $numberOfUsers;

	return $f_params;
}

function getSql($a_params) {
	//indicates number of activity indicator, now in [0..5]
	$indicatorNr = $a_params['indicatorNr'];

	//indicates number for sub activity indicator, possible value depends on value of indicatorNr
	$subIndicatorNr = $a_params['subIndicatorNr'];

	$f_params = array();

	//indicates for what other users data is visible. 0=mean other, 1=anonymous others, 2=others
	$f_params['privacyLevel'] = $a_params['privacyLevel'];

	//0=all groups, so elgg total. If >0 then data is filtered for group guid
	$f_params['groupGuid'] = $a_params['groupGuid'];

	//indicates if activity and performance should be shared with other users. 0=no, 1=yes
	$f_params['shareWithOthers'] = $a_params['shareWithOthers'];

	//indicates if activity and performance should always shared. 0=no, 1=yes
	$f_params['alwaysShowOthers'] = $a_params['alwaysShowOthers'];

	//comma separated list of user guids that must be excluded from the data
	//if one of these users is the current user, me should be excluded from the data
	$f_params['excludedFromDashboardUserGuids'] = $a_params['excludedFromDashboardUserGuids'];

	//if >0 get all data starting from startEpoch
	$f_params['startEpoch'] = $a_params['startEpoch'];

	//if >0 get all data till endEpoch
	$f_params['endEpoch'] = $a_params['endEpoch'];

	//get all data in intervals between startEpoch and endEpoch
	$f_params['numberOfMonths'] = $a_params['numberOfMonths'];

	//get all data in intervals between startEpoch and endEpoch
	$f_params['numberOfIntervals'] = $a_params['numberOfIntervals'];

	//get end date, it is in format yyyy-mm-dd
	$f_params['endDate'] = $a_params['endDate'];

	//indicates for which users data should be given. 0=all users, 1=current user, 2=all but current user
	$f_params['userCollectionNr'] = $a_params['userCollectionNr'];

	//minimal 1 interval
	if ($f_params['numberOfIntervals'] == 0) {
		$f_params['numberOfIntervals'] = 1;
	}

	//determine endEpoch if not set
	if ($f_params['endEpoch'] == 0) {
		if ($f_params['endDate'] != '') {
			$f_params['endEpoch'] = strtotime($f_params['endDate']);
		}
		else {
			//set it to now
			$f_params['endEpoch'] = time();
		}
	}
	//if numberOfMonths > 0, set endEpoch to end of month so intervals roughly correspond to months
	if ($f_params['numberOfMonths'] > 0) {
		$month = date("m",$f_params['endEpoch']);
		$year = date("Y",$f_params['endEpoch']);
		$f_params['endEpoch'] = mktime(23, 59, 59, ++$month, 0, $year);
	}

	//determine $startEpoch
	if ($f_params['numberOfMonths'] > 0) {
		$f_params['startEpoch'] = strtotime("-" . $f_params['numberOfMonths'] . " Months", $f_params['endEpoch']);
	}
	else {
		if ($f_params['startEpoch'] == 0) {
			//get oldest timestamp in database
			$sql = " SELECT posted AS time FROM elgg_river ORDER BY posted ASC LIMIT 1";
			$f_params['startEpoch'] = getTime($sql);
		}
	}
	$f_params['intervalTime'] = ($f_params['endEpoch'] - $f_params['startEpoch']) / $f_params['numberOfIntervals'];

	$users = getUsers($f_params);
	$f_params['users'] = $users;

	$showUsers = getShowUsers($f_params);
	$f_params['showUsers'] = $showUsers;

	$userGuids = getUserGuids($f_params);
	$f_params['userGuids'] = $userGuids;

	$userNames = getUserNames($f_params);
	$f_params['userNames'] = $userNames;

//	$numberOfCreatedUsers = getNumberOfIntervalUsers($f_params, "create");
//	$numberOfBannedUsers = getNumberOfIntervalUsers($f_params, "ban");
	$numberOfUsers = getNumberOfUsers($f_params);
//	$f_params['numberOfCreatedUsers'] = $numberOfCreatedUsers;
//	$f_params['numberOfBannedUsers'] = $numberOfBannedUsers;
	$f_params['numberOfUsers'] = $numberOfUsers;

	return getNumberOfCreatedAndUpdatedObjectsSql($f_params);
}

function getNumberOfCreatedAndUpdatedObjectsSql($f_params) {
	//I'm productive, number of objects created and updated, by all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='object'" .
		" AND er.action_type IN ('create','update')";
	$sql = sqlExcludeUserGuids($sql, "er.subject_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "er.subject_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "er.subject_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return $sql;
}

function getActivityData($a_params) {
	//indicates number of activity indicator, now in [0..5]
	$indicatorNr = $a_params['indicatorNr'];

	//indicates number for sub activity indicator, possible value depends on value of indicatorNr
	$subIndicatorNr = $a_params['subIndicatorNr'];

	$f_params = array();

	//indicates for what other users data is visible. 0=mean other, 1=anonymous others, 2=others
	$f_params['privacyLevel'] = $a_params['privacyLevel'];

	//0=all groups, so elgg total. If >0 then data is filtered for group guid
	$f_params['groupGuid'] = $a_params['groupGuid'];

	//indicates if activity and performance should be shared with other users. 0=no, 1=yes
	$f_params['shareWithOthers'] = $a_params['shareWithOthers'];

	//indicates if activity and performance should always shared. 0=no, 1=yes
	$f_params['alwaysShowOthers'] = $a_params['alwaysShowOthers'];

	//comma separated list of user guids that must be excluded from the data
	//if one of these users is the current user, me should be excluded from the data
	$f_params['excludedFromDashboardUserGuids'] = $a_params['excludedFromDashboardUserGuids'];

	//if >0 get all data starting from startEpoch
	$f_params['startEpoch'] = $a_params['startEpoch'];

	//if >0 get all data till endEpoch
	$f_params['endEpoch'] = $a_params['endEpoch'];

	//get all data in intervals between startEpoch and endEpoch
	$f_params['numberOfMonths'] = $a_params['numberOfMonths'];

	//get all data in intervals between startEpoch and endEpoch
	$f_params['numberOfIntervals'] = $a_params['numberOfIntervals'];

	//get end date, it is in format yyyy-mm-dd
	$f_params['endDate'] = $a_params['endDate'];

	//indicates for which users data should be given. 0=all users, 1=current user, 2=all but current user
	$f_params['userCollectionNr'] = $a_params['userCollectionNr'];

	//minimal 1 interval
	if ($f_params['numberOfIntervals'] == 0) {
		$f_params['numberOfIntervals'] = 1;
	}

	//determine endEpoch if not set
	if ($f_params['endEpoch'] == 0) {
		if ($f_params['endDate'] != '') {
			$f_params['endEpoch'] = strtotime($f_params['endDate']);
		}
		else {
			//set it to now
			$f_params['endEpoch'] = time();
		}
	}
	//if numberOfMonths > 0, set endEpoch to end of month so intervals roughly correspond to months
	if ($f_params['numberOfMonths'] > 0) {
		$month = date("m",$f_params['endEpoch']);
		$year = date("Y",$f_params['endEpoch']);
		$f_params['endEpoch'] = mktime(23, 59, 59, ++$month, 0, $year);
	}

	//determine $startEpoch
	if ($f_params['numberOfMonths'] > 0) {
		$f_params['startEpoch'] = strtotime("-" . $f_params['numberOfMonths'] . " Months", $f_params['endEpoch']);
	}
	else {
		if ($f_params['startEpoch'] == 0) {
			//get oldest timestamp in database
			$sql = " SELECT posted AS time FROM elgg_river ORDER BY posted ASC LIMIT 1";
			$f_params['startEpoch'] = getTime($sql);
		}
	}
	$f_params['intervalTime'] = ($f_params['endEpoch'] - $f_params['startEpoch']) / $f_params['numberOfIntervals'];

	$users = getUsers($f_params);
	$f_params['users'] = $users;

	$showUsers = getShowUsers($f_params);
	$f_params['showUsers'] = $showUsers;

	$userGuids = getUserGuids($f_params);
	$f_params['userGuids'] = $userGuids;

	$userNames = getUserNames($f_params);
	$f_params['userNames'] = $userNames;

//	$numberOfCreatedUsers = getNumberOfIntervalUsers($f_params, "create");
//	$numberOfBannedUsers = getNumberOfIntervalUsers($f_params, "ban");
	$numberOfUsers = getNumberOfUsers($f_params);
//	$f_params['numberOfCreatedUsers'] = $numberOfCreatedUsers;
//	$f_params['numberOfBannedUsers'] = $numberOfBannedUsers;
	$f_params['numberOfUsers'] = $numberOfUsers;

	$data = "";
	if ($indicatorNr == 0 && $subIndicatorNr == -1) {//I am productive
		$data = getUserResults(getData0($f_params), $f_params);
	} else if ($indicatorNr == 1 && $subIndicatorNr == -1) {//I deliver quality contributions
		$data = getUserResults(getData1($f_params), $f_params);
	} else if ($indicatorNr == 2 && $subIndicatorNr == -1) {//I am involved
		$data = getUserResults(getData2($f_params), $f_params);
	} else if ($indicatorNr == 3 && $subIndicatorNr == -1) {//I am socially active
		$data = getUserResults(getData3($f_params), $f_params);
	} else if ($indicatorNr == 4 && $subIndicatorNr == -1) {//I am available
		$data = getUserResults(getData4($f_params), $f_params);
	} else if ($indicatorNr == 5 && $subIndicatorNr == -1) {//I am reading
		$data = getUserResults(getData5($f_params), $f_params);
	} else if ($indicatorNr == 0 && $subIndicatorNr == 0) {//I am productive, number of objects created and updated, by all users, current user or all but current user
		$data = getResult(getNumberOfCreatedAndUpdatedObjects($f_params));
	} else if ($indicatorNr == 0 && $subIndicatorNr == 1) {//I am involved, number of comments to other users, for all users, current user or all but current user
		$data = getResult(getNumberOfCommentsToOthers($f_params));
	} else if ($indicatorNr == 1 && $subIndicatorNr == 0) {//I deliver quality contributions, number of ratings from others on created or updated objects and groups, for all users, current user or all but current user
		$data = getResult(getNumberOfRatingsFromOthers($f_params));
	} else if ($indicatorNr == 1 && $subIndicatorNr == 1) {//I deliver quality contributions, mean rating from others on created or updated objects and groups, for all users, current user or all but current user
		$data = getResult(getMeanRatingFromOthers($f_params));
	} else if ($indicatorNr == 2 && $subIndicatorNr == 0) {//I am involved, number of ratings to others on created or updated objects and groups, for all users, current user or all but current user
		$data = getResult(getNumberOfRatingsToOthers($f_params));
	} else if ($indicatorNr == 2 && $subIndicatorNr == 1) {//I am involved, number of comments to other users, for all users, current user or all but current user
		$data = getResult(getNumberOfCommentsToOthers($f_params));
	} else if ($indicatorNr == 3 && $subIndicatorNr == 0) {//I am socially active, number of friend requests by others, for all users, current user or all but current user
		$data = getResult(getNumberOfFriendRequestsFromOthers($f_params));
	} else if ($indicatorNr == 3 && $subIndicatorNr == 1) {//I am socially active, number of friend requests to others, for all users, current user or all but current user
		$data = getResult(getNumberOfFriendRequestsToOthers($f_params));
	} else if ($indicatorNr == 4 && $subIndicatorNr == 0) {//I am available, number of plugin views (also updates), for all users, current user or all but current user
		$data = getResult(getNumberOfPluginViews($f_params));
	}

	$result = '';
	if ($indicatorNr >= 0 && $subIndicatorNr >= 0) {
		$result .= "{";
		$result .= "\"userids\": " . getResult($userGuids) . ",\n";
		$result .= "\"usernames\": " . getStringResult($userNames) . ",\n";
		$result .= "\"showUsers\": " . getResult($showUsers) . ",\n";
		$result .= "\"count\": " . $data;
		$result .= "}";
	} else if ($indicatorNr >= 0 && $subIndicatorNr == -1){
		$result .= "{";
		$result .= "\"userids\": " . getResult($userGuids) . ",\n";
		$result .= "\"usernames\": " . getStringResult($userNames) . ",\n";
		$result .= "\"showUsers\": " . getResult($showUsers) . ",\n";
		$result .= "\"data\": " . $data;
		$result .= "}";
	} else {
		$result .= "{";
		$result .= "\"userids\": " . getResult($userGuids) . ",\n";
		$result .= "\"usernames\": " . getStringResult($userNames) . ",\n";
		$result .= "\"showUsers\": " . getResult($showUsers) . ",\n";
		$f_params['data0'] = getData0($f_params);
		$f_params['data1'] = getData1($f_params);
		$f_params['data2'] = getData2($f_params);
		$f_params['data3'] = getData3($f_params);
		$f_params['data4'] = getData4($f_params);
		$result .= 	"\"data0\": " . getUserResults($f_params['data0'], $f_params) . ",\n" .
				"\"data1\": " . getUserResults($f_params['data1'], $f_params) . ",\n" .
				"\"data2\": " . getUserResults($f_params['data2'], $f_params) . ",\n" .
				"\"data3\": " . getUserResults($f_params['data3'], $f_params) . ",\n" .
				"\"data4\": " . getUserResults($f_params['data4'], $f_params);
		$result .= "}";
	}
	return $result;
}

function getUsers($f_params) {
	$users;
	$sql = " SELECT eue.guid AS user_guid,eue.name AS user_name FROM elgg_users_entity AS eue";
	if ($f_params['privacyLevel'] == 1 || $f_params['privacyLevel'] == 2) {
		//use last_login to only get logged in users
		$sql.= " WHERE eue.last_login > 0";
		$sql = sqlExcludeUserGuids($sql, "eue.guid", $f_params);
		$sql = sqlAddUserCollectionConstraint1($sql, "eue.guid", $f_params);
		$sql = sqlAddMemberOfGroupConstraint($sql, "eue.guid", $f_params);
		$sql.= " ORDER BY eue.guid";
		$users = get_data($sql);
		//current user first
		for ($x=0; $x<sizeof($users); $x++) {
			if ($users[$x]->user_guid == get_loggedin_user()->guid) {
				$currentUser = $users[$x];
				for ($y=$x; $y>0; $y--) {
					$users[$y] = $users[$y-1];
				}
				$users[0] = $currentUser;
			}
		}
	}
	else {
		$sql.= " WHERE eue.guid=" . get_loggedin_user()->guid;
		$users = get_data($sql);
	}
	return $users;
}

function getShareWithOthers($f_params) {
	$shareWithOthers;
	$sql = " SELECT eue.guid AS user_guid,eps.value AS share_with_others FROM elgg_users_entity AS eue";
	$sql.= " INNER JOIN elgg_entities AS ee";
	$sql.= " INNER JOIN elgg_private_settings AS eps";
	$sql.= " WHERE ee.type='object'";
	$sql.= " AND ee.subtype=3";
	$sql.= " AND ee.owner_guid=eue.guid";
	$sql.= " AND ee.guid=eps.entity_guid";
	$sql.= " AND eps.name='shareWithOthers'";
	if ($f_params['privacyLevel'] == 1 || $f_params['privacyLevel'] == 2) {
		//use last_login to only get logged in users
		$sql.= " AND eue.last_login > 0";
		$sql = sqlExcludeUserGuids($sql, "eue.guid", $f_params);
		$sql = sqlAddUserCollectionConstraint1($sql, "eue.guid", $f_params);
		$sql = sqlAddMemberOfGroupConstraint($sql, "eue.guid", $f_params);
		$sql.= " ORDER BY eue.guid;";
		$shareWithOthers = get_data($sql);
	}
	else if ($f_params['privacyLevel'] == 0) {
		if ($f_params['userCollectionNr'] == 1) {
			$sql.= " WHERE eue.guid=" . get_loggedin_user()->guid;
			$shareWithOthers = get_data($sql);
		}
	}
	return $shareWithOthers;
}

function getShowUsers($f_params) {
	$shareWithOthers = getShareWithOthers($f_params);
	$result = array();
	if (($f_params['shareWithOthers'] == 1 || $f_params['alwaysShowOthers'] == 1) && ($f_params['privacyLevel'] == 1 || $f_params['privacyLevel'] == 2)) {
		$show_users = array();
		$counter = 0;
		//add group first
		$show_users[$counter] = 1;
		++$counter;
		if (!empty($f_params['users'])) {
			foreach ($f_params['users'] as $user) {
				$share = 0;
				if ($f_params['alwaysShowOthers'] == 1) {
					$share = 1;
				}
				else if (!empty($shareWithOthers)) {
					foreach ($shareWithOthers as $shareWithOther) {
						if ($user->user_guid == $shareWithOther->user_guid) {
							$share = $shareWithOther->share_with_others;
						}
					}
				}
				$show_users[$counter] = $share;
				++$counter;
			}
		}
		$result = $show_users;
	}
	else {
		$show_users = array();
		$show_users[0] = 1;
		if (!empty($f_params['users'])) {
			foreach ($f_params['users'] as $user) {
				if ($user->user_guid == get_loggedin_user()->guid) {
					$show_users[1] = 1;
				}
			}
		}
		$result = $show_users;
	}
	return $result;
}

function getUserGuids($f_params) {
	$result = array();
	$counter = 0;
	if (($f_params['privacyLevel'] == 0 && $f_params['userCollectionNr'] == 1) || $f_params['privacyLevel'] == 1 || $f_params['privacyLevel'] == 2) {
		if (!empty($f_params['users'])) {
			foreach ($f_params['users'] as $user) {
				$result[$counter] = $user->user_guid;
				++$counter;
			}
		}
	}
	return $result;
}

function getUserNames($f_params) {
	$result = array();
	if ($f_params['privacyLevel'] == 1 || $f_params['privacyLevel'] == 2) {
		$user_names = array();
		$counter = 0;
		//add group first
		$user_names[$counter] = elgg_echo("activityAndPerformanceDashboard:getDataGroup");
		++$counter;
		if (!empty($f_params['users'])) {
			$usercounter = 1;
			foreach ($f_params['users'] as $user) {
				if ($user->user_guid == get_loggedin_user()->guid) {
					$user_names[$counter] = elgg_echo("activityAndPerformanceDashboard:getDataMe");
				}
				else {
					if ($f_params['privacyLevel'] == 1) {
						$user_names[$counter] = elgg_echo("activityAndPerformanceDashboard:getDataUser") . " " . $usercounter;
						++$usercounter;
					}
					else {
						$user_names[$counter] = $user->user_name;
					}
				}
				++$counter;
			}
		}
		$result = $user_names;
	}
	else if ($f_params['privacyLevel'] == 0) {
		$user_names = array();
		$user_names[0] = elgg_echo("activityAndPerformanceDashboard:getDataGroup");
		if (!empty($f_params['users'])) {
			foreach ($f_params['users'] as $user) {
				if ($user->user_guid == get_loggedin_user()->guid) {
					$user_names[0] = elgg_echo("activityAndPerformanceDashboard:getDataGroup0");
					$user_names[1] = elgg_echo("activityAndPerformanceDashboard:getDataMe");
				}
			}
		}
		$result = $user_names;
	}
	return $result;
}

function getResult($data){
	$result = "[";
	for ($x=0; $x<sizeof($data); $x++) {
		if ($x > 0) {
			$result .= ",";
		}
		$result .= $data[$x];
	}
	$result .= "]";
	return $result;
}

function getUserResults($data, $f_params){
	$result = "{";
	for ($x=0; $x<sizeof($data); $x++) {
		if ($x > 0) {
			$result .= ",\n";
		}
		$result .= "\"user" . $x . "\": [";
		for ($y=0; $y<sizeof($data[$x][1]); $y++) {
			if ($y > 0) {
				$result .= ",";
			}
			$result .= $data[$x][1][$y];
		}
		$result .= "]";
	}
	$result .= "}";
	return $result;
}

function getStringResult($data){
	$result = "[";
	for ($x=0; $x<sizeof($data); $x++) {
		if ($x > 0) {
			$result .= ",";
		}
		$result .= "\"" . $data[$x] . "\"";
	}
	$result .= "]";
	return $result;
}

function getData0($f_params) {
	$weighting = array(1.0);
	//number of objects created and updated
	$f_params['userCollectionNr'] = 0;
	$data0 = array(getNumberOfCreatedAndUpdatedObjects($f_params));
	if ($f_params['privacyLevel'] == 0) {
		$f_params['userCollectionNr'] = 1;
		$data0[1] = getNumberOfCreatedAndUpdatedObjects($f_params);
		$f_params['userCollectionNr'] = 2;
		$data0[2] = getNumberOfCreatedAndUpdatedObjects($f_params);
	}
	$data = array($data0);
	return getDataResult($data, $weighting, $f_params);
}

function getData1($f_params) {
	$weighting = array(1.0);
	//number of comments to others
	$f_params['userCollectionNr'] = 0;
	$data0 = array(getNumberOfCommentsToOthers($f_params));
	if ($f_params['privacyLevel'] == 0) {
		$f_params['userCollectionNr'] = 1;
		$data0[1] = getNumberOfCommentsToOthers($f_params);
		$f_params['userCollectionNr'] = 2;
		$data0[2] = getNumberOfCommentsToOthers($f_params);
	}
	$data = array($data0);
	return getDataResult($data, $weighting, $f_params);
}

function getData2($f_params) {
	$weighting = array(1.0);
	//number of friend request to others
	$f_params['userCollectionNr'] = 0;
	$data0 = array(getNumberOfFriendRequestsToOthers($f_params));
	if ($f_params['privacyLevel'] == 0) {
		$f_params['userCollectionNr'] = 1;
		$data0[1] = getNumberOfFriendRequestsToOthers($f_params);
		$f_params['userCollectionNr'] = 2;
		$data0[2] = getNumberOfFriendRequestsToOthers($f_params);
	}
	$data = array($data0);
	return getDataResult($data, $weighting, $f_params);
}

function getData3($f_params) {
	$weighting = array(1.0);
	//number of plugin views
	$f_params['userCollectionNr'] = 0;
	$data0 = array(getNumberOfPluginViews($f_params));
	if ($f_params['privacyLevel'] == 0) {
		$f_params['userCollectionNr'] = 1;
		$data0[1] = getNumberOfPluginViews($f_params);
		$f_params['userCollectionNr'] = 2;
		$data0[2] = getNumberOfPluginViews($f_params);
	}
	$data = array($data0);
	return getDataResult($data, $weighting, $f_params);
}

function getData4($f_params) {
	$getData0 = $f_params['data0'];
	$getData1 = $f_params['data1'];
	$getData3 = $f_params['data3'];
	if ($f_params['privacyLevel'] == 0) {
		return getData4MeanOther($getData0, $getData1, $getData3, $f_params);
	}
	else {
		return getData4All($getData0, $getData1, $getData3, $f_params);
	}
}

function getData4MeanOther($numData1, $numData2, $denomData, $f_params) {
	$numberOfIntervals = $f_params['numberOfIntervals'];
	$result = array();
	$result[0] = array(elgg_echo("activityAndPerformanceDashboard:getDataGroup0"), array());
	$result[1] = array(elgg_echo("activityAndPerformanceDashboard:getDataMe"), array());
	for ($x=0; $x<$numberOfIntervals; $x++) {
		$othernum1 = $numData1[0][1][$x];
		$currentnum1 = $numData1[1][1][$x];
		$othernum2 = $numData2[0][1][$x];
		$currentnum2 = $numData2[1][1][$x];
		$otherdenom = $denomData[0][1][$x];
		$currentdenom = $denomData[1][1][$x];
		$other = getData4ByFormula($othernum1, $othernum2, $otherdenom);
		$current = getData4ByFormula($currentnum1, $currentnum2, $currentdenom);
		$total = $other + $current;
		if ($total > 0) {
			$other = $other / $total;
			$current = $current / $total;
		}
		//group first
		$result[0][1][$x] = $other;
		//then me
		$result[1][1][$x] = $current;
	}
	return $result;
}

function getData4All($numData1, $numData2, $denomData, $f_params) {
	$numberOfIntervals = $f_params['numberOfIntervals'];
	$result = array();
	for ($x=0; $x<sizeof($numData1); $x++) {
		$result[$x] = array($numData1[$x][0], array());
		for ($y=0; $y<$numberOfIntervals; $y++) {
			$result[$x][1][$y] = getData4ByFormula($numData1[$x][1][$y], $numData2[$x][1][$y], $denomData[$x][1][$y]);
			// if group value is 1 divide it by number of users
			if ($x == 0 && $result[$x][1][$y] == 1) {
				$result[$x][1][$y] = $result[$x][1][$y] / sizeof($f_params['users']);
			}
		}
	}
	$maxPerInterval = array();
	for ($x=0; $x<$numberOfIntervals; $x++) {
		$maxPerInterval[$x] = 0;
	}
	//NOTE start with $x=1 because values before are group values and don't contribute to total
	for ($x=1; $x<sizeof($numData1); $x++) {
		for ($y=0; $y<$numberOfIntervals; $y++) {
			$maxPerInterval[$y] = max($maxPerInterval[$y], $result[$x][1][$y]);
		}
	}
	for ($x=0; $x<sizeof($numData1); $x++) {
		for ($y=0; $y<$numberOfIntervals; $y++) {
			if ($maxPerInterval[$y] == 0) {
				$result[$x][1][$y] = 0;
			}
			else {
				$result[$x][1][$y] = (1.0 * $result[$x][1][$y]) / $maxPerInterval[$y];
			}
		}
	}
	return $result;
}

function getData4ByFormula($numData1, $numData2, $denomData) {
	$numData = $numData1 + $numData2;
	$result = 0;
	if ($denomData > 0) {
		$result = $numData / $denomData;
	}
	else {
		if ($numData == 0) {
			$result =  0;
		}
		else {
			$result =  1;
		}
	}
	return log(1 + $result, 10);
}


function getDataResult($data, $weighting, $f_params) {
	if ($f_params['privacyLevel'] == 0) {
		return getDataResultMeanOther($data, $weighting, $f_params);
	}
	else {
		return getDataResultAll($data, $weighting, $f_params);
	}
}

function getDataResultMeanOther($data, $weighting, $f_params) {
	//$data contains a number of arrays related to indicators, whoose content should be weighted by $weighting, so per array in $data there is a weighting in $weighting.
	//An array in $data contains three sub arrays: one for all users, one for the current user and and for all other users.
	//A sub array contains data per interval, so its length is equal to the number of intervals.
	$numberOfIntervals = $f_params['numberOfIntervals'];
	$meanotheruser = array();
	$currentuser = array();
	for ($x=0; $x<$numberOfIntervals; $x++) {
		$other = 0;
		$current = 0;
		for ($y=0; $y<sizeof($data); $y++) {
			if ($data[$y][0][$x] > 0) {
				//weigh values for current and all other users and divide by value for all users to get values between 0 and 1
				$data[$y][1][$x] = $weighting[$y] * ($data[$y][1][$x] / $data[$y][0][$x]);
				$data[$y][2][$x] = $weighting[$y] * ($data[$y][2][$x] / $data[$y][0][$x]);
			}
			else {
				$data[$y][1][$x] = 0;
				$data[$y][2][$x] = 0;
			}
			//summarize values for current user and all other users over arrays in $data, thus over indicators
			$other = $other + $data[$y][2][$x];
			$current = $current + $data[$y][1][$x];
		}
		//correct value for all other users to get value for mean other user
//		$meanotheruser[$x] = correctForNumberOfUsers($other, $f_params['numberOfCreatedUsers'][$x] - $f_params['numberOfBannedUsers'][$x]);
		$meanotheruser[$x] = correctForNumberOfUsers($other, $f_params['numberOfUsers']);
		$currentuser[$x] = $current;
		//divide values for mean other and current user by their total to get value between 0 and 1.
		$total = $meanotheruser[$x] + $currentuser[$x];
		if ($total > 0) {
			$meanotheruser[$x] = $meanotheruser[$x] / $total;
			$currentuser[$x] = $currentuser[$x] / $total;
		}
	}
	$result = array();
	$result[0] = array(elgg_echo("activityAndPerformanceDashboard:getDataGroup0"), $meanotheruser);
	$result[1] = array(elgg_echo("activityAndPerformanceDashboard:getDataMe"), $currentuser);
	return $result;
}

function getDataResultAll($data, $weighting, $f_params) {
	//$data contains one array related to indicators, whoose content should be weighted by $weighting, so per array in $data there is a weighting in $weighting.
	//An array in $data contains one sub array, which contains a sub array per user.
	//This last sub array contains data per interval, so its length is equal to the number of intervals.
	$numberOfIntervals = $f_params['numberOfIntervals'];
	$userGuids = $f_params['userGuids'];
	$uservalues = array();
	$totalvalues = array();
	$maxvalues = array();
	for ($x=0; $x<$numberOfIntervals; $x++) {
		for ($y=0; $y<sizeof($data); $y++) {
			$sum = 0;
			//determine sum of data per interval over all users
			for ($z=0; $z<sizeof($userGuids); $z++) {
				$sum = $sum + $data[$y][0][$z][$x];
			}
			for ($z=0; $z<sizeof($userGuids); $z++) {
				if ($sum > 0) {
					//weigh value for each user and divide by value for all users to get values between 0 and 1
					$data[$y][0][$z][$x] = $weighting[$y] * ($data[$y][0][$z][$x] / $sum);
				}
				else {
					$data[$y][0][$z][$x] = 0;
				}
				//summarize values for all users over arrays in $data, thus over indicators
				$uservalues[$z][$x] = $uservalues[$z][$x] + $data[$y][0][$z][$x];
			}
		}
		//divide values for all users by their total to get value between 0 and 1.
		$totalvalues[$x] = 0;
		$maxvalues[$x] = 0;
		for ($z=0; $z<sizeof($userGuids); $z++) {
			$totalvalues[$x] = $totalvalues[$x] + $uservalues[$z][$x];
			$maxvalues[$x] = max($maxvalues[$x], $uservalues[$z][$x]);
		}
		if ($maxvalues[$x] > 0) {
			for ($z=0; $z<sizeof($userGuids); $z++) {
				$uservalues[$z][$x] = (1.0 * $uservalues[$z][$x]) / $maxvalues[$x];
			}
		}
	}
	$result = array();
	//group first
	$result[0] = array(elgg_echo("activityAndPerformanceDashboard:getDataGroup"), array());
	for ($x=0; $x<$numberOfIntervals; $x++) {
		if (sizeof($userGuids) == 0) {
			$result[0][1][$x] = 0;
		}
		else {
			$factor = 1;
			if ($maxvalues[$x] > 0) {
				$factor = (1.0 * $totalvalues[$x])/$maxvalues[$x];
			}
			$result[0][1][$x] = ($factor * $totalvalues[$x])/sizeof($userGuids);
		}
	}
	//then users
	$userNames = $f_params['userNames'];
	for ($z=0; $z<sizeof($userGuids); $z++) {
		$result[$z+1] = array($userNames[$z], array());
		for ($x=0; $x<$numberOfIntervals; $x++) {
			$result[$z+1][1][$x] = $uservalues[$z][$x];
		}
	}
	return $result;
}

function correctForNumberOfUsers($data, $numberOfUsers) {
	if ($numberOfUsers > 1) {
		$data = $data / ($numberOfUsers - 1);
	}
	return $data;
}

function getNumberOfIntervalUsers($f_params, $event) {
	//NOTE elgg_system_log is not reliable! it can be regularly archived by a widget logrotate which means you have only data from last week, month or year.
	$intervalsql = sqlGetInterval("esl.time_created", $f_params);
	//use last_login to only get logged in users
	$sql = " SELECT count(*) AS count" . $intervalsql . " FROM elgg_users_entity AS eue";
	if ($f_params['startEpoch'] > 0 || $f_params['endEpoch'] > 0 || $f_params['intervalTime'] > 0) {
		$sql.= " INNER JOIN elgg_system_log AS esl";
	}
	$sql.=	" WHERE eue.last_login > 0";
	//to count as a user the user must created before $endEpoch and not deleted before $startEpoch
	if ($f_params['endEpoch'] > 0) {
		$sql.=	" AND eue.guid=esl.performed_by_guid";
		$sql.=	" AND esl.object_class='ElggUser'";
		$sql.=	" AND esl.object_type='user'";
		$sql.=	" AND esl.event='" . $event . "'";
		$sql.=	" AND esl.time_created<=" . $f_params['endEpoch'];
	}
	$sql = sqlExcludeUserGuids($sql, "eue.guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "eue.guid", $f_params);
	$sql = sqlAddGroupBy($sql, "", "intervalnumber", 0, $f_params);
	$sql = sqlAddOrderBy($sql, "", "intervalnumber", 0, $f_params);
	return getCounts($sql, $f_params);
}

function getNumberOfUsers($f_params) {
	//use last_login to only get logged in users
	$sql = " SELECT count(*) AS count FROM elgg_users_entity AS eue";
	$sql.=	" WHERE eue.last_login > 0";
	$sql = sqlExcludeUserGuids($sql, "eue.guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "eue.guid", $f_params);
	return getCount($sql);
}

function getNumberOfCreatedAndUpdatedObjects($f_params) {
	//I'm productive, number of objects created and updated, by all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='object'" .
		" AND (er.subtype!='groupforumtopic' OR er.annotation_id=0)" .
		" AND (er.action_type='create' OR er.action_type='update')";
	$sql = sqlExcludeUserGuids($sql, "er.subject_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "er.subject_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "er.subject_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfCreatedGroups($f_params) {
	//I'm productive, number of groups created, by all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='group'" .
		" AND er.action_type='create'";
	$sql = sqlExcludeUserGuids($sql, "er.subject_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "er.subject_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "er.subject_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfRatingsFromOthers($f_params) {
	//I deliver quality contributions, number of ratings from others on created or updated objects and groups, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("ea.time_created", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_annotations AS ea" .
		" INNER JOIN elgg_entities AS ee" .
		" INNER JOIN elgg_metastrings AS em";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE ea.entity_guid=ee.guid" .
		" AND ea.name_id=em.id" .
		" AND em.string='fivestar'";
	$sql = sqlExcludeUserGuids($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint2($sql, "ea.owner_guid", "ee.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "ea.time_created", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getMeanRatingFromOthers($f_params) {
	//I deliver quality contributions, mean rating from others on created or updated objects and groups, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("ea.time_created", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT avg((SELECT string FROM elgg_metastrings WHERE id=ea.value_id)) AS value" . $intervalsql . $privacysql . " FROM elgg_annotations AS ea" .
		" INNER JOIN elgg_entities AS ee" .
		" INNER JOIN elgg_metastrings AS em";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE ea.entity_guid=ee.guid" .
		" AND ea.name_id=em.id" .
		" AND em.string='fivestar'";
	$sql = sqlExcludeUserGuids($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "ea.time_created", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getCommentsFromOthers($f_params) {
	//I deliver quality contributions, number of comments from other users, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er" .
		" INNER JOIN elgg_annotations AS ea" .
		" INNER JOIN elgg_entities AS ee";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='object'" .
		" AND er.action_type='comment'" .
		" AND er.annotation_id=ea.id" .
		" AND ea.entity_guid=ee.guid";
	$sql = sqlExcludeUserGuids($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint2($sql, "ea.owner_guid", "ee.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfRatingsToOthers($f_params) {
	//I understand my co-member's needs, number of ratings to others on created or updated objects and groups, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("ea.time_created", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_annotations AS ea" .
		" INNER JOIN elgg_entities AS ee" .
		" INNER JOIN elgg_metastrings AS em";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE ea.entity_guid=ee.guid" .
		" AND ea.name_id=em.id" .
		" AND em.string='fivestar'";
	$sql = sqlExcludeUserGuids($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint2($sql, "ee.owner_guid", "ea.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "ea.time_created", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfCommentsToOthersOld($f_params) {
	//I understand my co-member's needs, number of comments to other users, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er" .
		" INNER JOIN elgg_annotations AS ea" .
		" INNER JOIN elgg_entities AS ee";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='object'" .
		" AND er.action_type='comment'" .
		" AND er.annotation_id=ea.id" .
		" AND ea.entity_guid=ee.guid";
	$sql = sqlExcludeUserGuids($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint2($sql, "ee.owner_guid", "ea.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ea.owner_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "ee.owner_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfCommentsToOthers($f_params) {
	//I understand my co-member's needs, number of comments to other users, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er" .
		" INNER JOIN elgg_entities AS ee";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='object'" .
		" AND (er.action_type='comment' OR (er.subtype='groupforumtopic' AND er.annotation_id>0))" .
		" AND er.object_guid=ee.guid";
	$sql = sqlExcludeUserGuids($sql, "er.subject_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "er.subject_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "er.subject_guid", $f_params);
	$sql = sqlAddEqualToGroupConstraint($sql, "ee.container_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfFriendRequestsFromOthers($f_params) {
	//Social, number of friend requests from others, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='user'" .
		" AND er.action_type='friend'";
	$sql = sqlExcludeUserGuids($sql, "er.object_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "er.object_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "er.subject_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfFriendRequestsToOthers($f_params) {
	//Social, number of friend requests to others, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='user'" .
		" AND er.action_type='friend'";
	$sql = sqlExcludeUserGuids($sql, "er.object_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "er.object_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "er.subject_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfJoinsToGroups($f_params) {
	//Social, number of joins to groups, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("er.posted", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_river AS er";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE er.type='group'" .
		" AND er.action_type='join'";
	$sql = sqlExcludeUserGuids($sql, "er.object_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "er.object_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "er.subject_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "er.posted", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "er.subject_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfPluginViews($f_params) {
//NOTE Elgg logs update events for object_class Elggplugin only for admins!!! It seems a bug because even if an admin just opens the plugin an event is logged.
//Instead we use all but ElggPlugin
/*	//get table names starting with elgg_system_log. if archiving of system log is set, tables are created with elgg_system_log_timestamp
	global $CONFIG;
	$sql = " SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES";
	$sql.=	" WHERE TABLE_SCHEMA='" . $CONFIG->dbname . "'";
	$sql.=	" AND TABLE_TYPE='BASE TABLE'";
	$sql.=	" AND TABLE_NAME LIKE 'elgg_system_log%'";
	$items = get_data($sql);
	if (!empty($items)) {
		foreach ($items as $item) {
			echo "<br/>" . $item->TABLE_NAME;
		}
	}
	//get all table names
	$items = get_db_tables();
	if (!empty($items)) {
		foreach ($items as $item) {
			echo "<br/>" . $item;
		}
	}*/
	//Consumer, number of plugin views (also updates), for all users, current user or all but current user
	$intervalsql = sqlGetInterval("esl.time_created", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_system_log AS esl";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
//	$sql.=	" WHERE esl.event='update'" .
//		" AND esl.object_class='ElggPlugin'";
//	$sql.=	" WHERE esl.event='create'" .
//		" AND esl.object_class='ElggMetadata'";
	$sql.=	" WHERE esl.object_class!='ElggPlugin'";
	$sql = sqlExcludeUserGuids($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "esl.time_created", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}

function getNumberOfLogins($f_params) {
	//Consumer, number of logins, for all users, current user or all but current user
	$intervalsql = sqlGetInterval("esl.time_created", $f_params);
	$privacysql = sqlGetPrivacyLevel($f_params);
	$sql = " SELECT count(*) AS value" . $intervalsql . $privacysql . " FROM elgg_system_log AS esl";
	$sql = sqlAddPrivacyLevelInnerJoin($sql, $f_params);
	$sql.=	" WHERE esl.event='login'";
	$sql = sqlExcludeUserGuids($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddUserCollectionConstraint1($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddMemberOfGroupConstraint($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddEpochConstraints($sql, "esl.time_created", $f_params);
	$sql = sqlAddPrivacyLevelAnd($sql, "esl.performed_by_guid", $f_params);
	$sql = sqlAddGroupBy($sql, "eue.guid", "intervalnumber", $f_params);
	$sql = sqlAddOrderBy($sql, "eue.guid", "intervalnumber", $f_params);
	return getValues($sql, $f_params);
}


function sqlGetInterval($fieldName, $f_params) {
	$sql = "";
	if ($f_params['intervalTime'] > 0) {
		$sql = " ,truncate((" . $fieldName . "-" . $f_params['startEpoch'] . ")/" . $f_params['intervalTime'] . ",0) AS intervalnumber";
	}
	return $sql;
}

function sqlExcludeUserGuids($sql, $fieldNameContainingUserGuid, $f_params) {
	if (strlen($f_params['excludedFromDashboardUserGuids']) > 0) {
		$sql.= " AND (NOT " . $fieldNameContainingUserGuid . " IN (" . $f_params['excludedFromDashboardUserGuids'] . "))";
	}
	return $sql;
}

function sqlAddUserCollectionConstraint1($sql, $fieldNameContainingUserGuid, $f_params) {
	//check if user guid given by $fieldNameContainingUserGuid is (un)equal to current user guid for userCollectionNr 1 or 2
	if ($f_params['userCollectionNr'] > 0 && $f_params['userCollectionNr'] < 3) {
		$operator = "";
		if ($f_params['userCollectionNr'] == 1) {
			$operator = "=";
		} else if ($f_params['userCollectionNr'] == 2) {
			$operator = "!=";
		}
		$sql.= " AND " . $fieldNameContainingUserGuid . $operator . get_loggedin_user()->guid;
	}
	return $sql;
}

function sqlAddUserCollectionConstraint2($sql, $fieldName1, $fieldName2, $f_params) {
	//check if
	//user collection indicates for which users data should be given. 0=all users, 1=current user, 2=all but current user
	if ($f_params['userCollectionNr'] == 0) {
		$sql.= " AND " . $fieldName1 . "!=" . $fieldName2;
	} else if ($f_params['userCollectionNr'] > 0 && $f_params['userCollectionNr'] < 3) {
		//NOTE order of fieldName1 and fieldName2 is important. Only fieldName1 is used in following statement.
		$sql.= " AND " . $fieldName1 . "!=" . get_loggedin_user()->guid;
	}
	return $sql;
}

function sqlAddMemberOfGroupConstraint($sql, $fieldNameContainingUserGuid, $f_params) {
	if ($f_params['groupGuid'] > 0) {
		$sql.= " AND " . $fieldNameContainingUserGuid . " IN (SELECT guid_one FROM elgg_entity_relationships WHERE guid_one!=" . $f_params['groupGuid'] . " AND guid_two=" . $f_params['groupGuid'] . " AND relationship='member')";
	}
	return $sql;
}

function sqlAddEqualToGroupConstraint($sql, $fieldNameContainingGroupGuid, $f_params) {
	if ($f_params['groupGuid'] > 0) {
		$sql.= " AND " . $fieldNameContainingGroupGuid . "=" . $f_params['groupGuid'];
	}
	return $sql;
}

function sqlAddEpochConstraints($sql, $fieldName, $f_params) {
	if ($f_params['startEpoch'] > 0) {
		$sql.= " AND " . $fieldName . ">=" . $f_params['startEpoch'];
	}
	if ($f_params['endEpoch'] > 0) {
		$sql.= " AND " . $fieldName . "<" . $f_params['endEpoch'];
	}
	return $sql;
}

function sqlAddGroupBy($sql, $fieldName1, $fieldName2, $f_params) {
	if ($f_params['privacyLevel'] > 0 || $f_params['intervalTime'] > 0) {
		if ($fieldName1 != "" || $fieldName2 != "") {
			$sql.= " GROUP BY ";
		}
		$tempsql = "";
		if ($f_params['privacyLevel'] > 0 && $fieldName1 != "") {
			$tempsql.= $fieldName1;
		}
		if ($f_params['intervalTime'] > 0 && $fieldName2 != "") {
			if ($tempsql != "") {
				$tempsql.= ",";
			}
			$tempsql.= $fieldName2;
		}
		$sql.= $tempsql;
	}
	return $sql;
}

function sqlAddOrderBy($sql, $fieldName1, $fieldName2, $f_params) {
	if ($f_params['privacyLevel'] > 0 || $f_params['intervalTime'] > 0) {
		if ($fieldName1 != "" || $fieldName2 != "") {
			$sql.= " ORDER BY ";
		}
		$tempsql = "";
		if ($f_params['privacyLevel'] > 0 && $fieldName1 != "") {
			$tempsql.= $fieldName1;
		}
		if ($f_params['intervalTime'] > 0 && $fieldName2 != "") {
			if ($tempsql != "") {
				$tempsql.= ",";
			}
			$tempsql.= $fieldName2;
		}
		$sql.= $tempsql;
	}
	return $sql;
}

function sqlGetPrivacyLevel($f_params) {
	$sql = "";
	if ($f_params['privacyLevel'] > 0) {
		$sql.= ", eue.guid AS user_guid, eue.name AS user_name";
	}
	return $sql;
}

function sqlAddPrivacyLevelInnerJoin($sql, $f_params) {
	if ($f_params['privacyLevel'] > 0) {
		$sql.= " INNER JOIN elgg_users_entity AS eue";
	}
	return $sql;
}

function sqlAddPrivacyLevelAnd($sql, $fieldName, $f_params) {
	if ($f_params['privacyLevel'] > 0) {
		$sql.= " AND eue.guid=" . $fieldName;
	}
	return $sql;
}


function getValue($sql) {
	//return value
	$value = 0;
	$items = get_data($sql);
	if (!empty($items)) {
		//NOTE Trick to get value, you have to add 'AS value' in sql query
		foreach ($items as $item) {
			if ($item->value != "") {
				$value = $item->value;
			}
		}
	} else {
      		$value = 0;
	}
	return $value;
}

function getValues($sql, $f_params) {
	$items = get_data($sql);
	if (empty($f_params['userGuids'])) {
		return getValuesOverIntervals($items, $f_params, 0);
	}
	else {
		return getValuesOverUserGuidsAndIntervals($items, $f_params);
	}
}

function getValuesOverIntervals($items, $f_params, $user_guid) {
	//return array of values for all intervals. If value is missing in sql query result, value is set to 0 for the corresponding interval.
	$numberOfIntervals = $f_params['numberOfIntervals'];
	$values = array();
	$counter = 0;
	if (!empty($items)) {
		//NOTE Trick to get user guid, you have to add 'AS user_guid' in sql query
		//NOTE Trick to get intervalnumber, you have to add 'AS intervalnumber' in sql query
		//NOTE Trick to get value, you have to add 'AS value' in sql query
		foreach ($items as $item) {
			$intervalnumber = $item->intervalnumber;
			if ($user_guid == 0 || $item->user_guid == $user_guid) {
				//NOTE intervalNumber=0 corresponds to first interval needed. Query can give negative intervalNumbers or larger than ($numberOfIntevals - 1) if values are found outside range given by $startEpoch and $endEpoch.
				if ($intervalnumber >= 0 && $intervalnumber < $numberOfIntervals) {
					$value = $item->value;
					if ($value == "") {
						$value = 0;
					}
					while ($counter < $intervalnumber && $counter < $numberOfIntervals) {
						$values[$counter] = 0;
						++$counter;
					}
					if ($counter < $numberOfIntervals) {
						$values[$counter] = $value;
						++$counter;
					}
				}
			}
		}
	}
	while ($counter < $numberOfIntervals) {
		$values[$counter] = 0;
		++$counter;
	}
	return $values;
}

function getValuesOverUserGuidsAndIntervals($items, $f_params) {
	//return array of values for all user guids and intervals. If value is missing in sql query result, value is set to 0 for the corresponding user guid and or interval.
	$userGuids = $f_params['userGuids'];
	$values = array();
	$counter = 0;
	foreach ($userGuids as $userGuid) {
		$values[$counter] = getValuesOverIntervals($items, $f_params, $userGuid);
		++$counter;
	}
	return $values;
}

function getCount($sql) {
	//return count
	$count = 0;
	$items = get_data($sql);
	if (!empty($items)) {
		//NOTE Trick to get count, you have to add 'AS count' in sql query
		foreach ($items as $item) {
			if ($item->count != "") {
				$count = $item->count;
			}
		}
	} else {
      		$count = 0;
	}
	return $count;
}

function getCounts($sql, $f_params) {
	//return array of counts for all intervals. Count is added by count of previous interval. If count is missing in sql query result, count is set to value of previous interval.
	//So counts will be something like 0,3,3,4,6,6,6,6,8,10
	$numberOfIntervals = $f_params['numberOfIntervals'];
	$counts = array();
	$items = get_data($sql);
	$counter = 0;
	$total = 0;
	if (!empty($items)) {
		//NOTE Trick to get intervalnumber, you have to add 'AS intervalnumber' in sql query
		//NOTE Trick to get count, you have to add 'AS count' in sql query
		foreach ($items as $item) {
			$intervalnumber = $item->intervalnumber;
			//NOTE intervalNumber=0 corresponds to first interval needed. Query can give negative intervalNumbers or larger than ($numberOfIntevals - 1) if counts are found outside range given by $startEpoch and $endEpoch.
			$count = $item->count;
			if ($count == "") {
				$count = 0;
			}
			if ($intervalnumber < 0) {
				//NOTE add count to get total at first interval
				$total = $total + $count;
			}
			else {
				while ($counter < $intervalnumber && $counter < $numberOfIntervals) {
					//NOTE if count is missing, count is set to value of previous interval
					$counts[$counter] = $total;
					++$counter;
				}
				if ($counter < $numberOfIntervals) {
					//NOTE increase total
					$total = $total + $count;
					$counts[$counter] = $total;
					++$counter;
				}
			}
		}
	}
	while ($counter < $numberOfIntervals) {
		$counts[$counter] = $total;
		++$counter;
	}
	return $counts;
}

function getTime($sql) {
	//return time
	$time = 0;
	$items = get_data($sql);
	if (!empty($items)) {
		//NOTE Trick to get time, you have to add 'as time' in sql query
		foreach ($items as $item) {
			$time = $item->time;
		}
	}
	return $time;
}
?>