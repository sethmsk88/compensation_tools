<link href="./css/comp_analysis.css" rel="stylesheet">
<script src="./scripts/comp_analysis.js"></script>

<script>
	// Set Header Text
	$('#pageHeader').html("Compensation Analysis");
</script>

<?php
	// Connect to DB
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if ($conn->connect_errno){
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	}

	// Select all employees with the specified job code
	$sql = "
		SELECT *
		FROM $allActives_table
		WHERE JobCode = '4206'
		ORDER BY Annual_Rt ASC
		";

	// Run Query
	$jobCodeEmps = $conn->query($sql);

	if (!$jobCodeEmps){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}
	else{
		$sal_array = array();

		// Copy query results into array
		foreach ($jobCodeEmps as $row){
			array_push($sal_array, $row['Annual_Rt']);
		}
	}

	// Select all employees with the specified job code and DeptID
	$sql = "
		SELECT Annual_Rt
		FROM $allActives_table
		WHERE JobCode = '4206' AND
			DeptID = 45001
		ORDER BY Annual_Rt ASC
		";

	$workDeptEmps = $conn->query($sql);

	if (!$workDeptEmps){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
	}
	else{
		// Array containing salaries for the specified department
		$dept_sal_array = array();

		// Copy query results into array
		foreach ($workDeptEmps as $row){
			array_push($dept_sal_array, $row['Annual_Rt']);
		}
	}

	// Close DB connection
	mysqli_close($conn);

	// Calculate quartiles
	$actualQuartile_array = [
		1 => quartiles($sal_array, 1),
		2 => quartiles($sal_array, 2),
		3 => quartiles($sal_array, 3)
	];

	$deptQuartile_array = [
		1 => quartiles($dept_sal_array, 1),
		2 => quartiles($dept_sal_array, 2),
		3 => quartiles($dept_sal_array, 3)
	];
?>

