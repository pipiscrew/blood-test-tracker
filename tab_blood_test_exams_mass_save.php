<?php
@session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['blood_test_id'])){
	echo "error010101010";
	return;
}

$blood_test_id = intval($_POST['blood_test_id']);

if ($blood_test_id == 0 ){
	echo "blood test is not set";
	return;
}

//DB
require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();

// if (isset($_POST['3']))
//     echo "SDf";

// print_r($_POST);

$posted_exams = $_POST;

//construct the SQL
$insert_sql = "INSERT INTO `blood_test_exams` (blood_test_id, exam_type_id, exam_value) VALUES (:blood_test_id, :exam_type_id, :exam_value)";

//prepare the SQL to destination connection
if ($stmt = $db->getConnection()->prepare($insert_sql)){

    foreach($posted_exams as $exam_id => $exam_id_value)
    {
        if (empty($exam_id_value) || $exam_id == 'blood_test_id' || $exam_id == 'submit')
            continue;
        
        $stmt->bindValue(":blood_test_id" , $blood_test_id);
        $stmt->bindValue(":exam_type_id" , $exam_id);
        $stmt->bindValue(":exam_value" , $exam_id_value);

        //execute the prepared statement
        $stmt->execute();    

        if($stmt->errorCode() != "00000"){
            echo $stmt->errorCode();
            exit;
        }
    }

}
