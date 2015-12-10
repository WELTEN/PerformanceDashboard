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

include 'init.php';
include 'getData.php';

$groupName = '';
if (!empty($groups)) {
	foreach ($groups as $group) {
		if ($group->guid == $vars['entity']->groupGuid) {
			$groupName = $group->name;
		}
	}
}
$groupVisibility = 'hidden';
if (!empty($groups) && sizeof($groups) > 0) {
	$groupVisibility = 'visible';
}
$dashboardVisibility = 'hidden';
if (!empty($groups) && sizeof($groups) > 0) {
	$dashboardVisibility = 'visible';
}

$CA_typeOfData = 'CA';
$CA_numberOfIntervals = '1';
$CA_privacyLevel = $privacySetting;
$PA_typeOfData = 'PA';
$PA_numberOfIntervals = $numberOfMonths;
$PA_privacyLevel = $privacySetting;
$MR_typeOfData = 'MR';
$MR_numberOfIntervals = $numberOfMonths;
$MR_privacyLevel = $privacySetting;
$GR_typeOfData = 'GR';
$GR_numberOfIntervals = $numberOfMonths;
$GR_privacyLevel = $privacySetting;

$CA_result = '[]';
$PA_result = '[]';
$MR_GR_monthly_result = '[]';
$GR_weekly_result = '[]';
if ($groupGuid > 0) {
	$a_params = array();
	$a_params['indicatorNr'] = -1;
	$a_params['subIndicatorNr'] = -1;
	$a_params['privacyLevel'] = $CA_privacyLevel;
	$a_params['groupGuid'] = $groupGuid;
	$a_params['shareWithOthers'] = $shareWithOthers;
	$a_params['alwaysShowOthers'] = $userIsAdmin || $userIsTutor;
	$a_params['excludedFromDashboardUserGuids'] = $excludedFromDashboardUserGuids;
	$a_params['numberOfMonths'] = $numberOfMonths;
	$a_params['numberOfIntervals'] = $CA_numberOfIntervals;
	$a_params['endDate'] = $endDate;
	$CA_result = getActivityData($a_params);

	$a_params['privacyLevel'] = $PA_privacyLevel;
	$a_params['numberOfIntervals'] = $PA_numberOfIntervals;
	$PA_result = getActivityData($a_params);

	$MR_GR_monthly_result = $memberPlusGroupRating;
	$GR_weekly_result = $generalGroupRating;
}

