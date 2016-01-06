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
	 * @param jobFamilies  Array containing job families
	 * @param payLevels	   Array containing pay levels
	 * @param lookup_table 2-dim array used to lookup matrix values
	 */
	function createMatrix($jobFamilies, $payLevels, $lookup_table) {
?>
		<table class="table matrix">
			<thead>
				<tr>
					<th>Pay Level</th>
				<?php
					foreach ($jobFamilies as $i => $jobFamily) {
						echo '<th>';
							echo '<a href="">' . $jobFamily . '</a>';
						echo '</th>';
					}
				?>
				</tr>
			</thead>

			<tbody>
		<?php
			foreach ($payLevels as $payLevel) {
				echo '<tr>';
					echo '<td class="payLevel">' . $payLevel . '</td>';
				foreach ($jobFamilies as $i => $jobFamily) {
					echo '<td class="cell">';
						echo $lookup_table[$payLevel][$i-1];
					echo '</td>';
				}
				echo '</tr>';
			}
		?>
			</tbody>
		</table>
<?php } ?>
