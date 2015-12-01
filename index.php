<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle="Compensation/Classification Tools"; ?></title>

    <!-- Linked stylesheets -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/master.css" rel="stylesheet">
    <link href="./css/main.css" rel="stylesheet">

    <!-- Included PHP Libraries -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '\bootstrap\libraries-php\stats.php'; ?>

    <!-- Included UDFs -->
    <?php include "../shared/query_UDFs.php"; ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/bootstrap/js/bootstrap.min.js"></script>

    <!-- Included Scripts -->
    <script src="./scripts/main.js"></script>
    <script src="/bootstrap/js/money_formatting.js"></script>
    <script src="/bootstrap/js/median.js"></script>

    <?php

        // Include my database info
        include "../shared/dbInfo.php";

        // Set application homepage
        $homepage = "JFPL_matrix";

        // Globals
        $allActives_table = "all_active_fac_staff";
        $payLevels_table = "pay_levels";
        $payLevelsDescr_table = "pay_levels_descr";
        $jobFamilies_table = "job_families";

        // key = job_family_short
        // value = array(position_in_class_matrix, job_family_long)"
        /*$jobFamily_array = array(
            "AA"    => array(0, "Academic Affairs"),
            "ABS"   => array(1, "Administrative &amp; Business Services"),
            ""      => array(2, "Athletics"),
            "BFS"   => array(3, "Budget &amp; Financial Services"),
            "CAPRM" => array(4, "Communications, Advancement, PR, &amp; Marketing"),
            "FGS"   => array(5, "Facilities &amp; Grounds Services"),
            "SS"    => array(6, "Safety &amp; Security"),
            "STS"   => array(7, "Student Services"),
            "TS"    => array(8, "Technology Services")
        );

        // Used for creating links in matrix
        $indexed_jobFamily_array = array("AA","ABS","","BFS","CAPRM","FGS","SS","STS","TS");*/

        $payPlan_array = array("USPS", "A&amp;P", "Faculty", "Executive");

    	// If a page variable exists, include the page
    	if (isset($_GET["page"])){
    		$filePath = './content/' . $_GET["page"] . '.php';
    	}
    	else{
    		$filePath = './content/' . $homepage . '.php';
    	}

        // Include Header
        $headerText = "&nbsp;";
        include "../templates/header_2.php";

    	if (file_exists($filePath)){
			include $filePath;
		}
		else{
			echo '<h2>404 Error</h2>Page does not exist';
		}

    ?>




  </body>
</html>
