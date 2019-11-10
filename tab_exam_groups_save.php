<?php
@session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['examgrp_name'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('general.php');
 
$db = new dbase();
$db->connect_sqlite();



if(isset($_POST['exam_groupsFORM_updateID']) && !empty($_POST['exam_groupsFORM_updateID']))
{
	$sql = "UPDATE exam_groups set examgrp_name=:examgrp_name where examgrp_id=:examgrp_id";
	$stmt = $db->getConnection()->prepare($sql);
	$stmt->bindValue(':examgrp_id' , $_POST['exam_groupsFORM_updateID']);
}
else
{
	$sql = "INSERT INTO exam_groups (examgrp_name) VALUES (:examgrp_name)";
	$stmt = $db->getConnection()->prepare($sql);
}

$stmt->bindValue(':examgrp_name' , $_POST['examgrp_name']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>