<?php
@session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['blood_date']) || !isset($_POST['blood_where']) || !isset($_POST['blood_comments'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();


if(isset($_POST['blood_testsFORM_updateID']) && !empty($_POST['blood_testsFORM_updateID']))
{
	$sql = "UPDATE blood_tests set blood_date=:blood_date, blood_where=:blood_where, blood_comments=:blood_comments where blood_id=:blood_id";
	$stmt = $db->getConnection()->prepare($sql);
	$stmt->bindValue(':blood_id' , $_POST['blood_testsFORM_updateID']);
}
else
{
	$sql = "INSERT INTO blood_tests (blood_date, blood_where, blood_comments) VALUES (:blood_date, :blood_where, :blood_comments)";
	$stmt = $db->getConnection()->prepare($sql);
}

$stmt->bindValue(':blood_date' , $_POST['blood_date']);
$stmt->bindValue(':blood_where' , $_POST['blood_where']);
$stmt->bindValue(':blood_comments' , $_POST['blood_comments']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>