<?php
@session_start();

require_once('general.php');

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}

if (!isset($_GET['blood_test_ids'])){
	echo "error010101010";
	return;
}

$blood_test_ids = $_GET['blood_test_ids'];

$blood_test_ids_arr = str_getcsv($blood_test_ids, ',');


$db = new dbase();
$db->connect_sqlite();


$chart_html = <<<EOT
<div style="width:45%;">
<canvas id="chart{{id}}"></canvas>
</div>
EOT;

$chart_exam_type_dataset = <<<EOT
{
    label: '{{acronym}}',
    backgroundColor: '{{color}}',
    borderColor: '{{color}}',
    data: {{data}},
    fill: false,
    steppedLine: true,
    spanGaps: true,
    hidden: {{enabled}}
}
EOT;

$chart_script = <<<EOT
var ctx = document.getElementById('chart{{id}}').getContext('2d');
var chart = new Chart(ctx, {

    type: 'line',
    data: {
        labels: {{blood_test_dates}},
        datasets: [{{exam_types}}]
    },

    options: {
            responsive: true,
            title: {
                display: true,
                text: '{{exam_group}}'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Date'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    }
                }]
            }
        }
});
EOT;

//parse all exam_groups to an array
$records = array();
$exam_grps = $db->getSet("select * from exam_groups order by examgrp_id", null);
foreach($exam_grps as $grp)
{
    $rec = new stdClass;
    $rec->grp_id = $grp['examgrp_id'];
    $rec->grp_name = $grp['examgrp_name'];

    $records[] = $rec;
}
//parse all exam_groups to an array

//hang foreach exam_group the exam_types
foreach($records as $record)
{
    $types = array();
    $exam_types = $db->getSet('select * from exam_types where exam_group_id = ? order by exam_group_sort_order', array($record->grp_id));
    
    foreach($exam_types as $exam)
    {
        $types[] = array('id' => $exam['exam_type_id'], 'acronym' =>  $exam['exam_acronym']);
    }  
    
    $record->exam_types = $types;
}
//hang foreach exam_group the exam_types


//construct the date X axis of the chart
$blood_test_dates = $db->getSet("select blood_date from blood_tests where blood_id in ($blood_test_ids) order by blood_date asc", null);

$blood_test_dates_JSON = array();
foreach($blood_test_dates as $row)
{
    array_push($blood_test_dates_JSON, $row['blood_date']);
}
$blood_test_dates = json_encode( $blood_test_dates_JSON);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>
			PipisCrew
		</title>
		<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="assets/bootstrap.min.css">
		
		<script src="assets/jquery.min.js"></script> -->
        <script src="assets/chart.js"></script>

    <style>
        canvas{
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }

	</style>
	</head>


<body>

        <div class="row">
  
<?php

//https://www.bestrandoms.com/random-colors
//https://www.chartjs.org/samples/latest/charts/line/basic.html
$colors = array(
'rgb(255, 99, 132)',
'rgb(255, 159, 64)',
'rgb(255, 205, 86)',
'rgb(75, 192, 192)',
'rgb(54, 162, 235)',
'rgb(153, 102, 255)',
'rgb(201, 203, 207)', //
'rgb(0,20,168)',
'rgb(194,178,128)',
'rgb(172,229,238)',
'rgb(181,114,129)',
'rgb(67,179,174)',
'rgb(0,109,91)',
'rgb(194,178,128)'
);

$colors_counter = 0;
    
//for each group
foreach($records as $record)
{
    $exam_visible_on_chart = "false";
    $chart_datesets_json = "";
    $colors_counter = 0;

    //Chart HTML
    echo str_replace('{{id}}',$record->grp_id,$chart_html);

    //Chart JS - construct the datasets
    //for each exam type in group
    foreach($record->exam_types as $exam_type) {

        //init for each exam_type
        $exam_type_values_JSON = array();

        //for each blood_test selected
        foreach($blood_test_ids_arr as $blood_test_id){
            $v = $db->getScalar("select exam_value from blood_test_exams 
                where blood_test_id = $blood_test_id and exam_type_id = ".$exam_type['id'], null);

            if ($v == 0)
                $v = null; //when on exam we didnt done thr particular exam should write 0, but null so chart dont create a checkpoint (chart needs null)

            array_push($exam_type_values_JSON, $v);
        }

        $searchReplaceArray = array(
            '{{acronym}}' => $exam_type['acronym'], 
            '{{color}}' => $colors[$colors_counter],
            '{{enabled}}' => $exam_visible_on_chart,
            '{{data}}' => json_encode( $exam_type_values_JSON )
            );
    
            $chart_datesets_json .= str_replace(
            array_keys($searchReplaceArray), 
            array_values($searchReplaceArray), 
            $chart_exam_type_dataset
            ) .  ',';

            
            $colors_counter+=1;
            $exam_visible_on_chart = "true";
    }


    echo "<script>";

    $searchReplaceArray = array(
        '{{id}}' => $record->grp_id,
        '{{blood_test_dates}}' => $blood_test_dates,
        '{{exam_group}}' => $record->grp_name,
        '{{exam_types}}' => $chart_datesets_json
        );

    echo str_replace(
        array_keys($searchReplaceArray), 
        array_values($searchReplaceArray), 
        $chart_script
        );

    echo "</script>";

}

?>

        </div>
        
    </div>
</body>
</html>