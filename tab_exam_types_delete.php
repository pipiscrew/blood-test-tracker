<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['exam_type_id'])) {
	echo json_encode(null);
	exit ;
}

require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();


$sql = "DELETE FROM `exam_types` WHERE exam_type_id=:exam_type_id";
$sth = $db->getConnection()->prepare($sql);
$sth->bindValue(':exam_type_id', $_POST['exam_type_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>