<?php
	/**
	* activityAndPerformanceDashboard settings configuration.
	*/

	$privacySetting = $vars['entity']->privacySetting;
	if (!$privacySetting) {
		$privacySetting = 0;
	}

	$monitorGroupGuids = $vars['entity']->monitorGroupGuids;
	if (!$monitorGroupGuids) {
		$monitorGroupGuids = '';
	}

	$tutorUserGuids = $vars['entity']->tutorUserGuids;
	if (!$tutorUserGuids) {
		$tutorUserGuids = '';
	}

	$excludedUserGuids = $vars['entity']->excludedUserGuids;
	if (!$excludedUserGuids) {
		$excludedUserGuids = '';
	}

	$defaultShareWithOthers = $vars['entity']->defaultShareWithOthers;
	if (!$defaultShareWithOthers) {
		$defaultShareWithOthers = 0;
	}

	$defaultEndDate = $vars['entity']->defaultEndDate;
	if (!defaultEndDate) {
		$defaultEndDate = '';
	}

	$numberOfMonths = $vars['entity']->numberOfMonths;
	if (!$numberOfMonths) {
		$numberOfMonths = 12;
	}

	$memberPlusGroupRating = $vars['entity']->memberPlusGroupRating;
	if (!$memberPlusGroupRating) {
		$memberPlusGroupRating = '';
	}

	$generalGroupRating = $vars['entity']->generalGroupRating;
	if (!$generalGroupRating) {
		$generalGroupRating = '';
	}

?>
<p>
	<table>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:privacySetting"); ?>:</td>
		</tr>
		<tr>
			<td>
				<input type="radio" id="privacySetting0" name="params[privacySetting]" value="0" <?php if($privacySetting == "0"){ echo "CHECKED"; }?> /> <?php echo elgg_echo("activityAndPerformanceDashboard:privacySetting0"); ?><br/>
				<input type="radio" id="privacySetting1" name="params[privacySetting]" value="1" <?php if($privacySetting == "1"){ echo "CHECKED"; }?> /> <?php echo elgg_echo("activityAndPerformanceDashboard:privacySetting1"); ?><br/>
				<input type="radio" id="privacySetting2" name="params[privacySetting]" value="2" <?php if($privacySetting == "2"){ echo "CHECKED"; }?> /> <?php echo elgg_echo("activityAndPerformanceDashboard:privacySetting2"); ?>
			</td>
		<tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:inputGroups"); ?>:</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="monitorGroupGuids" name="params[monitorGroupGuids]" value="<?php echo $monitorGroupGuids; ?>" style="width:600px;" />
			</td>
		</tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:inputTutors"); ?>:</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="tutorUserGuids" name="params[tutorUserGuids]" value="<?php echo $tutorUserGuids; ?>" style="width:600px;" />
			</td>
		</tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:inputExcludedUsers"); ?>:</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="excludedUserGuids" name="params[excludedUserGuids]" value="<?php echo $excludedUserGuids; ?>" style="width:600px;" />
			</td>
		</tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:defaultShareWithOthers"); ?>:</td>
		</tr>
		<tr>
			<td>
				<?php
					$options_values = array();
					$options_values['0'] = elgg_echo('option:no');
					$options_values['1'] = elgg_echo('option:yes');
					echo elgg_view('input/pulldown', array(
						'internalname' => 'params[defaultShareWithOthers]',
						'options_values' => $options_values,
						'value' => $defaultShareWithOthers
					));
				?>
			</td>
		<tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:defaultEndDate"); ?>:</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="defaultEndDate" name="params[defaultEndDate]" value="<?php echo $defaultEndDate; ?>" style="width:600px;" />
			</td>
		<tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:numberOfMonths"); ?>:</td>
		</tr>
		<tr>
			<td>
				<?php
					$options_values = array();
					for ($i=1; $i<=12; $i++) {
						$options_values[''+$i] = $i;
					}
					echo elgg_view('input/pulldown', array(
						'internalname' => 'params[numberOfMonths]',
						'options_values' => $options_values,
						'value' => $numberOfMonths
					));
				?>
			</td>
		<tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:memberPlusGroupRating"); ?>:</td>
		</tr>
		<tr>
			<td>
				<?php echo elgg_view('input/longtext', array('internalname' => 'params[memberPlusGroupRating]', 'value' => $memberPlusGroupRating)); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo elgg_echo("activityAndPerformanceDashboard:generalGroupRating"); ?>:</td>
		</tr>
		<tr>
			<td>
				<?php echo elgg_view('input/longtext', array('internalname' => 'params[generalGroupRating]', 'value' => $generalGroupRating)); ?>
			</td>
		</tr>
	</table>
</p>
