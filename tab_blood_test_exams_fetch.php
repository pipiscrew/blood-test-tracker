<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['blood_test_exam_id'])) {
	echo json_encode(null);
	exit ;
}


try {
	require_once ('general.php');
	
	$db = new dbase();
	$db->connect_sqlite();

	$r= $db->getRow("SELECT blood_test_exam_id, blood_test_id, exam_type_id, exam_value, comments FROM blood_test_exams where blood_test_exam_id=?", array($_POST['blood_test_exam_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>