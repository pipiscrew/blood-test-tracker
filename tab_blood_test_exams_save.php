<?php
@session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['blood_test_id']) || !isset($_POST['exam_type_id']) || !isset($_POST['exam_value']) || !isset($_POST['comments'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();
 




if(isset($_POST['blood_test_examsFORM_updateID']) && !empty($_POST['blood_test_examsFORM_updateID']))
{
	$sql = "UPDATE blood_test_exams set blood_test_id=:blood_test_id, exam_type_id=:exam_type_id, exam_value=:exam_value, comments=:comments where blood_test_exam_id=:blood_test_exam_id";
	$stmt = $db->getConnection()->prepare($sql);
	$stmt->bindValue(':blood_test_exam_id' , $_POST['blood_test_examsFORM_updateID']);
}
else
{
	//check with the same exam type exist for the current blood test
	$d = $db->getScalar("select count(blood_test_exam_id) from blood_test_exams where blood_test_id=? and exam_type_id=?", array($_POST['blood_test_id'], $_POST['exam_type_id'] ));
	if ($d>0) {
		echo "Duplicate examination type";
		exit;
	}

	$sql = "INSERT INTO blood_test_exams (blood_test_id, exam_type_id, exam_value, comments) VALUES (:blood_test_id, :exam_type_id, :exam_value, :comments)";
	$stmt = $db->getConnection()->prepare($sql);
}

$stmt->bindValue(':blood_test_id' , $_POST['blood_test_id']);
$stmt->bindValue(':exam_type_id' , $_POST['exam_type_id']);
$stmt->bindValue(':exam_value' , $_POST['exam_value']);
$stmt->bindValue(':comments' , $_POST['comments']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>