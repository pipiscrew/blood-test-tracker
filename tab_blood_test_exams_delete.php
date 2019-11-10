<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['blood_test_exam_id'])) {
	echo json_encode(null);
	exit ;
}


require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();

$sql = "DELETE FROM `blood_test_exams` WHERE blood_test_exam_id=:blood_test_exam_id";
$sth = $db->getConnection()->prepare($sql);
$sth->bindValue(':blood_test_exam_id', $_POST['blood_test_exam_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>