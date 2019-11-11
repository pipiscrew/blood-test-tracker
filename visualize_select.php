<?php
@session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}

require_once('general.php');

$db = new dbase();
$db->connect_sqlite();

//fill chooser
$blood_tests = $db->getSet("select blood_id, blood_date, blood_where, blood_comments from blood_tests order by blood_date asc", null);

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

		<script src="assets/jquery.min.js"></script>
        <script src="assets/bootstrap-selector.js"></script>


        <script>

			$(function ()
				{
					var jArray_blood_tests =   <?php echo json_encode($blood_tests); ?>;

                    $('#test').chooser();
                    //pass an array with id and usernames to fillList function to autofill the List group
                    $("#test").fillList(jArray_blood_tests, "Blood Tests", "blood_id", "blood_date");

                    $('#btn_select_all').on('click', function(e) {
                        e.preventDefault();
                        
                        $('#test').setAll(true);
                    });

                    $('#btn_deselect_all').on('click', function(e) {
                        e.preventDefault();
                        
                        $('#test').setAll(false);
                    });
                
                }); //jQ ends

                function go_chart() {
                    //get #selected countries#
                    var countries = $("#test").getSelected();
                   console.log();
                    if (!countries[0] || !countries[1])
                    {
                        alert("Please choose blood tests");
                        return false;
                    }

  

                    location.href= 'visualize.php?blood_test_ids='+countries.join(',');
                }

                function go_export() {
                    //get #selected countries#
                    var countries = $("#test").getSelected();
                   console.log();
                    if (!countries[0] || !countries[1])
                    {
                        alert("Please choose blood tests");
                        return false;
                    }

  

                    location.href= 'export_csv.php?blood_test_ids='+countries.join(',');
                }
        </script>
    </head>
<body>
    <div class="container">

        <div class="row">
            <div class="col-md-2">
                <div id="test" class="list-group centre" ></div>

                <button  class="btn btn-primary" onclick="go_chart()">visualize</button>
                <button  class="btn btn-primary" onclick="go_export()">export</button>
            </div>

            <button class="btn btn-success" id="btn_select_all" style="margin-bottom:10px">+</button>
			<button class="btn btn-success" id="btn_deselect_all" style="margin-bottom:10px">-</button>
							
        </div>
     </div>
</body>
</html>