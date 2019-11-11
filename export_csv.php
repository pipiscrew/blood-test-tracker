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
        $types[] = array('id' => $exam['exam_type_id'],
        'acronym' =>  $exam['exam_acronym'],
        'name_eng' =>  $exam['exam_name_eng'],
        'name_gr' =>  $exam['exam_name_gr'],
        'mes_type' =>  $exam['exam_mes_type'],
        'suggested_values' =>  $exam['exam_suggested_values'],
         'description' =>  trim(preg_replace('/\s+/', ' ', $exam['exam_description']))
    );
        
    }  
    
    $record->exam_types = $types;
}
//hang foreach exam_group the exam_types



$fp = fopen('php://temp', 'w+');

//for each group
foreach($records as $record)
{
    //construct header [start]
    if (!isset($row)) {
        $row = null;
        $row[0] = '';
        $row[1] = '';
        $row[2] = 'RECOMMENDED';
        //for each blood_test selected
        foreach($blood_test_ids_arr as $blood_test_id){
            $res = $db->getRow('select blood_id, blood_date from blood_tests where blood_id =' . $blood_test_id, null);
            $row["x".$res['blood_id']] = $res['blood_date'];
        }
        fputcsv($fp, $row);
    }
    //construct header [end]

    //print exam_group
    $row = null;
    $row['grp_descr'] = $record->grp_name;
    fputcsv($fp, $row);
    $row = null;

    //for exam
    foreach($record->exam_types as $exam_type) {
        $row['descr'] = $exam_type['acronym'] . ' (' . $exam_type['name_eng'] . ')';
        $row['descr_gr'] = $exam_type['name_gr'];
        $row['suggested'] = $exam_type['suggested_values'] . ' ' . $exam_type['mes_type'];

        //for each blood_test selected
        foreach($blood_test_ids_arr as $blood_test_id){
            $res = $db->getScalar('select exam_value from blood_test_exams
                                where blood_test_exams.exam_type_id = ' . $exam_type['id'] . ' and blood_test_exams.blood_test_id =' . $blood_test_id, null);
            $row["test$blood_test_id"] = $res;
        }
        
        $row["descr$blood_test_id"] = $exam_type['description'];

            // Add row to CSV buffer
             fputcsv($fp, $row);
    }
}

rewind($fp); // Set the pointer back to the start
$csv_contents = stream_get_contents($fp); // Fetch the contents of our CSV
fclose($fp); // Close our pointer and free up memory and /tmp space

$filename ="ingame.csv";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
echo $csv_contents;