if ($endDate != '') {
	$lastMonthNr = date("m",strtotime($endDate));
	$lastYear = date("Y",strtotime($endDate));
}
else {
	$lastMonthNr = date("m",time());
	$lastYear = date("Y",time());
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" />
		<script type="text/javascript" src="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/vendors/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/vendors/jquery-ui.js"></script>

		<script type="text/javascript" src="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/vendors/d3.v3.min.js"></script>
		<script type="text/javascript" src="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/views/default/widgets/activityAndPerformanceDashboard/RadarChart.js"></script>

		<link rel="stylesheet" href="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/views/default/widgets/activityAndPerformanceDashboard/d3.slider.css" />
		<script type="text/javascript" src="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/views/default/widgets/activityAndPerformanceDashboard/d3.slider.js"></script>

		<link rel="stylesheet" href="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/views/default/widgets/activityAndPerformanceDashboard/style.css" />

		<script id="CA_bars_csv1" type="text/csv">Value,BarBorderColor,BarFillColor
		5.5,white,#FF9900
		4.5,white,steelblue</script>
		<script id="GR_bars_csv1" type="text/csv">Value,BarBorderColor,BarFillColor
		4.0,white,#FF9900
		8.0,white,steelblue</script>
		<script id="GR_bars_csv2" type="text/csv">Value,BarBorderColor,BarFillColor
		7.5,white,#FF9900
		8.5,white,steelblue</script>
		<script type="text/javascript" src="<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/views/default/widgets/activityAndPerformanceDashboard/draw.js"></script>
	</head>
	<body>
		<div style="visibility:<?php echo elgg_echo($groupVisibility); ?>;">
			<span style="position:relative;left:20px;font-size:125%;">
				<?php echo elgg_echo("activityAndPerformanceDashboard:group"); ?>: <?php echo elgg_echo($groupName); ?>
			</span>
			<hr />
		</div>
		<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all tabs-min" style="visibility:<?php echo elgg_echo($dashboardVisibility); ?>;">
			<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
				<li onclick="showSubtabs(1);" class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tabs-1" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true">
					<a href="#tabs-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1">
						<?php echo elgg_echo("activityAndPerformanceDashboard:tabs-1"); ?>
					</a>
				</li>
				<li onclick="showSubtabs(2);" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-2" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false">
					<a href="#tabs-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2">
						<?php echo elgg_echo("activityAndPerformanceDashboard:tabs-2"); ?>
					</a>
				</li>
			</ul>
			<div id="tabs-1" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false">
				<div id="subtabs1" class="ui-tabs ui-widget ui-widget-content ui-corner-all tabs-min">
					<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
						<li onclick="CA_loadAndRender();" class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tabs-1" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true">
							<a href="#subtabs1-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1">
								<?php echo elgg_echo("activityAndPerformanceDashboard:subtabs1-1"); ?>
							</a>
						</li>
						<li onclick="PA_loadAndRender();" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-2" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false">
							<a href="#subtabs1-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2">
								<?php echo elgg_echo("activityAndPerformanceDashboard:subtabs1-2"); ?>
							</a>
						</li>
					</ul>
					<div id="subtabs1-1" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false">
						<div id="CA_nodatayet" style="visibility:hidden;">
							<?php echo elgg_echo("activityAndPerformanceDashboard:noDataYet"); ?>
						</div>
						<div id="CA_spiderweb"></div>
						<div id="CA_spiderwebhover"></div>
						<div id="CA_bars">
							<span class="CA_bars_header"><b>
								<?php echo elgg_echo("activityAndPerformanceDashboard:barsTitleCA"); ?>
							</b></span>
							<div id="CA_bars1"></div>
						</div>
					</div>
					<div id="subtabs1-2" aria-labelledby="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="true" style="display: none;">
						<div id="PA_nodatayet" style="visibility:hidden;">
							<?php echo elgg_echo("activityAndPerformanceDashboard:noDataYet"); ?>
						</div>
						<div id="PA_spiderweb"></div>
						<div id="PA_spiderwebhover"></div>
						<div id="PA_slider"></div>
						<div id="PA_sliderscale" style="visibility:hidden;" />
					</div>
				</div>
			</div>
			<div id="tabs-2" aria-labelledby="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="true" style="display: none;">
				<div id="subtabs2" class="ui-tabs ui-widget ui-widget-content ui-corner-all tabs-min">
					<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
						<li onclick="MR_loadAndRender();" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-1" aria-labelledby="ui-id-1" aria-selected="false" aria-expanded="false">
							<a href="#subtabs2-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1">
								<?php echo elgg_echo("activityAndPerformanceDashboard:subtabs2-1"); ?>
							</a>
						</li>
						<li onclick="GR_loadAndRender();" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-2" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false">
							<a href="#subtabs2-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2">
								<?php echo elgg_echo("activityAndPerformanceDashboard:subtabs2-2"); ?>
							</a>
						</li>
					</ul>
					<div id="subtabs2-1" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false">
						<div id="MR_nodatayet" style="visibility:hidden;">
							<?php echo elgg_echo("activityAndPerformanceDashboard:noDataYet"); ?>
						</div>
						<div id="MR_spiderweb"></div>
						<div id="MR_spiderwebhover"></div>
						<div id="MR_slider"></div>
						<div id="MR_sliderscale" style="visibility:hidden;" />
					</div>
					<div id="subtabs2-2" aria-labelledby="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="true" style="display: none;">
						<div id="GR_nodatayet" style="visibility:hidden;">
							<?php echo elgg_echo("activityAndPerformanceDashboard:noDataYet"); ?>
						</div>
						<div id="GR_spiderweb"></div>
						<div id="GR_spiderwebhover"></div>
						<div id="GR_slider"></div>
						<div id="GR_sliderscale" style="visibility:hidden;" />
						<div id="GR_bars">
							<span class="GR_bars_header"><b>
								<?php echo elgg_echo("activityAndPerformanceDashboard:barsTitleGR1"); ?>
							</b></span>
							<div id="GR_bars1"></div>
							<div class="GR_bars_separator"></div>
							<span class="GR_bars_header"><b>
								<?php echo elgg_echo("activityAndPerformanceDashboard:barsTitleGR2"); ?>
							</b></span>
							<div id="GR_bars2"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			/* load and render data */
			var TOC = <?php if ($TOC) echo 'true'; else echo 'false'; ?>;

			var resultArr = [];
			var usernamesArr = [];
			var showUsersArr = [];
			var renderUsersArr = [];

			var dimensionsArr = [];
			var dimensionsSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionCA-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionCA-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionCA-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionCA-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionCA-4"); ?>"];
			dimensionsArr[0] = dimensionsSubArr;
			dimensionsSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionPA-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionPA-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionPA-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionPA-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionPA-4"); ?>"];
			dimensionsArr[1] = dimensionsSubArr;
			dimensionsSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-4"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-5"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-6"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-7"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionMR-8"); ?>"];
			dimensionsArr[2] = dimensionsSubArr;
			dimensionsSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-4"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-5"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-6"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-7"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionGR-8"); ?>"];
			dimensionsArr[3] = dimensionsSubArr;

			var dimensionsHoverArr = [];
			var dimensionsHoverSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverCA-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverCA-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverCA-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverCA-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverCA-4"); ?>"];
			dimensionsHoverArr[0] = dimensionsHoverSubArr;
			dimensionsHoverSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverPA-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverPA-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverPA-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverPA-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverPA-4"); ?>"];
			dimensionsHoverArr[1] = dimensionsHoverSubArr;
			dimensionsHoverSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-4"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-5"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-6"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-7"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverMR-8"); ?>"];
			dimensionsHoverArr[2] = dimensionsHoverSubArr;
			dimensionsHoverSubArr = ["<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-0"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-1"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-2"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-3"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-4"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-5"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-6"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-7"); ?>",
			  "<?php echo elgg_echo("activityAndPerformanceDashboard:dimensionHoverGR-8"); ?>"];
			dimensionsHoverArr[3] = dimensionsHoverSubArr;

			var typeOfDataArr = ['<?php echo $CA_typeOfData; ?>',
			  '<?php echo $PA_typeOfData; ?>',
			  '<?php echo $MR_typeOfData; ?>',
			  '<?php echo $GR_typeOfData; ?>'];
			var numberOfIntervalsArr = [<?php echo $CA_numberOfIntervals; ?>,
			  <?php echo $PA_numberOfIntervals; ?>,
			  <?php echo $MR_numberOfIntervals; ?>,
			  <?php echo $GR_numberOfIntervals; ?>];
			var privacyLevelArr = [<?php echo $CA_privacyLevel; ?>,
			  <?php echo $PA_privacyLevel; ?>,
			  <?php echo $MR_privacyLevel; ?>,
			  <?php echo $GR_privacyLevel; ?>];
			var intervalNumberArr = [];
			for (i = 0; i < numberOfIntervalsArr.length; i++) {
			  intervalNumberArr[i] = numberOfIntervalsArr[i];
			}
			var numberOfMonths = <?php echo $numberOfMonths; ?>;
			var lastYear = <?php echo $lastYear; ?>;
			var lastMonthNr = <?php echo $lastMonthNr; ?>;
			var months = [
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-1"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-2"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-3"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-4"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-5"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-6"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-7"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-8"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-9"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-10"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-11"); ?>',
			  '<?php echo elgg_echo("activityAndPerformanceDashboard:shortMonth-12"); ?>'
			  ];
			var startYear = lastYear;
			var startMonthNr = lastMonthNr;
			for (i = 0; i < (numberOfMonths - 1); i++) {
			  startMonthNr = startMonthNr - 1;
			  if (startMonthNr == 0) {
			    startMonthNr = 12;
			    startYear = startYear - 1;
			  }
			}

			var userIsAdmin = <?php if ($userIsAdmin) echo 'true'; else echo 'false'; ?>;
			var userIsTutor = <?php if ($userIsTutor) echo 'true'; else echo 'false'; ?>;
			var showCABar = (!userIsAdmin && !userIsTutor);
			var showPerformanceData = (userIsAdmin || userIsTutor || TOC);
			var showMe = !(userIsAdmin || userIsTutor);

			var spiderMinScaleValue = [];
			spiderMinScaleValue[0] = 0;
			spiderMinScaleValue[1] = 0;
			spiderMinScaleValue[2] = 0;
			spiderMinScaleValue[3] = 0;
			var spiderNumberOfScaleValues = [];
			spiderNumberOfScaleValues[0] = 5;
			spiderNumberOfScaleValues[1] = 5;
			spiderNumberOfScaleValues[2] = 5;
			spiderNumberOfScaleValues[3] = 5;
			var spiderScaleValuesAsNumbers = [];
			spiderScaleValuesAsNumbers[0] = true;
			spiderScaleValuesAsNumbers[1] = true;
			spiderScaleValuesAsNumbers[2] = true;
			spiderScaleValuesAsNumbers[3] = true;
			var spiderScaleValueMultiplyFactor = [];
			spiderScaleValueMultiplyFactor[0] = 10;
			spiderScaleValueMultiplyFactor[1] = 10;
			spiderScaleValueMultiplyFactor[2] = 10;
			spiderScaleValueMultiplyFactor[3] = 10;

			var spiderWidth = 481;

			var MR_monthly_result;
			var GR_monthly_result;
			function getMRandGRresult() {
			  var groupGuid = <?php echo $groupGuid; ?>;
			  if (groupGuid == 0) {
			    return;
			  }
			  var MR_GR_monthly_result = <?php echo $MR_GR_monthly_result; ?>;
			  //Contains one array with a sub array per user
			  //values per user are:
			  //- datetime (format 7-11-2014 13:39)
			  //- username
			  //- groupguid
			  //- userguid
			  //- period
			  //- 9 answers which represent me about me (MR_GR_monthly_result[5] gives first answer)
			  //- 9 answers which represent me about the group (MR_GR_monthly_result[14] gives first answer)
			  //- 9 values which represent userguids of other rated users (if value=0 user is not rated) (MR_GR_monthly_result[23] gives first userguid)
			  //- 9x9 answers which represent me about another user where column is equal to userguid column (MR_GR_monthly_result[32] gives first answer for userguid MR_GR_monthly_result[23], MR_GR_monthly_result[41] gives second answer for userguid MR_GR_monthly_result[23])

			  var indexDatetime = 0;
			  var indexGroupguid = 2;
			  var indexUserguid = 3;
			  var indexFirstMeAboutMe = 5;
			  var indexFirstMeAboutGroup = 14;
			  var indexFirstOtherUserguid = 23;
			  var maxOtherUserguid = 9;
			  var indexFirstMeAboutOther = 32;

			  //get PA result because it contains user ids, names and if users are shown
			  MR_monthly_result = <?php echo $PA_result; ?>;
			  //overwrite/create empty data for MR
			  for (i = 0; i < dimensionsArr[2].length; i++) {
			    MR_monthly_result["data" + i] = [];
			    for (j = 0; j <= MR_monthly_result["userids"].length; j++) {
			      MR_monthly_result["data" + i]["user" + j] = [];
			      for (k = 0; k < numberOfMonths; k++) {
			        MR_monthly_result["data" + i]["user" + j][k] = 0;
			      }
			    }
			  }
			  //fill MR data
			  //first me about myself
			  var userGuid = MR_monthly_result["userids"][0];
			  for (i = 0; i < MR_GR_monthly_result.length; i++) {
			    if (MR_GR_monthly_result[i][indexGroupguid] == groupGuid && MR_GR_monthly_result[i][indexUserguid] == userGuid) {
			      var temp = MR_GR_monthly_result[i][indexDatetime].split(' ')[0].split('-');
			      var year = parseInt(temp[2]);
			      var monthNr = parseInt(temp[1]);
			      var intervalNr = 12 * (year  - startYear) + monthNr - startMonthNr;
			      if (intervalNr >= 0 && intervalNr < numberOfMonths) {
			        for (j = 0; j < dimensionsArr[2].length; j++) {
			          MR_monthly_result["data" + j]["user1"][intervalNr] = MR_GR_monthly_result[i][indexFirstMeAboutMe + j] / 10;
			        }
			      }
			    }
			  }
			  //then group all users
			  //maak array per dimensie, per user, per interval, met aantal waarden
			  var tempArray = [];
			  for (i = 0; i < dimensionsArr[2].length; i++) {
			    tempArray["data" + i] = [];
			    for (j = 0; j <= MR_monthly_result["userids"].length; j++) {
			      tempArray["data" + i]["user" + j] = [];
			      for (k = 0; k < numberOfMonths; k++) {
			        tempArray["data" + i]["user" + j][k] = [];
			      }
			    }
			  }
			  for (i = 0; i < MR_GR_monthly_result.length; i++) {
			    if (MR_GR_monthly_result[i][indexGroupguid] == groupGuid) {
			      var temp = MR_GR_monthly_result[i][indexDatetime].split(' ')[0].split('-');
			      var year = parseInt(temp[2]);
			      var monthNr = parseInt(temp[1]);
			      var intervalNr = 12 * (year  - startYear) + monthNr - startMonthNr;
			      if (intervalNr >= 0 && intervalNr < numberOfMonths) {
			        for (x = 0; x < maxOtherUserguid; x++) {
			          if (MR_GR_monthly_result[i][indexFirstOtherUserguid + x] > 0) {
			            for (y = 0; y < MR_monthly_result["userids"].length; y++) {
			              if (MR_GR_monthly_result[i][indexFirstOtherUserguid + x] == MR_monthly_result["userids"][y]) {
			                for (j = 0; j < dimensionsArr[2].length; j++) {
			                  var length = tempArray["data" + j]["user" + y][intervalNr].length;
			                  tempArray["data" + j]["user" + y][intervalNr][length] = MR_GR_monthly_result[i][indexFirstMeAboutOther+j*maxOtherUserguid+x] / 10;
			                }
			              }
			            }
			          }
			        }
			      }
			    }
			  }
			  for (i = 0; i < dimensionsArr[2].length; i++) {
			    for (j = 0; j < MR_monthly_result["userids"].length; j++) {
			      for (k = 0; k < numberOfMonths; k++) {
			        var value = 0;
			        var length = tempArray["data" + i]["user" + j][k].length;
			        for (x = 0; x < length; x++) {
			          value = value + tempArray["data" + i]["user" + j][k][x];
			        }
			        if (length > 0) {
			          value = value/length;
			        }
			        if (j == 0) {
			          MR_monthly_result["data" + i]["user0"][k] = value;
			        }
			        else {
			          MR_monthly_result["data" + i]["user" + (j + 1)][k] = value;
			        }
			      }
			    }
			  }
			  //get PA result because it contains user ids, names and if users are shown
			  GR_monthly_result = <?php echo $PA_result; ?>;
			  //overwrite/create empty data for GR
			  for (i = 0; i < dimensionsArr[2].length; i++) {
			    GR_monthly_result["data" + i] = [];
			    for (j = 0; j <= GR_monthly_result["userids"].length; j++) {
			      GR_monthly_result["data" + i]["user" + j] = [];
			      for (k = 0; k < numberOfMonths; k++) {
			        GR_monthly_result["data" + i]["user" + j][k] = 0;
			      }
			    }
			  }
			  //fill GR data
			  for (x = 0; x < GR_monthly_result["userids"].length; x++) {
			    var userGuid = GR_monthly_result["userids"][x];
			    for (i = 0; i < MR_GR_monthly_result.length; i++) {
			      if (MR_GR_monthly_result[i][indexGroupguid] == groupGuid && MR_GR_monthly_result[i][indexUserguid] == userGuid) {
			        var temp = MR_GR_monthly_result[i][indexDatetime].split(' ')[0].split('-');
			        var year = parseInt(temp[2]);
			        var monthNr = parseInt(temp[1]);
			        var intervalNr = 12 * (year  - startYear) + monthNr - startMonthNr;
			        if (intervalNr >= 0 && intervalNr < numberOfMonths) {
			          for (j = 0; j < dimensionsArr[2].length; j++) {
			            var value = MR_GR_monthly_result[i][indexFirstMeAboutGroup + j] / 10;
			            //first group about the group
			            GR_monthly_result["data" + j]["user0"][intervalNr] += value / GR_monthly_result["userids"].length;
			            //then user about the group
			            GR_monthly_result["data" + j]["user" + (x + 1)][intervalNr] = value;
			          }
			        }
			      }
			    }
			  }
			}

			function getData(typeOfData) {
			  var index = getTypeOfDataIndex(typeOfData);
			  if (index == 0 || index == 1 || index == 2 || index == 3) {
			    var data;
			    if (index == 0) {
			      data = <?php echo $CA_result; ?>;
			    }
			    else if (index == 1) {
			      data = <?php echo $PA_result; ?>;
			    }
			    else if (index == 2) {
			      data = MR_monthly_result;
			    }
			    else if (index == 3) {
			      data = GR_monthly_result;
			    }
			    if (data["userids"] == null) {
			      showNoDataYet(typeOfData);
			    }
			    else {
			      var resultSubArr = [];
			      for (i = 0; i < dimensionsArr[index].length; i++) {
			        resultSubArr[i] = data["data" + i];
			      }
			      resultArr[index] = resultSubArr;
			      usernamesArr[index] = data["usernames"];
			      showUsersArr[index] = data["showUsers"];
			      renderUsersArr[index] = [];
			      var start = 1;
			      if (showMe) {
			        start = 2;
			      }
			      for (i = 0; i < usernamesArr[index].length; i++) {
			        if (i < start) {
			          renderUsersArr[index][i] = true;
			        }
			        else {
			          renderUsersArr[index][i] = false;
			        }
			      }
			      draw(typeOfData);
			    }
			  }
			}

			function drawLegendForTypeOfData(typeOfData) {
			  //legend title
			  var legendTitle = '';
			  //legend options
			  var legendOptions = [];
			  var index = getTypeOfDataIndex(typeOfData);
			  if (index >= 0) {
			    if (index < 2) {
			      if (privacyLevelArr[index] == 0) {
			        legendOptions[0] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroup0"); ?>';
			        if (showMe) {
			          legendOptions[1] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionMe"); ?>';
			        }
			      }
			      else {
			        legendOptions[0] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroup"); ?>';
			        if (showMe) {
			          legendOptions[1] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionMe"); ?>';
			        }
			      }
			    }
			    else if (index == 2) {
			      legendOptions[0] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroupMR"); ?>';
			      if (showMe) {
			        legendOptions[1] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionMeMR"); ?>';
			      }
			    }
			    else if (index == 3) {
			      legendOptions[0] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroupGR"); ?>';
			      if (showMe) {
			        legendOptions[1] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionMeGR"); ?>';
			      }
			    }
			  }
			  if (usernamesArr[index] != null) {
			    var counter = 1;
			    var start = 1;
			    if (showMe) {
			      counter = 2;
			      start = 2;
			    }
			    for (i = start; i < usernamesArr[index].length; i++) {
			      if (showUsersArr[index][i] == 1) {
			        if (index < 2) {
			          legendOptions[counter] = usernamesArr[index][i];
			        }
			        else if (index == 2) {
			          legendOptions[counter] = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroupMRprefix"); ?> ' + usernamesArr[index][i];
			        }
			        else if (index == 3) {
			          legendOptions[counter] = usernamesArr[index][i] + ' <?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroupGRpostfix"); ?>';
			        }
			        counter = counter + 1;
			      }
			    }
			  }
			  if (index == 0) {
			    legendTitle = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendTitleCA"); ?>';
			  }
			  else if (index == 1) {
			    legendTitle = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendTitlePA"); ?>';
			  }
			  else if (index == 2) {
			    legendTitle = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendTitleMR"); ?>';
			  }
			  else if (index == 3) {
			    legendTitle = '<?php echo elgg_echo("activityAndPerformanceDashboard:legendTitleGR"); ?>';
			  }
			  if (index > 1 && !showPerformanceData) {
			    legendOptions = [];
			  }
			  drawLegend(legendTitle, legendOptions, typeOfDataArr[index] + "_spiderweb", index);
			}

			function drawSliderScaleElement(parent, elementnr, maxelements, label) {
			  var div = document.createElement("DIV");
			  parent.appendChild(div);
			  div.style.position = 'absolute';
			  div.style.left = '' + elementnr*(spiderWidth/(maxelements-1)) + 'px';
			  div.style.position = '0px';
			  var img = document.createElement("IMG");
			  div.appendChild(img);
			  img.src = "<?php echo $vars['url']; ?>mod/activityAndPerformanceDashboard/views/default/widgets/activityAndPerformanceDashboard/images/uiSliderLine.png";
			  var subdiv = document.createElement("DIV");
			  div.appendChild(subdiv);
			  subdiv.style.position = 'relative';
			  subdiv.style.left = '-9px';
			  subdiv.style.position = '0px';
			  subdiv.style.fontSize = '11px';
			  subdiv.innerHTML = label;
			}

			function drawBarsForTypeOfData(typeOfData) {
			  var index = getTypeOfDataIndex(typeOfData);
			  var legendOptions = [];
			  var resultIsEmpty = false;
			  if (index >= 0) {
			    if (privacyLevelArr[index] == 0) {
			      if (showMe) {
			        legendOptions = ['<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionMe"); ?>','<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroup0"); ?>'];
			      }
			      else {
			        legendOptions = ['<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroup"); ?>'];
			      }
			    }
			    else {
			      legendOptions = ['<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionMe"); ?>','<?php echo elgg_echo("activityAndPerformanceDashboard:legendOptionGroup"); ?>'];
			    }
			    var barsId = typeOfDataArr[index] + "_bars";
			    if (index == 0) {
			      var result = [];
			      var group = 0;
			      var me = 0;
			      //NOTE only first four indicators
			      for (i = 0; i < 4; i++) {
			        group += resultArr[index][i]['user0'][0];
			        me += resultArr[index][i]['user1'][0];
			      }
			      if (showMe) {
			        result[0] = (10 * me) / 4;
			        result[1] = (10 * group) / 4;
			      }
			      else {
			        result[0] = (10 * group) / 4;
			      }
			      drawBars(barsId, 1, legendOptions, result);
			    }
			    else if (index == 3) {
			      var groupGuid = <?php echo $groupGuid; ?>;
			      var GR_weekly_result = <?php echo $GR_weekly_result; ?>;
			      //Contains one array with a sub array per user
			      //values per user are:
			      //- datetime (format 7-11-2014 13:39)
			      //- username
			      //- groupguid
			      //- userguid
			      //- period
			      //- 2 answers to questions (GR_weekly_result[5] gives first answer)

			      var indexDatetime = 0;
			      var indexGroupguid = 2;
			      var indexUserguid = 3;
			      var indexFirstAnswer = 5;

			      var group = [0,0];
			      var me = [0,0];
			      var mecounter = 0;
			      var groupcounter = 0;
			      for (i = 0; i < GR_weekly_result.length; i++) {
			        if (GR_weekly_result[i][indexGroupguid] == groupGuid) {
			          var temp = GR_weekly_result[i][indexDatetime].split(' ')[0].split('-');
			          var year = parseInt(temp[2]);
			          var monthNr = parseInt(temp[1]);
			          var intervalNr = 12 * (year  - startYear) + monthNr - startMonthNr;
			          if (intervalNr >= 0 && intervalNr < numberOfMonths) {
			            if (GR_weekly_result[i][indexUserguid] == <?php echo get_loggedin_user()->guid; ?>) {
			              me[0] += GR_weekly_result[i][indexFirstAnswer];
			              me[1] += GR_weekly_result[i][indexFirstAnswer + 1];
			              mecounter ++;
			            }
			            group[0] += GR_weekly_result[i][indexFirstAnswer];
			            group[1] += GR_weekly_result[i][indexFirstAnswer + 1];
			            groupcounter ++;
			          }
			        }
			      }
			      if (groupcounter > 0) {
			        group[0] = group[0] / groupcounter;
			        group[1] = group[1] / groupcounter;
			      }
			      if (mecounter > 0) {
			        me[0] = me[0] / mecounter;
			        me[1] = me[1] / mecounter;
			      }

			      var result = [];
			      if (showMe) {
			        result[0] = me[0];
			        result[1] = group[0];
			      }
			      else {
			        result[0] = group[0];
			      }
			      drawBars(barsId, 1, legendOptions, result);
			      if (showMe) {
			        result[0] = me[1];
			        result[1] = group[1];
			      }
			      else {
			        result[0] = group[1];
			      }
			      drawBars(barsId, 2, legendOptions, result);
			      if (group[0] == 0 || group[1] == 0 || me[0] == 0 || me[1] == 0) {
			        resultIsEmpty = true;
			      }
			    }
			    var object = document.getElementById(barsId);
			    if (object != null && !resultIsEmpty) {
			      object.style.visibility = 'visible';
			    }
			  }
			}
		</script>
	</body>
</html>
