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
		<ul class="list-unstyled">
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
								<input type="checkbox" id="">
								<label for=""><?php echo $option; ?></label>
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

	/* Get Pay Plans */
	$sql_select_payPlans = "
		SELECT DISTINCT PayPlan
		FROM class_specs
	";
	$qry_select_payPlans = $conn->query($sql_select_payPlans);

	/* Get Pay Levels */
	$sql_select_payLevels = "
		SELECT DISTINCT PayLevel
		FROM pay_levels
	";
	$qry_select_payLevels = $conn->query($sql_select_payLevels);

	/* Get Job Families */
	$sql_select_jobFamilies = "
		SELECT *
		FROM job_families
	";
	$qry_select_jobFamilies = $conn->query($sql_select_jobFamilies);

?>


<div class="container-fluid">
	<div class="row-fluid">

		<!-- Sidebar content -->
		<div class="col-xs-2 c1">
			<form
				name="filters-form"
				role="form"
				action=""
				>
			<?php
				


				$payPlan_array = getColArrayFromQuery($qry_select_payPlans, "PayPlan");
				$payLevel_array = getColArrayFromQuery($qry_select_payLevels, "PayLevel");
				$jobFamily_array = getKeyValArrayFromQuery($qry_select_jobFamilies, "ID", "JobFamily_long");

				createCheckboxFilter("Pay Plan", "payPlan", $payPlan_array);
				createCheckboxFilter("Pay Level", "payLevel", $payLevel_array);
				createCheckboxFilter("Job Family", "jobFamily", $jobFamily_array);
			?>


			</form>
		</div>

		<!-- Body Content -->
		<div class="col-xs-10 c2">
			<table class="table matrix">
				<thead>
					<tr>
						<th>Pay Level</th>
					<?php
						for ($i=0; $i<6; $i++) {
					?>
						<th>
							<a href="">[Job_Family]</a>
						</th>
					<?php
						}
					?>
					</tr>
				</thead>

				<tbody>
				<?php
					for ($row=10; $row <= 19; $row++) {
						echo '<tr>';
							echo '<td class="payLevel">' . $row . '</td>';
						for ($col=0; $col<6; $col++) {
							echo '<td class="cell">0</td>';
						}
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
