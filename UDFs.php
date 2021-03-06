<?php
	
	/**
	 * Modify pay plan values in an array, so they are
	 * in the specified format.
	 * The parameter is passed by reference, so no return
	 * value is necessary.
	 * 
	 * @param payPlan_array  Array containing pay plan names
	 *		as they appear in the table from which they
	 *		were queried
	 * @param format  The format to which the pay plans will
	 *		be converted
	 */
	function convertPayPlans(&$payPlan_array, $format) {
		foreach ($payPlan_array as $i => $payPlan) {
			$payPlan_array[$i] = convertPayPlan($payPlan, $format);
		}
	}


	/**
	 *	Convert pay plan to a different format
	 *
	 * @param payPlan  String representing a pay plan
	 * @param format  The format to which the pay plan
	 *		will be converted
	 * 
	 * @return convertedPayPlan  A String representing the
	 *		converted pay plan
	 */
	function convertPayPlan($payPlan, $format) {
		$convertedPayPlan = ''; // Return value
		
		if ($format == 'pay_levels') {
			switch ($payPlan) {
				case 'usps':
					$convertedPayPlan = 'USPS';
					break;
				case 'ap':
					$convertedPayPlan = 'A&P';
					break;
				case 'exec':
					$convertedPayPlan = 'EXC';
					break;
				case 'fac':
					$convertedPayPlan = 'Faculty';
					break;
			}
		}
		else if ($format == 'long') {
			switch ($payPlan) {
				case 'usps':
					$convertedPayPlan = 'USPS';
					break;
				case 'ap':
					$convertedPayPlan = 'A&amp;P';
					break;
				case 'exec':
					$convertedPayPlan = 'Executive';
					break;
				case 'fac':
					$convertedPayPlan = 'Faculty';
					break;
			}
		}

		return $convertedPayPlan;
	}


	/**
	 * Return an array containing the values from one specified column
	 * of a query result object.
	 *
	 * @param qryResult  Result object of a query
	 * @param colName    Name of the column whose values you would like
	 * @return col_array  Array containing the values from the colName
	 *		column of the qryResult query object
	 */
	function getColArrayFromQuery($qryResult, $colName) {
		$col_array = array();

		while ($row = $qryResult->fetch_assoc()) {
			array_push($col_array, $row[$colName]);
		}
		$qryResult->data_seek(0); // Move result set iterator back to start
		
		return $col_array;
	}


	/**
	 * Return an array containing key-value pairs from two specified
	 * columns of a query result object.
	 * 
	 * @param qryResult  Result object of a query
	 * @param keyColName Name of the column to use as the key
	 * @param valColName Name of the column to use as the val
	 * @return keyVal_array Array containing the key-value pairs from the
	 *		keyColName column and the valColName column of the
	 *		qryresult query object
	 */
	function getKeyValArrayFromQuery($qryResult, $keyColName, $valColName) {
		$keyVal_array = array();

		while ($row = $qryResult->fetch_assoc()) {
			$key = $row[$keyColName];
			$val = $row[$valColName];
			$keyVal_array[$key] = $val;
		}
		$qryResult->data_seek(0); // Move result set iterator back to start

		return $keyVal_array;
	}


	/**
	 * Return a 2-dim array containing key-value pairs from two specified
	 * columns of a query result object, where the value is an array
	 * containing two values
	 * 
	 * @param qryResult  Result object of a query
	 * @param keyColName Name of the column to use as the key
	 * @param valColName Name of the column to use as the val
	 * @return keyVal_array Array containing the key-value pairs from the
	 *		keyColName column and the valColName column of the
	 *		qryresult query object
	 */
	function getKeyVal2DArrayFromQuery($qryResult, $keyColName, $val1ColName, $val2ColName) {
		$keyVal_2Darray = array();

		while ($row = $qryResult->fetch_assoc()) {
			$key = $row[$keyColName];
			$val = array($row[$val1ColName], $row[$val2ColName]);
			$keyVal_2Darray[$key] = $val;
		}
		$qryResult->data_seek(0); // Move result set iterator back to start

		return $keyVal_2Darray;
	}


	/**
	 * Create a lookup table for the (pay level x job family) values
	 *
	 * @param payLevels             Array containing pay levels
	 * @param numJobFamilies        Number of job families
	 * @param jobCodeCountQryResult Query result from job code count query
	 *
	 * @return lookup_table 2-dim array used to lookup matrix values
	 */
	function createLookupTable($payLevels, $numJobFamilies, $jobCodeCountQryResult) {

		$lookup_table = array();

		/* Create lookup table filled with zeros */
		foreach ($payLevels as $payLevel) {
			$lookup_table[$payLevel] = array();

			// Initialize job counts for each (payLevel x jobFamily)
			for ($i=0; $i < $numJobFamilies; $i++) {
				$lookup_table[$payLevel][$i] = 0; // initialize
			}
		}

		/* Populate lookup table */
		while ($row = $jobCodeCountQryResult->fetch_assoc()) {
			$row_i = $row['PayLevel'];
			$col_i = $row['JobFamilyID'] - 1;
			$lookup_table[$row_i][$col_i] = $row['JobCodeCount'];
		}

		return $lookup_table;
	}


	/**
	 * Create a table representing the Job Family/Pay Level matrix
	 *	
	 * @param jobFamilies  2-dim array containing job families (ID => array(name, descr))
	 * @param payLevels	   Array containing pay levels
	 * @param payLevelDescrs  Array containing pay level descriptions
	 * @param lookup_table 2-dim array used to lookup matrix values
	 */
	function createMatrix($jobFamilies, $payLevels, $payLevelDescrs, $lookup_table) {
?>
		<table class="table matrix">
			<thead>
				<tr>
					<th>Pay Level</th>
				<?php
					$popover_i = 0;
					foreach ($jobFamilies as $i => $jobFamily) {
				?>
						<th class="popover-parent">
							<a
								href="#"
								data-toggle="popover"
								tabindex="<?=$popover_i?>"
								data-trigger="hover"
								title="<?=$jobFamily[0]?>"
								data-content="
									<?php
									if (strlen($jobFamily[1]))
										echo $jobFamily[1];
									else
										echo 'No description';
									?>"
								data-placement="bottom">
								<?=$jobFamily[0]?>
							</a>
						</th>
				<?php
						$popover_i++;
					}
				?>
				</tr>
			</thead>

			<tbody>
		<?php
			$popover_i = 0;
			foreach ($payLevels as $payLevel) {
		?>
				<tr>
					<td class="payLevel popover-parent">
						<a 
							href="#"
							data-toggle="popover"
							tabindex="<?=$popover_i?>"
							data-trigger="hover"
							title="Pay Level <?=$payLevel?>"
							data-content="<?=$payLevelDescrs[$payLevel]?>">
							<?=$payLevel?>
						</a>
					</td>
		<?php
				$popover_i++;

				foreach ($jobFamilies as $i => $jobFamily) {
					$jobCodeCount = $lookup_table[$payLevel][$i-1];

					if ($jobCodeCount > 0) {
						echo '<td class="cell cell-clickable">';
					}
					else {
						echo '<td class="cell cell-notClickable">';
					}

						echo $lookup_table[$payLevel][$i-1];

						/* The following spans hold information about the cell, to be used when the cell is clicked */
						echo '<span class="cell-payLevel" style="display:none;">' . $payLevel . '</span>';
						echo '<span class="cell-jobFamily" style="display:none;">' . $i . '</span>';
					echo '</td>';
				}
				echo '</tr>';
			}
		?>
			</tbody>
		</table>

		<script>
			/* Click event handler for cells in matrix */
			var eventHandler_cellClick = function() {
				var payLevel = $(this).find('.cell-payLevel').text();
				var jobFamily = $(this).find('.cell-jobFamily').text();

				window.location.href = "?page=jobs_list&pl=" + payLevel + "&jf=" + jobFamily;
			};

			/* Assign event handler to cell class */
			$('.cell-clickable').click(eventHandler_cellClick);

			/* Activate all tooltips */
			$('[data-toggle = "tooltip"').tooltip();
		</script>
<?php
	}


	/**
	 * Get the string Yes/No represenation of a boolean value
	 *
	 * @param val  Boolean value (or integers 0 or 1)
	 * @return  String "Yes" or "No"
	 */
	function convertYesNo($val) {
		if ($val == 0)
			return "No";
		else if ($val == 1)
			return "Yes";
		else
			return "";
	}


	/**
	 *
	 * Convert FLSA value into a different format
	 *
	 * @param flsa  A string or int representing the FLSA status
	 * @param format  Format to which the user would like to
	 *		convert the FLSA status
	 * @return The converted form of the FLSA value
	 */
	function convertFLSA($flsa, $format) {

		$convertedFLSA = ''; // Return value

		if ($format == 'numeric') {
			switch ($flsa) {
				case 'N':
				case 'NE':
					$convertedFLSA = 0;
					break;
				case 'X':
				case 'E':
					$convertedFLSA = 1;
					break;
				case '1X N':
				case 'both':
					$convertedFLSA = 2;
					break;
			}
		}
		else if ($format == 'symbolic') {
			switch ($flsa) {
				case 0:
					$convertedFLSA = 'N';
					break;
				case 1:
					$convertedFLSA = 'X';
					break;
				case 2:
					$convertedFLSA = 'both';
					break;
			}
		}
		else if ($format == 'string') {
			switch ($flsa) {
				case 0:
					$convertedFLSA = 'Non-Exempt';
					break;
				case 1:
					$convertedFLSA = 'Exempt';
					break;
				case 2:
					$convertedFLSA = 'Both';
					break;
			}
		}
		return $convertedFLSA;
	}
?>
