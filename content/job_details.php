<link href="./css/job_details.css" rel="stylesheet">

<?php

	include "./UDFs.php"; // include shared functions

	/* Connect to DB */
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	/* Get class spec */
	if (isset($_GET['jc']))
		$param_str_JobCode = $_GET['jc'];
	else
		$param_str_JobCode = ""; // Default

	$select_classSpec_sql = "
		SELECT c.*, p.PayLevel, j.JobFamily_long
		FROM class_specs c
		JOIN pay_levels p
		ON p.JobCode = c.JobCode
		JOIN job_families j
		ON j.ID = c.JobFamilyID
		WHERE c.JobCode = ? AND
			c.Active = 1
	";

	/* Prepare, Bind, and Run query */
	if (!$stmt = $conn->prepare($select_classSpec_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	else if (!$stmt->bind_param("s", $param_str_JobCode)) {
		echo 'Binding params failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else if (!$stmt->execute()) {
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	$select_classSpec_result = $stmt->get_result();
	$stmt->close();

	/* Display warning if there are multiple class specs in query result */
	if ($select_classSpec_result->num_rows > 1) {
		echo '<span class="warning">Warning: A duplicate class spec exists. Please contact the administrator.</span>';
	}

	/* Get first row from query result */
	$classSpec_row = $select_classSpec_result->fetch_assoc();


	/* Get all competencies for this class spec */
	$param_int_ClassSpec_ID = $classSpec_row['ID'];
	
	$select_competencies_sql = "
		SELECT b.*
		FROM class_specs_rec_competencies a
		JOIN competencies b
		ON b.ID = a.Competency_ID
		WHERE a.ClassSpec_ID = ?
		ORDER BY b.Descr
	";

	/* Prepare, Bind, and Run query */
	if (!$stmt = $conn->prepare($select_competencies_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	else if (!$stmt->bind_param("i", $param_int_ClassSpec_ID)) {
		echo 'Binding params failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else if (!$stmt->execute()) {
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	$select_competencies_result = $stmt->get_result();
	$stmt->close();
?>

<br />
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<span class="myLabel">Job Code:</span>
			<?php echo $classSpec_row['JobCode']; ?>
		</div>
		<div class="col-md-9">
			<span class="myLabel">Job Title:</span>
			<?php echo stripslashes($classSpec_row['JobTitle']); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<span class="myLabel">Job Family:</span>
			<?php echo $classSpec_row['JobFamily_long']; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<span class="myLabel">Pay Plan:</span>
			<?php echo convertPayPlan($classSpec_row['PayPlan'], 'long'); ?>
		</div>
		<div class="col-md-9">
			<span class="myLabel">Pay Level:</span>
			<?php echo $classSpec_row['PayLevel']; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<span class="myLabel">FLSA Status:</span>
			<?php echo convertFLSA($classSpec_row['FLSA'], 'string'); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>Position Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php echo $classSpec_row['PositionDescr']; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>Education/Experience:</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php echo $classSpec_row['EducationExp']; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table id="recComp" class="table table-bordered table-condensed table-striped">
				<thead>
					<tr>
						<th>Recommended Competencies</th>
					</tr>
				</thead>
				<tbody>
					<?php
						// Create list item for each competency
						while($row = $select_competencies_result->fetch_assoc()){
							echo '<tr>';
							echo '<td>' . stripslashes($row['Descr']) . '</td>';
							echo '</tr>';
						}
					?>
				</tbody>
			</table>			
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table id="otherReq-table" class="table table-bordered table-condensed table-striped">
				<thead>
					<tr>
						<th colspan="2">Other Specific Requirements</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Police Background Check</td>
						<td>
							<?php echo convertYesNo($classSpec_row['BackgroundCheck']); ?>
						</td>
					</tr>
					<tr>
						<td>Financial Disclosure</td>
						<td>
							<?php echo convertYesNo($classSpec_row['FinancialDisclosure']); ?>
						</td>
					</tr>
					<tr>
						<td>Pre/Post Offer Physical</td>
						<td>
							<?php echo convertYesNo($classSpec_row['Physical']); ?>
						</td>
					</tr>
					<tr>
						<td>Confidentiality Statement</td>
						<td>
							<?php echo convertYesNo($classSpec_row['ConfidentialityStmt']); ?>
						</td>
					</tr>
					<tr>
						<td>Child Care Security Check</td>
						<td>
							<?php echo convertYesNo($classSpec_row['ChildCareSecurityCheck']); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	/* Close DB connection */
	mysqli_close($conn);
?>
