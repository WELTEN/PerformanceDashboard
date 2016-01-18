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
 * in the program Activity and performance dashboard written by
 * Aad Slootmaker
 *
 */

	/**
	 * activityAndPerformanceDashboard language file.
	 */

	$english = array(
		'activityAndPerformanceDashboard:privacySetting' => 'Privacy setting, show data for',
		'activityAndPerformanceDashboard:privacySetting0' => 'Me and group without me',
		'activityAndPerformanceDashboard:privacySetting1' => 'Me and anonymized other group members',
		'activityAndPerformanceDashboard:privacySetting2' => 'Me and other group members',
		'activityAndPerformanceDashboard:inputGroups' => 'Enter group guids to monitor, comma separated',
		'activityAndPerformanceDashboard:inputTutors' => 'Enter user guids of tutors, comma separated',
		'activityAndPerformanceDashboard:inputExcludedUsers' => 'Enter user guids to exclude from monitoring, comma separated',
		'activityAndPerformanceDashboard:defaultShareWithOthers' => 'Default value for sharing data with other group members',
		'activityAndPerformanceDashboard:defaultEndDate' => 'Set default end date (yyyy-mm-dd), if empty it is the current date',
		'activityAndPerformanceDashboard:numberOfMonths' => 'Number of months',
		'activityAndPerformanceDashboard:memberPlusGroupRating' => 'Enter array with monthly group member ratings',
		'activityAndPerformanceDashboard:generalGroupRating' => 'Enter array with weekly group member ratings',

		'activityAndPerformanceDashboard:TOC' => 'TOC',
		'activityAndPerformanceDashboard:shareWithOthers' => 'Share activity and performance with other group members',
		'activityAndPerformanceDashboard:chooseGroup' => 'Choose group',
		'activityAndPerformanceDashboard:setEndDate' => 'Set end date (yyyy-mm-dd), if empty it will be the default date set by the admin',
		'activityAndPerformanceDashboard:TOCSettings' => 'TOC settings',
		'activityAndPerformanceDashboard:ShareWithOthersSettings' => 'Share with other user settings',

		'activityAndPerformanceDashboard:group' => 'Group',

		'activityAndPerformanceDashboard:tabs-1' => 'Platform activity',
		'activityAndPerformanceDashboard:tabs-2' => 'Performance',
		'activityAndPerformanceDashboard:subtabs1-1' => 'Cumulative activity',
		'activityAndPerformanceDashboard:subtabs1-2' => 'Periodic activity',
		'activityAndPerformanceDashboard:subtabs2-1' => 'Members',
		'activityAndPerformanceDashboard:subtabs2-2' => 'Group',

		'activityAndPerformanceDashboard:noDataYet' => 'No data available yet.',

		'activityAndPerformanceDashboard:dimensionCA-0' => 'Initiative',
		'activityAndPerformanceDashboard:dimensionCA-1' => 'Responsiveness',
		'activityAndPerformanceDashboard:dimensionCA-2' => 'Connectedness',
		'activityAndPerformanceDashboard:dimensionCA-3' => 'Presence',
		'activityAndPerformanceDashboard:dimensionCA-4' => 'Productivity',
		'activityAndPerformanceDashboard:dimensionPA-0' => 'Initiative',
		'activityAndPerformanceDashboard:dimensionPA-1' => 'Responsiveness',
		'activityAndPerformanceDashboard:dimensionPA-2' => 'Connectedness',
		'activityAndPerformanceDashboard:dimensionPA-3' => 'Presence',
		'activityAndPerformanceDashboard:dimensionPA-4' => 'Productivity',
		'activityAndPerformanceDashboard:dimensionMR-0' => 'Productivity',
		'activityAndPerformanceDashboard:dimensionMR-1' => 'Initiative & influence',
		'activityAndPerformanceDashboard:dimensionMR-2' => 'Quality',
		'activityAndPerformanceDashboard:dimensionMR-3' => 'Understanding',
		'activityAndPerformanceDashboard:dimensionMR-4' => 'Adjustment',
		'activityAndPerformanceDashboard:dimensionMR-5' => 'Friendliness',
		'activityAndPerformanceDashboard:dimensionMR-6' => 'Reliability & trustworthiness',
		'activityAndPerformanceDashboard:dimensionMR-7' => 'Helpfulness & supportiveness',
		'activityAndPerformanceDashboard:dimensionMR-8' => 'Structure',
		'activityAndPerformanceDashboard:dimensionGR-0' => 'Monitoring & evaluation',
		'activityAndPerformanceDashboard:dimensionGR-1' => 'Realignment & adaption',
		'activityAndPerformanceDashboard:dimensionGR-2' => 'Goals & objectives',
		'activityAndPerformanceDashboard:dimensionGR-3' => 'Shared understanding',
		'activityAndPerformanceDashboard:dimensionGR-4' => 'Purposeful communication',
		'activityAndPerformanceDashboard:dimensionGR-5' => 'Positive & negative feelings',
		'activityAndPerformanceDashboard:dimensionGR-6' => 'Conflict resolution',
		'activityAndPerformanceDashboard:dimensionGR-7' => 'Cohesiveness & belonging',
		'activityAndPerformanceDashboard:dimensionGR-8' => 'Interpersonal relationships',
		'activityAndPerformanceDashboard:dimensionHoverCA-0' => 'Number of posts (discussion, blog, files, pages)',
		'activityAndPerformanceDashboard:dimensionHoverCA-1' => 'Number of comments to posts (discussion, blog, files, pages)',
		'activityAndPerformanceDashboard:dimensionHoverCA-2' => 'Number of contacts created',
		'activityAndPerformanceDashboard:dimensionHoverCA-3' => 'Online presence measured through page views',
		'activityAndPerformanceDashboard:dimensionHoverCA-4' => 'Sum of (Initiative plus Responsiveness) divided by Presence',
		'activityAndPerformanceDashboard:dimensionHoverPA-0' => 'Number of posts (discussion, blog, files, pages)',
		'activityAndPerformanceDashboard:dimensionHoverPA-1' => 'Number of comments to posts (discussion, blog, files, pages)',
		'activityAndPerformanceDashboard:dimensionHoverPA-2' => 'Number of contacts created',
		'activityAndPerformanceDashboard:dimensionHoverPA-3' => 'Online presence measured through page views',
		'activityAndPerformanceDashboard:dimensionHoverPA-4' => 'Sum of (Initiative plus Responsiveness) divided by Presence',
		'activityAndPerformanceDashboard:dimensionHoverMR-0' => 'A productive group member is one who is actively participating in group discussions, giving answers to questions, commenting to ideas and opinions, and producing pieces of text',
		'activityAndPerformanceDashboard:dimensionHoverMR-1' => 'Exerting influence means that the strategies and directions as suggested by a group member so to achieve the group goals are followed by other group members',
		'activityAndPerformanceDashboard:dimensionHoverMR-2' => 'Quality refers to the usefulness of the discussion contributions, the answers and the comments, and the pieces of text produced by a group member',
		'activityAndPerformanceDashboard:dimensionHoverMR-3' => 'Understanding refers to the recognition and meaning of the academic, social and socio-emotional needs of the other group members.',
		'activityAndPerformanceDashboard:dimensionHoverMR-4' => 'Adjustment refers to keeping the group coherent by adjusting one\'s actions to that of the other group members.',
		'activityAndPerformanceDashboard:dimensionHoverMR-5' => 'Friendliness refers to how a group member is behaving socially in the group. A positive social behavior will a to a healthy group atmosphere.',
		'activityAndPerformanceDashboard:dimensionHoverMR-6' => 'When a group member is reliable and trustworthy it means that other group members can rely on him about his or her actions and that she or he will not misuse their words, actions, and decisions.',
		'activityAndPerformanceDashboard:dimensionHoverMR-7' => 'Helpfulness and supportiveness refer to cooperative behavior and willingness to contribute to group outcomes. They also refer to the degree of responsiveness to the needs of other group members.',
		'activityAndPerformanceDashboard:dimensionHoverMR-8' => 'Structure refers to compliance with the group structures which includes norms, values, rules, and roles.',
		'activityAndPerformanceDashboard:dimensionHoverGR-0' => 'Monitoring and evaluating the group\'s progress should happen on a regularly bases. Through monitoring, problems that may hamper the progress of the group can be detected at an early stage. Problems can be academic, social or even socio-emotional. Also ways to better the group\'s progress can be discussed.',
		'activityAndPerformanceDashboard:dimensionHoverGR-1' => 'Change of settings or the detection of problems may give rise to revise the group\'s strategies, directions and behavior.',
		'activityAndPerformanceDashboard:dimensionHoverGR-2' => 'Setting goals and objectives means that every group member participates in the decision/ revision process that sets/redefines which goals and objectives the group will strive for.',
		'activityAndPerformanceDashboard:dimensionHoverGR-3' => 'Shared understanding refers to the set of mutual beliefs, knowledge, and assumptions that exist amongst the group members.',
		'activityAndPerformanceDashboard:dimensionHoverGR-4' => 'Purposeful communication exist if group members are describing, explaining, arguing, critiquing or evaluating in a group discussion or in response to a comment, opinion, or idea.',
		'activityAndPerformanceDashboard:dimensionHoverGR-5' => 'Positive feelings are associated with increased trust, involvement, enthusiastic behavior and happiness. In contrast negative feelings are associated with reduced trust, withdrawal, defensive behavior and unhappiness.',
		'activityAndPerformanceDashboard:dimensionHoverGR-6' => 'Without conflict resolution a group may experience an unhealthy group atmosphere and possibly if conflicts persist may give rise to a breakdown of the group.',
		'activityAndPerformanceDashboard:dimensionHoverGR-7' => 'A cohesive group is a group that sticks together and remains united. The group is/becomes cohesive if group members identify with the group and a desire to be part of the group',
		'activityAndPerformanceDashboard:dimensionHoverGR-8' => 'Good interpersonal relationships exist if group members get along with each other, respect each other and are willing to help the other',

		'activityAndPerformanceDashboard:legendTitleCA' => 'Cumulative activity',
		'activityAndPerformanceDashboard:legendTitlePA' => 'Periodic activity',
		'activityAndPerformanceDashboard:legendTitleMR' => 'Members performance',
		'activityAndPerformanceDashboard:legendTitleGR' => 'Group performance',
		'activityAndPerformanceDashboard:legendOptionGroup0' => 'Group without me',
		'activityAndPerformanceDashboard:legendOptionGroup' => 'Group',
		'activityAndPerformanceDashboard:legendOptionMe' => 'Me',
		'activityAndPerformanceDashboard:legendOptionGroupMR' => 'Group about me',
		'activityAndPerformanceDashboard:legendOptionMeMR' => 'Me about myself',
		'activityAndPerformanceDashboard:legendOptionGroupMRprefix' => 'Group about',
		'activityAndPerformanceDashboard:legendOptionGroupGR' => 'Group about the group',
		'activityAndPerformanceDashboard:legendOptionMeGR' => 'Me about the group',
		'activityAndPerformanceDashboard:legendOptionGroupGRpostfix' => 'about the group',

		'activityAndPerformanceDashboard:barsTitleCA' => 'My activity',
		'activityAndPerformanceDashboard:barsTitleGR1' => 'We are an effective team',
		'activityAndPerformanceDashboard:barsTitleGR2' => 'We have a healthy group atmosphere',

		'activityAndPerformanceDashboard:getDataGroup0' => 'Group without me',
		'activityAndPerformanceDashboard:getDataGroup' => 'Group',
		'activityAndPerformanceDashboard:getDataMe' => 'Me',
		'activityAndPerformanceDashboard:getDataUser' => 'User',

		'activityAndPerformanceDashboard:shortMonth-1' => 'jan',
		'activityAndPerformanceDashboard:shortMonth-2' => 'feb',
		'activityAndPerformanceDashboard:shortMonth-3' => 'mar',
		'activityAndPerformanceDashboard:shortMonth-4' => 'apr',
		'activityAndPerformanceDashboard:shortMonth-5' => 'may',
		'activityAndPerformanceDashboard:shortMonth-6' => 'jun',
		'activityAndPerformanceDashboard:shortMonth-7' => 'jul',
		'activityAndPerformanceDashboard:shortMonth-8' => 'aug',
		'activityAndPerformanceDashboard:shortMonth-9' => 'sep',
		'activityAndPerformanceDashboard:shortMonth-10' => 'oct',
		'activityAndPerformanceDashboard:shortMonth-11' => 'nov',
		'activityAndPerformanceDashboard:shortMonth-12' => 'dec'
	);

	add_translation("en", $english);
?>
