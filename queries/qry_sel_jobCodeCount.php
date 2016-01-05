<?php
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
			WHERE p.PayLevel IS NOT NULL
			ORDER BY p.PayLevel, p.JobFamily) AS sub
		GROUP BY sub.JobFamilyID, sub.PayLevel
		ORDER BY sub.PayLevel, sub.JobFamilyID
	";

	if (!($res_sel_jobCodeCount = $conn->query($sql_sel_jobCodeCount))){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}
?>