<div class="container">

	<div class="col-xs-4">
		<table class="empSals table table-striped table-bordered table-hover">
			<caption><b>Job Code</b>: 4206 - Program Assistant</caption>
			<thead>
				<tr>
					<th>Name</th>
					<th>Salary</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$jobCodeEmps->data_seek(0); // Move result set iterator to start
				$empCount = 0;
				$currentQuartile = 1;
				while ($row = $jobCodeEmps->fetch_assoc()){

					// Display quartile marker when necessary
					/*
						If current salary is larger than the current quartile,
						display the current quartile marker.
					*/
					if (($currentQuartile < 4) && ($row['Annual_Rt'] >= $actualQuartile_array[$currentQuartile])){
						echo '<tr>';
							echo '<td class="quartile-cell" colspan="2">Q' . ++$currentQuartile . '</td>';
						echo '</tr>';
					}

					echo '<tr>';
						echo '<td>Employee #' . ++$empCount . '</td>';
						echo '<td>$' . number_format($row['Annual_Rt'], 2, '.', ',') . '</td>';
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
	</div>

	<!-- Recommended Job Code Hiring Range -->
	<div class="col-xs-6">
		<div class="outWrapper" style="margin-top:10px;">
			<div class="inWrapper">
				<table class="quartiles">
					<caption>Recommended Job Code Hiring Range</caption>
					<thead>
						<tr>
							<th class="align-left">Min</th>
							<th class="align-center" colspan="2">Midpoint</th>
							<th class="align-right">Max</th>
						</tr>
						<tr>
							<th class="align-left">$25,920.00</th>
							<th id="recommended-mid" class="align-center" colspan="2">$32,400.00</th>
							<th class="align-right">$38,800.00</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="cell-4 bord-top bord-left">&nbsp;</td>
							<td class="cell-4 bord-top divider"></td>
							<td class="cell-4 bord-top"></td>
							<td class="cell-4 bord-top bord-right"></td>
						</tr>

						<tr>
							<td class="cell-4 divider bord-left">&nbsp;</td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 bord-right"></td>
						</tr>
						
						<tr class="quart-labels">
							<td class="cell-4">Q1</td>
							<td class="cell-4">Q2</td>
							<td class="cell-4">Q3</td>
							<td class="cell-4">Q4</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Pay Level Range -->
	<div class="col-xs-6">
		<div class="outWrapper">
			<div class="inWrapper">
				<table class="quartiles">
					<caption><a href="./Pay_Level_10.xlsx" target="_blank" style="color:inherit;">Pay Level 10 Range</a></caption>
					<thead>
						<tr>
							<th class="align-left">Min</th>
							<th class="align-center" colspan="2">Midpoint</th>
							<th class="align-right">Max</th>
						</tr>
						<tr>
							<th class="align-left">$17,300.00</th>
							<th class="align-center" colspan="2">$24,978.94</th>
							<th class="align-right">$45,000.00</th>
						</tr>
					</thead>
					<tbody>
						<tr id="fillRow_1">
							<td class="cell-4 bord-top bord-left">&nbsp;</td>
							<td class="cell-4 bord-top divider"></td>
							<td class="cell-4 bord-top"></td>
							<td class="cell-4 bord-top bord-right"></td>
						</tr>

						<tr id="fillRow_2">
							<td class="cell-4 divider bord-left">&nbsp;</td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 bord-right"></td>
						</tr>
						
						<tr class="quart-labels">
							<td class="cell-4">Q1</td>
							<td class="cell-4">Q2</td>
							<td class="cell-4">Q3</td>
							<td class="cell-4">Q4</td>
						</tr>
					</tbody>
				</table>
				<canvas id="payLevel_canvas" class="canvasOverlay"></canvas>
				<script>
					var canvas = document.getElementById('payLevel_canvas');
					var context = canvas.getContext('2d');
					var canvasWidth = canvas.offsetWidth;
					canvasWidth = 300; // Idk why, but this value is being calculated as 300 instead of 480
					var canvasHeight = canvas.offsetHeight;
					canvasHeight += 50; // additional height needed for canvasHeight to be correct

					var fillRowHeight = $('#fillRow_1').height() + $('#fillRow_2').height();
					fillRowHeight += 20; // additional height needed for fillRowHeight to be correct
					
					var yPos = canvasHeight - fillRowHeight;
					
					var plMin = 17300;
					var plMid = 24978.94;
					var plMax = 45000;
					var recMin = 25920;
					var recMax = 38800;
					var plQ34_range = plMax - plMid;
					var plRecMidOffset = recMin - plMid; // If this is negative, set it to 0
					var plRecMaxOffset = recMax - plMid; // If this is negative, set it to 0
					var midLine = ((plRecMidOffset / plQ34_range) * (canvasWidth / 2)) + (canvasWidth / 2);
					var maxLine = ((plRecMaxOffset / plQ34_range) * (canvasWidth / 2)) + (canvasWidth / 2);

					var plRange = plMax - plMin;
					var plRecMinOffset = recMin - plMin;
					var minLine = plRecMinOffset / plRange * canvasWidth;
					// minLine is num of px from left size of canvas
					//var minLine = (1 - recPlMin / recRange) * canvasWidth;

					// Draw Q34 bar
					context.beginPath();
					context.rect(midLine, yPos, maxLine-midLine, fillRowHeight);
					context.fillStyle = "rgba(15,108,55,0.5)";
					context.fill();
					context.lineWidth = 0;
					context.strokeStyle = "rgba(0,0,0,0)";

					context.stroke();

				</script>
			</div>
		</div>
	</div>

	<!-- Pay Range for Working Department -->
	<div class="col-xs-6">
		<div class="outWrapper" style="margin-bottom:80px;">
			<div class="inWrapper">
				<table class="quartiles">
					<caption>
						Working Department: Housing<br />
						Pay Range for Working Department
					</caption>
					<thead>
						<tr>
							<th class="align-left">Min</th>
							<th class="align-center" colspan="2">Midpoint</th>
							<th class="align-right">Max</th>
						</tr>
						<tr>
							<th class="align-left">
								<?php echo '$' . number_format(min($dept_sal_array), 2, '.', ','); ?>
							</th>
							<th id="workDept-mid" class="align-center" colspan="2">
								<?php echo '$' . number_format($deptQuartile_array[2], 2, '.', ','); ?>
							</th>
							<th class="align-right">
								<?php echo '$' . number_format(max($dept_sal_array), 2, '.', ','); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="cell-4 bord-top bord-left">&nbsp;</td>
							<td class="cell-4 bord-top divider"></td>
							<td class="cell-4 bord-top"></td>
							<td class="cell-4 bord-top bord-right"></td>
						</tr>

						<tr>
							<td class="cell-4 divider bord-left">&nbsp;</td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 bord-right"></td>
						</tr>
						
						<tr class="quart-labels">
							<td class="cell-4">Q1</td>
							<td class="cell-4">Q2</td>
							<td class="cell-4">Q3</td>
							<td class="cell-4">Q4</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- FAMU Actual Pay Range -->
	<div class="col-xs-6">
		<div class="outWrapper">
			<div class="inWrapper">
				<table class="quartiles">
					<caption>FAMU Actual Pay Range</caption>
					<thead>
						<tr>
							<th class="align-left">Min</th>
							<th class="align-center" colspan="2">Midpoint</th>
							<th class="align-right">Max</th>
						</tr>
						<tr>
							<th class="align-left">
								<?php echo '$' . number_format(min($sal_array), 2, '.', ','); ?>
							</th>
							<th id="actual-mid" class="align-center" colspan="2">
								<?php echo '$' . number_format($actualQuartile_array[2], 2, '.', ','); ?>
							</th>
							<th class="align-right">
								<?php echo '$' . number_format(max($sal_array), 2, '.', ','); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="cell-4 bord-top bord-left">&nbsp;</td>
							<td class="cell-4 bord-top divider"></td>
							<td class="cell-4 bord-top"></td>
							<td class="cell-4 bord-top bord-right"></td>
						</tr>

						<tr>
							<td class="cell-4 divider bord-left">&nbsp;</td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 divider"></td>
							<td class="cell-4 bord-right"></td>
						</tr>
						
						<tr class="quart-labels">
							<td class="cell-4">Q1</td>
							<td class="cell-4">Q2</td>
							<td class="cell-4">Q3</td>
							<td class="cell-4">Q4</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- External Market Ratio -->
	<div class="col-xs-6">
		<br />

		<!-- Create a popup explanation of the different ratios -->
		<!-- Overall = Mid of recommended / aggregate mid of external -->
		<!-- WorkDept Ext = Mid of WorkDept / aggregate mid of external -->
		<!-- WorkDept Int = Mid of WorkDept / mid of FAMU actual pay range -->
		<a
			role="button"
			tabindex="0"
			data-toggle="popover"
			data-trigger="focus"
			data-placement="left"
			title=""
			data-content="Midpoint of Recommended Job Code Hiring Range / Aggregate Midpoint of External Benchmarks"
			>
			<span class="myCaption">Overall Market Ratio: <span id="overall_market_ratio" class="marketRatio"></span></span>
		</a><br />
		<a
			role="button"
			tabindex="0"
			data-toggle="popover"
			data-trigger="focus"
			data-placement="left"
			title=""
			data-content="Midpoint of Working Department Pay Range / Aggregate Midpoint of External Benchmarks"
			>
			<span class="myCaption">Working Department External Market Ratio: <span id="workDept_ext_market_ratio" class="marketRatio"></span></span>
		</a><br />
		<a
			role="button"
			tabindex="0"
			data-toggle="popover"
			data-trigger="focus"
			data-placement="left"
			title=""
			data-content="Midpoint of Working Department Pay Range / Midpoint of FAMU Actual Pay Range"
			>
			<span class="myCaption">Working Department Internal Market Ratio: <span id="workDept_int_market_ratio" class="marketRatio"></span></span>
		</a><br />
		<!--
		<span class="myCaption">External Market Ratio: <span id="market_ratio"></span></span><br />
		-->
		<br />

		<form name="extMarketRatio" id="extMarketRatio" role="form" action="" >
			<table id="extMarketRatio" class="table-striped table-hover">
				<thead>
					<th>Source</th>
					<th>Min</th>
					<th>25%</th>
					<th>Midpoint</th>
					<th>75%</th>
					<th>Max</th>
				</thead>
				<tbody>
				<?php
					for ($i = 0; $i < 4; $i++){
						echo '<tr>';
							echo '<td>';
								echo '<input name="src_' . $i . '"' .
										' id="src_' . $i . '"' .
										' type="text"' .
										'>';
							echo '</td>';
							echo '<td>';
								echo '<input name="min_' . $i . '"' .
										' id="min_' . $i . '"' .
										' type="text"' .
										' class="calc calc-lowest"' .
										'>';
							echo '</td>';
							echo '<td>';
								echo '<input name="Q1_' . $i . '" id="Q1_' . $i . '" type="text" class="calc calc-lowest">';
							echo '</td>';
							echo '<td>';
								echo '<input name="mid_' . $i . '" id="mid_' . $i . '" type="text" class="calc calc-mid">';
							echo '</td>';
							echo '<td>';
								echo '<input name="Q3_' . $i . '" id="Q3_' . $i . '" type="text" class="calc calc-highest">';
							echo '</td>';
							echo '<td>';
								echo '<input name="max_' . $i . '" id="max_' . $i . '" type="text" class="calc calc-highest">';
							echo '</td>';
						echo '</tr>';
					}
				?>
					<tr>
						<td>Aggregate</td>
						<td id="aggregate_low" colspan="2" class="aggregate-cell"></td>
						<td id="aggregate_mid" class="aggregate-cell"></td>
						<td id="aggregate_high" colspan="2" class="aggregate-cell"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>

</div>