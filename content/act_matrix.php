<?php
	/* Receive post data from matrix.php, create new table */

	include "../../shared/dbInfo.php";
	include "../UDFs.php"; // include shared functions

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
	$payPlan_array = getColArrayFromQuery($res_sel_all_payPlans, "PayPlan");

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
	$payLevel_array = getColArrayFromQuery($res_sel_all_payLevels, "PayLevel");

	/*
		Create WHERE clauses based on which filters are selected.
	*/
	$where_payLevel = "";
	$where_payPlan = "";
	$where_jobFamily = "";
	
	foreach ($_POST as $filter_id => $flag) {
		$splitFilter = explode('_', $filter_id);
		$category = $splitFilter[0];
		$filterNum = $splitFilter[1];

		if ($category == "payPlan") {
			$payPlan = $payPlan_array[$filterNum];
			$where_payPlan .= " OR PayPlan = '" . $payPlan . "'";
		}
		else if ($category == "payLevel") {
			$payLevel = $payLevel_array[$filterNum];
			$where_payLevel .= " OR PayLevel = " . $payLevel;
		}
		else if ($category == "jobFamily") {
			$where_jobFamily .= " OR ID = " . $filterNum;
		}
	}

	/*
		Get filtered pay plans
	*/
	$sql_sel_filt_payPlans = "
		SELECT DISTINCT PayPlan
		FROM class_specs
		WHERE 1 = 0" . $where_payPlan;
	if (!$res_sel_filt_payPlans = $conn->query($sql_sel_filt_payPlans)) {
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

	/*
		Get filtered pay levels
	*/
	$sql_sel_filt_payLevels = "
		SELECT DISTINCT PayLevel
		FROM pay_levels
		WHERE PayLevel IS NOT NULL AND (1 = 0" . $where_payLevel . ")";
	if (!$res_sel_filt_payLevels = $conn->query($sql_sel_filt_payLevels)) {
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

	/*
		Get filtered job families
	*/
	$sql_sel_filt_jobFamilies = "
		SELECT *
		FROM job_families
		WHERE 1 = 0" . $where_jobFamily;
	if (!$res_sel_filt_jobFamilies = $conn->query($sql_sel_filt_jobFamilies)) {
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}

	
	$filtered_payPlan_array = getColArrayFromQuery($res_sel_filt_payPlans, "PayPlan");
	$filtered_payLevel_array = getColArrayFromQuery($res_sel_filt_payLevels, "PayLevel");
	$filtered_jobFamily_array = getKeyValArrayFromQuery($res_sel_filt_jobFamilies, "ID", "JobFamily_long");

	/*
		Get the number of distinct job codes for each (PayLevel x JobFamily) pair
		Query provides $res_sel_jobCodeCount
	*/
	include "../queries/qry_sel_jobCodeCount.php";


	/*
		Create lookup table to populate matrix table
	*/
	$lookup_table = createLookupTable($filtered_payLevel_array, count($filtered_jobFamily_array), $res_sel_jobCodeCount);

	/* Print new matrix to screen */
	createMatrix($filtered_jobFamily_array, $filtered_payLevel_array, $lookup_table);

	/* Close database connection */
	mysqli_close($conn);
?>
