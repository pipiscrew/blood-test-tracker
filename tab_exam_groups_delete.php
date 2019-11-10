<?php
@session_start();

if (!isset($_SESSION["id"]) || empty($_POST['examgrp_id'])) {
	echo json_encode(null);
	exit ;
}


require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();

$sql = "DELETE FROM `exam_groups` WHERE examgrp_id=:examgrp_id";
$sth = $db->getConnection()->prepare($sql);
$sth->bindValue(':examgrp_id', $_POST['examgrp_id']);
	
$sth->execute();

echo $sth->errorCode(); 
?>