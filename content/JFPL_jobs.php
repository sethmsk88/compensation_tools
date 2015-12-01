<!-- Using matrix stylesheet, b/c this table will go on that sheet at some point -->
<link href="./css/JFPL_matrix.css" rel="stylesheet">
<script src="./scripts/JFPL_matrix.js"></script>

<script>
	// Set Header Text
	$('#pageHeader').html("Jobs List");
</script>

<?php
	
	// Connect to DB
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if ($conn->connect_errno){
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	}

	/***  Get Working Department from GET.deptID  ***/
	if (isset($_GET['deptID'])){
	
		// Select all distinct DeptIDs
		$sql = "
			SELECT DISTINCT WorkingDept
			FROM $allActives_table
			WHERE DeptID = " . $_GET['deptID'] . "
			";

		// Run Query
		$workingDept = $conn->query($sql);

		if (!$workingDept){
			echo "Query failed: (" . $conn->errno . ") " . $conn->error;
		}
	}

	// Get job codes that fit criteria
	$sql = "
		SELECT DISTINCT a.JobCode, a.JobTitle
		FROM $allActives_table AS a
		LEFT JOIN $payLevels_table AS p
		ON LPAD(a.JobCode, 4, '0') = LPAD(p.JobCode, 4, '0')
		WHERE 1=1 /* to allow additional where clauses to be ANDed on */";

	// If any of these filters were used, add them to the query as criteria
	if (isset($_GET['deptID'])){
		$sql = $sql . " AND a.DeptID = " . $_GET['deptID'] . " "; 
	}
	if (isset($_GET['pl'])){
		$sql = $sql . " AND p.PayLevel = " . $_GET['pl'] . " "; 
	}
	if (isset($_GET['jf'])){
		$sql = $sql . " AND p.JobFamily = '" . $_GET['jf'] . "' "; 
	}
	// Order query results
	$sql = $sql . "ORDER BY a.JobCode ASC";

	// Run query
	$qry_jobCodes = $conn->query($sql);
	if (!$qry_jobCodes){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

	// Get Job Family
	$sql = "
		SELECT *
		FROM " . $jobFamilies_table . "
		WHERE JobFamily_short = '" . $_GET['jf'] . "'
	";
	if (!$qry_jobFamily = $conn->query($sql)){
		echo 'Query failed: (' . $conn->errno . ') ' . $conn->error;
	}

	// Disconnect from DB
	mysqli_close($conn);
?>


<div class="container">
	<div class="row">
		<div class="col-lg-6">

			<table class="table jobs">
				<caption>
					<?php
						if (isset($_GET['deptID'])){
							echo '<b>Working Department:</b> ' . $workingDept->fetch_assoc()['WorkingDept'] . '<br />';
						}
					
						if (isset($_GET['jf'])){
							echo '<b>Job Family:</b> ' . $qry_jobFamily->fetch_assoc()['JobFamily_long'] . '<br />';
						}
					
						if (isset($_GET['pl'])){
							echo '<b>Pay Level:</b> ' . $_GET['pl'];
						}
					?>
				</caption>
				<thead>
					<tr>
						<th>Job Code</th>
						<th>Description</th>
						<th>Current Salary Range*</th>
					</tr>
				</thead>
				<tbody>
				<?php
				/*
					if (isset($_GET["jf"]) and isset($_GET["pl"]) and isset($_GET["deptID"])){
						$jobs_array = $pl10_housing_jobs_array;
					}
					elseif (isset($_GET["pl"])) {
						$jobs_array = $pl10_jobs_array;
					}
					elseif (isset($_GET["jf"])) {
						$jobs_array = $housing_jobs_array;
					}
					else{
						$jobs_array = array("");
					}
				*/


					// Connect to DB
					$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
					if ($conn->connect_errno){
						echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
					}

					// Loop through jobCodes query
					while ($row = $qry_jobCodes->fetch_assoc()){

						if ($row["JobCode"] == "4206"){
							echo '<tr onclick="window.location=\'?page=job_spec&jobCode=' . $row["JobCode"] . '\'">';
						}
						else{
							echo '<tr>';
						}

						// Get Min and Max
						$sql = "
							SELECT MIN(Annual_Rt) AS minSal, MAX(Annual_Rt) AS maxSal
							FROM $allActives_table
							WHERE JobCode = '" . $row['JobCode']  . "'
							";
						// Run query
						$minMax = $conn->query($sql);
						//dumpQuery($minMax);
						if (!$minMax){
							echo "Query failed: (" . $conn->errno . ") " . $conn->error;
						}

						echo '<td>' . $row['JobCode'] . '</td>';
						echo '<td>' . $row['JobTitle'] . '</td>';
						echo '<td>$';
							while ($row = $minMax->fetch_assoc()){
								echo number_format($row['minSal'], 2, '.', ',') . ' - $';
								echo number_format($row['maxSal'], 2, '.', ',') . '</td>';
							}
						echo '</tr>';

					}


					/*
					foreach ($qry_jobCodes as $jobCode => $jobInfo){

						if ($jobCode == "4206"){
							echo '<tr onclick="window.location=\'?page=job_spec&jobCode=' . $jobCode . '\'">';
						}
						else{
							echo '<tr>';
						}
							// Get Min and Max
							$sql = "
								SELECT MIN(Annual_Rt) AS minSal, MAX(Annual_Rt) AS maxSal
								FROM $allActives_table
								WHERE JobCode = '$jobCode'
								";
							// Run query
							$minMax = $conn->query($sql);
							//dumpQuery($minMax);
							if (!$minMax){
								echo "Query failed: (" . $conn->errno . ") " . $conn->error;
							}

							echo '<td>' . $jobCode . '</td>';
							echo '<td>' . $jobInfo . '</td>';
							echo '<td>$';
								while ($row = $minMax->fetch_assoc()){
									echo number_format($row['minSal'], 2, '.', ',') . ' - $';
									echo number_format($row['maxSal'], 2, '.', ',') . '</td>';
								}
								
							
						echo '</tr>';
					}
					*/

					// Disconnect from DB
					mysqli_close($conn);
				?>
				</tbody>

			</table>

			<div class="footnote">* <b>For an estimate and general career information only.</b> Represents a recent range of highest and lowest campus salaries in this classification. Data may be aged. Salary data were collected on [Data_Collection_Date].</div><br />
			<br />

		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<button id="goBack" type="button" class="btn btn-lg btn-style1">Back</button>
		</div>
	</div>

	<div class="footer">&nbsp;</div>
</div>
