<link href="./css/JFPL_matrix.css" rel="stylesheet">
<script src="./scripts/JFPL_matrix.js"></script>

<script>
	// Set Header Text
	$('#pageHeader').html("Classification Matrix");
</script>

<?php
	
	// Rows = Pay Levels; Cols = Job Families
	$class_matrix = array(
		10 => array(0,0,0,0,0,0,0,0,0),
		11 => array(0,0,0,0,0,0,0,0,0),
		12 => array(0,0,0,0,0,0,0,0,0),
		13 => array(0,0,0,0,0,0,0,0,0),
		14 => array(0,0,0,0,0,0,0,0,0),
		15 => array(0,0,0,0,0,0,0,0,0),
		16 => array(0,0,0,0,0,0,0,0,0),
		17 => array(0,0,0,0,0,0,0,0,0),
		18 => array(0,0,0,0,0,0,0,0,0),
		19 => array(0,0,0,0,0,0,0,0,0)
		);

	// Declare var to hold the cell links to be used below
	$cellLink = "./?page=JFPL_jobs";

	// Connect to DB
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if ($conn->connect_errno){
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	}

	// Get the number of distinct job codes for each (PayLevel x JobFamily) pair
	$sql = "
		SELECT sub.PayLevel, sub.JobFamily, COUNT(sub.jobCode) AS jobCodeCount
		FROM (
			SELECT DISTINCT a.jobCode, p.PayLevel, p.JobFamily
			FROM $allActives_table AS a
			LEFT JOIN $payLevels_table AS p
			ON LPAD(a.JobCode, 4, '0') = LPAD(p.JobCode, 4, '0')
			WHERE 1=1 /* to allow additional where clauses to be ANDed on */";

	/*
		If the first 3 filters don't all have non-blank values,
		make it so query returns no results.
	*/
	if (!isset($_GET['deptID']) OR !isset($_GET['pl']) OR !isset($_GET['jf'])){
		$sql = $sql . "AND 1=0 ";
	}

	/*
		If the filter is set and not blank or equal to "All", then apply the selected option as criteria to the query
	*/
	if (isset($_GET['deptID']) AND
		$_GET['deptID'] != "all" AND
		$_GET['deptID'] != 'none'
		){

		$sql = $sql . "AND a.DeptID = " . $_GET['deptID'] . " ";
		$cellLink .= '&deptID=' . $_GET['deptID']; // Update cellLink with deptID var
	}
	if (isset($_GET['pl']) AND
		$_GET['pl'] != "all" AND
		$_GET['pl'] != 'none'
		){
		
		$sql = $sql . "AND p.PayLevel = " . $_GET['pl'] . " ";
	}
	if (isset($_GET['jf']) AND
		$_GET['jf'] != "all" AND
		$_GET['jf'] != 'none'
		){
		
		$sql = $sql . "AND p.JobFamily = '" . $_GET['jf'] . "' ";
	}

	$sql = $sql .		
			"ORDER BY p.PayLevel, p.JobFamily
		) AS sub
		GROUP BY sub.JobFamily, sub.PayLevel
		ORDER BY sub.PayLevel, sub.JobFamily
		";

	// Run Query
	$qry_payLevelJobFamily = $conn->query($sql);

	if (!$qry_payLevelJobFamily){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

	// Select all distinct DeptIDs
	$sql = "
		SELECT DISTINCT DeptID, WorkingDept
		FROM $allActives_table
		ORDER BY WorkingDept ASC
		";

	// Run Query
	$all_depts = $conn->query($sql);

	if (!$all_depts){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

	// Select all job families
	$sql = "
		SELECT *
		FROM $jobFamilies_table
		";

	// Run query
	if (!$jobFamilies = $conn->query($sql)){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

?>

<div class="container">
	<div class="row" style="margin-top:10px;">
		<form
			id="filters"
			name="filters"
			method="post"
			role="form"
			action=""
			>
			
			<!-- Working Department Filter -->
			<div class="col-xs-3">
				<label for="workDept_filter" class="control-label">Working Department</label>
				<select
					id="workDept_filter"
					name="workDept_filter"
					class="form-control input-sm"
					>
					
					<!-- Create blank option -->
					<option
						<?php
							if (!isset($_GET['deptID'])){
								echo 'selected="selected" ';
							}
						?>
						value="none"
						>&nbsp;
					</option>

					<!-- Create "All" option -->
					<option
						<?php
							if (isset($_GET['deptID']) AND $_GET['deptID'] == "all"){
								echo 'selected="selected" ';
							}
						?>
						value="all"
						>All
					</option>

					<!-- Create options for each department -->
					<?php
						while ($row = $all_depts->fetch_assoc()){
							echo '<option ';

								// Select a department if URL variable is set
								if (isset($_GET['deptID']) AND $_GET['deptID'] == $row['DeptID']){
									echo 'selected="selected" ';
								}
								echo 'value="' . $row['DeptID'] . '"';
								echo '>';
								echo $row['WorkingDept'];
							echo '</option>';
						}
					?>
				</select>
			</div>

			<!-- Pay Level Filter -->
			<div class="col-xs-3">
				<label for="payLevel_filter" class="control-label">Pay Level</label>
				<select
					id="payLevel_filter"
					name="payLevel_filter"
					class="form-control input-sm"
					>

					<!-- Create blank option -->
					<option
						<?php
							if (!isset($_GET['pl'])){
								echo 'selected="selected" ';
							}
						?>
						value="none"
						>&nbsp;
					</option>

					<!-- Create "All" option -->
					<option
						<?php
							if (isset($_GET['pl']) AND $_GET['pl'] == "all"){
								echo 'selected="selected" ';
							}
						?>
						value="all"
						>All
					</option>

					<!-- Create options for all pay levels -->
					<?php
						for ($i = 10; $i <= 19; $i++){
							echo '<option ';

								// Select a pay level if URL variable is set
								if (isset($_GET['pl']) AND $_GET['pl'] == $i){
									echo 'selected="selected" ';
								}
								echo 'value="' . $i . '"';
								echo '>';
								echo $i;
							echo '</option>';
						}
					?>
				</select>
			</div>

			<!-- Job Family Filter -->
			<div class="col-xs-3">
				<label for="jobFamily_filter" class="control-label">Job Family</label>
				<select
					id="jobFamily_filter"
					name="jobFamily_filter"
					class="form-control input-sm"
					>

				
					<!-- Create blank option -->
					<option
						<?php
							if (!isset($_GET['jf'])){
								echo 'selected="selected" ';
							}
						?>
						value="none"
						>&nbsp;
					</option>

					<!-- Create "All" option -->
					<option
						<?php
							if (isset($_GET['jf']) AND $_GET['jf'] == "all"){
								echo 'selected="selected" ';
							}
						?>
						value="all"
						>All
					</option>

					<!-- Create options for all job families -->
					<?php
						while ($row = $jobFamilies->fetch_assoc()){
							echo '<option ';

								// Select a job family if URL variable is set
								if (isset($_GET['jf']) AND $_GET['jf'] == $row['JobFamily_short']){
									echo 'selected="selected" ';
								}
								echo 'value="' . $row['JobFamily_short'] . '"';
								echo '>';
								echo $row['JobFamily_long'];
							echo '</option>';
						}
					?>
				</select>
			</div>

			<!-- Pay Plan Filter -->
			<div class="col-xs-3">
				<label for="payPlan_filter" class="control-label">Pay Plan</label>
				<select
					id="payPlan_filter"
					name="payPlan_filter"
					class="form-control input-sm"
					>
					<option selected="selected" value="all">All</option>
					<?php
						$i = 0;
						foreach ($payPlan_array as $payPlan){
							echo '<option value="' . $i . '">' . $payPlan . '</option>';
							$i++;
						}
					?>
					
				</select>
			</div>

		</form>
	</div>
	

	<div class="row">
		<div class="col-xs-12">

			<table class="table matrix">
				<thead>
					<tr>
						<!-- Job Families -->
						<th class="payLevel">Pay Level</th>
						<?php

							$jobFamilies->data_seek(0); // Move result set iterator back to start
							while ($row = $jobFamilies->fetch_assoc()){
						?>
							<th class="jobFamily">
							 	<a
							 		role="button"
							 		tabindex="0"
							 		data-toggle="popover"
							 		data-trigger="focus"
							 		data-placement="bottom"
							 		title="<?php echo $row['JobFamily_long']; ?>"
							 		data-content="<?php echo $row['Descr']; ?>"
							 		>
							 		<?php echo $row['JobFamily_long']; ?>
							 	</a>
							</th>
						<?php
							}
						?>
					</tr>
				</thead>
				<tbody>
					<!-- The rest of the cells -->
					<?php

						// For each row in query results
						while ($row = $qry_payLevelJobFamily->fetch_assoc()){

							// Ignore the jobCodes that don't have assigned pay levels (there are 35 of them)
							if ($row['PayLevel'] >= 10){

								/*** Insert jobCodeCount in classMatrix ***/

								$jobFamilies->data_seek(0); // Move result set iterator back to start
								// Loop through all Job Families to find index of this Job Family
								while ($jf_row = $jobFamilies->fetch_assoc()){
									if ($jf_row['JobFamily_short'] == $row['JobFamily']){
										$classMatrixCol = $jf_row['ID'] - 1;
									}
								}
								$class_matrix[$row['PayLevel']][$classMatrixCol] = $row['jobCodeCount'];
							}
						}

						// Classification Matrix
						foreach ($class_matrix as $payLevel => $jobs){
					?>
							<tr>
								<td class="payLevel">
								 	<a
								 		role="button"
								 		tabindex="0"
								 		data-toggle="popover"
								 		data-trigger="focus"
								 		data-placement="right"
								 		title="Pay Level <?php echo $payLevel; ?>"
								 		data-content="<?php /*echo $payLevelDescr_array[$payLevel];*/ ?>"
								 		>
								 		<?php echo $payLevel; ?>
								 	</a>
								</td>
					<?php
								$jobFam_i = 1; // Iterator to tell which job family we are on.
								foreach ($jobs as $jobCodeCount){

									$cellLink_urlVars = "";
									$cellLink_urlVars .= "&pl=" . $payLevel; // Add pay level to cell link

									$jobFamilies->data_seek(0); // Move result set iterator back to start
									// Loop through all Job Families to find index of this Job Family
									while ($jf_row = $jobFamilies->fetch_assoc()){
										if ($jf_row['ID'] == $jobFam_i){
											$cellLink_urlVars .= "&jf=" . $jf_row['JobFamily_short']; // Add job family to cell link
										}
									}

									if ($jobCodeCount > 0){
										echo '<td id="jobsLink" class="cell" onclick="window.location=\'' . $cellLink . $cellLink_urlVars . '\'">' . $jobCodeCount . '</td>';
									}
									else{
										echo '<td class="cell dark">' . $jobCodeCount . '</td>';
									}

									$jobFam_i++;
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
	// Disconnect from DB
	mysqli_close($conn);
?>