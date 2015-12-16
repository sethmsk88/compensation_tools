<link href="./css/matrix.css" rel="stylesheet">
<script src="./scripts/matrix.js"></script>

<?php
	/**
	 * Creates filters for the matrix.
	 * Must be called in a form.
	 * 
	 * @param filterName Name of the category by which you are filtering
	 * @param filterID   ID prefix to be used for each checkbox
	 * @param options    Array of strings representing filter options
	 */
	function createCheckboxFilter($filterName, $filterID, $options_array) {
?>
		<ul class="filter-group list-unstyled">
			<li>
				<?php echo $filterName; ?>
				<span class="expand-collapse glyphicon glyphicon-triangle-bottom"></span>
				<ul>
					<li>
						<input type="checkbox" id="">
						All
						<span class="expand-collapse glyphicon glyphicon-triangle-bottom"></span>
						<ul>
					<?php
						foreach ($options_array as $option) {
					?>
							<li>
								<div class="filter-option">
									<input type="checkbox" id="">
									<span class="option-label">
										<?php echo $option; ?>
									</span>
								</div>
							</li>
					<?php
						}
					?>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
<?php
	}

	/**
	 * Return an array containing the values from one specified column
	 * of a query result object.
	 *
	 * @param qryResult Result object of a query
	 * @param colName   Name of the column whose values you would like
	 * @return col_array Array containing the values from the colName
	 *					 column of the qryResult query object
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
	 *						keyColName column and the valColName column
	 *						of the qryresult query object
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


	/* Connect to database */
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if ($conn->connect_errno){
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	}

	/*
		Get All Pay Plans
	*/
	$sql_sel_all_payPlans = "
		SELECT DISTINCT PayPlan
		FROM class_specs
	";
	$res_sel_all_payPlans = $conn->query($sql_sel_all_payPlans);

	/*
		Get All Pay Levels
	*/
	$sql_sel_all_payLevels = "
		SELECT DISTINCT PayLevel
		FROM pay_levels
		WHERE PayLevel IS NOT NULL
	";
	$res_sel_all_payLevels = $conn->query($sql_sel_all_payLevels);

	/*
		Get All Job Families
	*/
	$sql_sel_all_jobFamilies = "
		SELECT *
		FROM job_families
	";
	$res_sel_all_jobFamilies = $conn->query($sql_sel_all_jobFamilies);

	/*
		Get Filtered Job Families
	*/
	$sql_sel_filt_jobFamilies = "
		SELECT *
		FROM job_families
		WHERE ID > 0 AND	/* TESTING */
			ID < 6			/* TESTING */
	";
	$res_sel_filt_jobFamilies = $conn->query($sql_sel_filt_jobFamilies);

	/*
		Get the number of distinct job codes for each (PayLevel x JobFamily) pair
	*/
	$sql_sel_jobCodeCount = "
		SELECT sub.PayLevel, sub.JobFamilyID, COUNT(sub.jobCode) AS JobCodeCount
		FROM (
			SELECT DISTINCT a.jobCode, p.PayLevel, j.ID AS JobFamilyID
			FROM all_active_fac_staff AS a
			LEFT JOIN pay_levels AS p
			ON LPAD(a.JobCode, 4, '0') = LPAD(p.JobCode, 4, '0')
			JOIN job_families AS j
			ON j.JobFamily_short = p.JobFamily
			WHERE p.PayLevel IS NOT NULL AND
				p.PayLevel > 10 AND			/* TESTING */
				p.PayLevel < 15				/* TESTING */
			ORDER BY p.PayLevel, p.JobFamily) AS sub
		GROUP BY sub.JobFamilyID, sub.PayLevel
		ORDER BY sub.PayLevel, sub.JobFamilyID
	";
	if (!($res_sel_jobCodeCount = $conn->query($sql_sel_jobCodeCount))){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

	/*
		Create arrays from query results
	*/
	$payPlan_array = getColArrayFromQuery($res_sel_all_payPlans, "PayPlan");
	$payLevel_array = getColArrayFromQuery($res_sel_all_payLevels, "PayLevel");
	$jobFamily_array = getKeyValArrayFromQuery($res_sel_all_jobFamilies, "ID", "JobFamily_long");
	$filtered_jobFamily_array = getKeyValArrayFromQuery($res_sel_filt_jobFamilies, "ID", "JobFamily_long");

	/*
		TODO: This should be populated with filter selections
	*/
	$filtered_payLevel_array = array(11,12,13,14);

	/*
		Create lookup table to populate matrix table
	*/
	$lookup_table = array();
	foreach ($payLevel_array as $payLevel) {
		$lookup_table[$payLevel] = array();

		// Initialize job counts for each (payLevel x jobFamily)
		for ($i=0; $i < count($jobFamily_array); $i++) {
			$lookup_table[$payLevel][$i] = 0; // initialize
		}
	}

	/* Populate lookup table */
	while ($row = $res_sel_jobCodeCount->fetch_assoc()) {
		$row_i = $row['PayLevel'];
		$col_i = $row['JobFamilyID'] - 1;
		$lookup_table[$row_i][$col_i] = $row['JobCodeCount'];
	}
	$res_sel_jobCodeCount->data_seek(0); // Move result set iterator back to start
	dumpQuery($res_sel_jobCodeCount);
?>


<div class="container-fluid">
	<div class="row-fluid">

		<!-- Sidebar content -->
		<div class="sidebar col-md-2">
			<form
				name="filters-form"
				role="form"
				action=""
				>
			<?php
				createCheckboxFilter("Pay Plan", "payPlan", $payPlan_array);
				createCheckboxFilter("Pay Level", "payLevel", $payLevel_array);
				createCheckboxFilter("Job Family", "jobFamily", $jobFamily_array);
			?>


			</form>
		</div>

		<!-- Body Content -->
		<div class="col-md-10">
			<table class="table matrix">
				<thead>
					<tr>
						<th>Pay Level</th>
					<?php
						foreach ($filtered_jobFamily_array as $id => $jobFamily) {
							echo '<th>';
								echo '<a href="">' . $jobFamily . '</a>';
							echo '</th>';
						}
					?>
					</tr>
				</thead>

				<tbody>
			<?php
				foreach ($filtered_payLevel_array as $payLevel) {
					echo '<tr>';
						echo '<td class="payLevel">' . $payLevel . '</td>';
					foreach ($filtered_jobFamily_array as $id => $jobFamily) {
						echo '<td class="cell">';
							echo $lookup_table[$payLevel][$id - 1];
						echo '</td>';
					}
					echo '</tr>';
				}
			?>
				</tbody>
			</table>
		</div>
	</div>

</div>


<?php
	/* Close database connection */
	mysqli_close($conn);
?>
