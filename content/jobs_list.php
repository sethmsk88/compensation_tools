<link href="./css/matrix.css" rel="stylesheet">
<script src="./scripts/matrix.js"></script>

<?php
	include "./UDFs.php"; // include shared functions
	
	/* Connect to database */
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if ($conn->connect_errno){
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	}

	/* DUMMY TEST DATA */
	$_POST["payLevel"] = 10;
	$_POST["jobFamily_ID"] = 2; // Admin Business Services

	// Select Class Specs that meet the criteria posted to the page
	// Criteria (PayLevel, JobFamily, DeptID(NOT YET IMPLEMENTED))
	$param_str_PayLevel = $_POST["payLevel"];
	$param_int_JobFamily_ID = $_POST["jobFamily_ID"];

	$select_classSpecs = "
		SELECT DISTINCT p.JobCode, p.JobTitle, j.JobFamily_long
		FROM pay_levels p
		LEFT JOIN job_families j
		ON p.JobFamily = j.JobFamily_short
		WHERE p.PayLevel = ? AND
			j.ID = ?
		ORDER BY p.JobCode, J.ID
	";

	// Prepare, Bind, and Run query
	if (!$stmt = $conn->prepare($select_classSpecs)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	else if (!$stmt->bind_param("ii",
		$param_str_PayLevel,
		$param_int_JobFamily_ID)) {
		echo 'Binding params failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else if (!$stmt->execute()) {
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	$select_classSpecs_result = $stmt->get_result();
	$stmt->close();

	echo 'Num Rows: ' . $select_classSpecs_result->num_rows . '<br />'; // TESTING
	dumpQuery($select_classSpecs_result); // TESTING
?>

<div class="container">
	<div class="row">
		<div class="col-xs-6">
			<table class="table jobs">
				<caption>
				<?php
					echo '<b>Job Family:</b> ' . $select_classSpecs_result->fetch_assoc()['JobFamily_long'] . '<br />';

					/* Rewind result set, b/c the above line will move the result set pointer */
					$select_classSpecs_result->data_seek(0);

					echo  '<b>Pay Level:</b> ' . $_POST['payLevel'];
				?>
				</caption>
				<thead>
					<tr>
						<th>Job Code</th>
						<th>Job Title</th>
					</tr>
				</thead>
				<tbody>
				<?php
					while ($row = $select_classSpecs_result->fetch_assoc()) {
				?>
						<tr>
							<td><?php echo $row['JobCode']; ?></td>
							<td><?php echo $row['JobTitle']; ?></td>
						</tr>
				<?php
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>





<?php
	// Close DB connection
	mysqli_close($conn);
?>
