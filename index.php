<?php
	date_default_timezone_set("UTC");
    	
	@session_start();
	if (!isset($_SESSION["id"])) {
		header("Location: login.php");
		exit ;
	}
	else {
		
		
		if ($_SESSION["login_expiration"] != date("Y-m-d"))
		{	
			session_destroy();
			header("Location: login.php");
			exit ;
		} 
        
	}
	
require_once('general.php');
	
$db = new dbase();
$db->connect_sqlite();

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>
			PipisCrew
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="assets/bootstrap.min.css">
		<link rel="stylesheet" href="assets/bootstrap-table.min.css">
		
		<script src="assets/jquery.min.js"></script>
		<script src="assets/bootstrap.min.js"></script>
		<script src="assets/bootstrap-table.min.js"></script>
		
		

		<script>
			
			var loading = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');
		
			$(function () {
				
			})
			
		</script>
		
		<style type="text/css">
			.modal-backdrop {
				opacity: 0.7;
				filter: alpha(opacity=70);
				background: #fff;
				z-index: 2;
			}

			div.loading {
				position: fixed;
				margin: auto;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				width: 200px;
				height: 30px;
				z-index: 3;
			}
		</style>
	</head>
	<body>

		<ul class='nav nav-tabs' id='tabContainer'> <!-- or class='nav nav-tabs' -->
			<li class="active">
				<a href="#bloodtests" data-toggle='tab'>Blood Tests</a>
			</li>
<?php
if ($_SESSION['level'] == 2)
{ ?>
			<li>
				<a href="#examtypes" data-toggle='tab'>Examination Types</a>
			</li>
			<li>
				<a href="#examgroups" data-toggle='tab'>Examination Groups</a>
			</li>
			<li>
				<a href="logout.php" >Logout</a>
			</li>
<?php	} ?>

		</ul>

		<!-- TABS Content [START] -->
		<div id="tabs" class="tab-content">

			<div class="tab-pane fade in active" id="bloodtests">
				<div class="row" style="padding:10px">
					<div class="col-md-6">
						<?php
						include ('tab_blood_tests.php');
						?>
					</div>
					<div class="col-md-6">
						<?php
						include ('tab_blood_test_exams.php');
						?>
					</div>
				</div>
			</div>


<?php
	if ($_SESSION['level'] == 2)
	{ ?>
			<div class="tab-pane fade" id="examtypes">
				<?php
				include ('tab_exam_types.php');
				?>
			</div>

			<div class="tab-pane fade" id="examgroups">
				<?php
				include ('tab_exam_groups.php');
				?>
			</div>


<?php	} ?>
		
		</div>
		<!-- TABS Content [END] -->
		
	</body>
</html>