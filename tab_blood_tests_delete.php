<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['blood_id'])) {
	echo json_encode(null);
	exit ;
}


require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();

$sql = "DELETE FROM `blood_tests` WHERE blood_id=:blood_id";
$sth = $db->getConnection()->prepare($sql);
$sth->bindValue(':blood_id', $_POST['blood_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>