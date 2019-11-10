<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['blood_id'])) {
	echo json_encode(null);
	exit ;
}


try {
	require_once ('general.php');
	
	$db = new dbase();
	$db->connect_sqlite();

	$r= $db->getRow("SELECT blood_id, blood_date, blood_where, blood_comments FROM blood_tests where blood_id=?", array($_POST['blood_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>