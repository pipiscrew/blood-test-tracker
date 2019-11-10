<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['examgrp_id'])) {
	echo json_encode(null);
	exit ;
}


try {
	require_once ('general.php');

	$db = new dbase();
	$db->connect_sqlite();
	

	$r= $db->getRow("SELECT examgrp_id, examgrp_name FROM exam_groups where examgrp_id=?", array($_POST['examgrp_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>