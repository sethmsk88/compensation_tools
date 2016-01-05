<link href="./css/matrix.css" rel="stylesheet">
<script src="./scripts/matrix.js"></script>

<?php
	include "./UDFs.php"; // include shared functions

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
				<ul>
					<li>
						<span class="click-container">
							<input
								type="checkbox"
								id="<?php echo $filterID . '_all'; ?>"
								class="checkbox-all"
								checked="checked">

							<span class="option-label">All</span>
						</span>
						<span class="expand-collapse glyphicon glyphicon-triangle-top"></span>
						<ul class="options-list">
					<?php
						foreach ($options_array as $i => $option) {
					?>
							<li>
								<div class="filter-option">
									<input
										type="checkbox"
										name="<?php echo $filterID . '_'. $i; ?>"
										id="<?php echo $filterID . '_'. $i; ?>"
										class="option-checkbox"
										checked="checked">

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
		ORDER BY PayPlan
	";
	$res_sel_all_payPlans = $conn->query($sql_sel_all_payPlans);

	/*
		Get All Pay Levels
	*/
	$sql_sel_all_payLevels = "
		SELECT DISTINCT PayLevel
		FROM pay_levels
		WHERE PayLevel IS NOT NULL
		ORDER BY PayLevel ASC
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
		WHERE 1 = 1
	";
	$res_sel_filt_jobFamilies = $conn->query($sql_sel_filt_jobFamilies);

	/*
		Get the number of distinct job codes for each (PayLevel x JobFamily) pair
		Query provides $res_sel_jobCodeCount
	*/
	include "./queries/qry_sel_jobCodeCount.php";

	/*
		Create arrays from query results
	*/
	$payPlan_array = getColArrayFromQuery($res_sel_all_payPlans, "PayPlan");
	$payLevel_array = getColArrayFromQuery($res_sel_all_payLevels, "PayLevel");
	$jobFamily_array = getKeyValArrayFromQuery($res_sel_all_jobFamilies, "ID", "JobFamily_long");
	$filtered_jobFamily_array = getKeyValArrayFromQuery($res_sel_filt_jobFamilies, "ID", "JobFamily_long");

	/*
		Modify array values so they are the descriptive forms of
		pay plans.
		(pass by reference)
	*/
	convertPayPlans($payPlan_array, 'pay_levels');

	/*
		Create lookup table to populate matrix table
	*/
	$lookup_table = createLookupTable($payLevel_array,
		count($jobFamily_array),
		$res_sel_jobCodeCount);
?>


<div class="container-fluid">
	<div class="row-fluid">

		<!-- Sidebar content -->
		<div class="sidebar col-md-2">
			<form
				name="filters-form"
				id="filters-form"
				role="form"
				action=""
				>

				<div style="text-align:center;">
					<button
						type="button"
						id="applyFilters-btn"
						class="btn btn-default"
						>
						Apply Filters
					</button>
				</div>

			<?php
				createCheckboxFilter("Pay Plan", "payPlan", $payPlan_array);
				createCheckboxFilter("Pay Level", "payLevel", $payLevel_array);
				createCheckboxFilter("Job Family", "jobFamily", $jobFamily_array);
			?>


			</form>
		</div>
		
		<?php
			/*
				Create matrix
				Default: Show all Job Families and Pay Levels
			*/
			createMatrix($jobFamily_array, $payLevel_array, $lookup_table);
		?>



	</div>

</div>


<?php
	/* Close database connection */
	mysqli_close($conn);
?>
