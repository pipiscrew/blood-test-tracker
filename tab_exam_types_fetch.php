<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['exam_type_id'])) {
	echo json_encode(null);
	exit ;
}


try {
	require_once ('general.php');
	
	$db = new dbase();
	$db->connect_sqlite();

	$r= $db->getRow("SELECT exam_type_id, exam_group_id, exam_acronym, exam_name_eng, exam_name_gr, exam_mes_type, exam_chart_visible, exam_suggested_values, exam_values_explain, exam_description, exam_group_sort_order FROM exam_types where exam_type_id=?", array($_POST['exam_type_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>