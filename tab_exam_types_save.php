<?php
@session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['exam_group_id']) || !isset($_POST['exam_acronym']) || !isset($_POST['exam_name_eng']) || !isset($_POST['exam_name_gr']) || !isset($_POST['exam_mes_type']) || !isset($_POST['exam_chart_visible']) || !isset($_POST['exam_suggested_values']) || !isset($_POST['exam_values_explain']) || !isset($_POST['exam_description']) || !isset($_POST['exam_group_sort_order'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();
 




if(isset($_POST['exam_typesFORM_updateID']) && !empty($_POST['exam_typesFORM_updateID']))
{
	$sql = "UPDATE exam_types set exam_group_id=:exam_group_id, exam_acronym=:exam_acronym, exam_name_eng=:exam_name_eng, exam_name_gr=:exam_name_gr, exam_mes_type=:exam_mes_type, exam_chart_visible=:exam_chart_visible, exam_suggested_values=:exam_suggested_values, exam_values_explain=:exam_values_explain, exam_description=:exam_description, exam_group_sort_order=:exam_group_sort_order where exam_type_id=:exam_type_id";
	$stmt = $db->getConnection()->prepare($sql);
	$stmt->bindValue(':exam_type_id' , $_POST['exam_typesFORM_updateID']);
}
else
{
	$sql = "INSERT INTO exam_types (exam_group_id, exam_acronym, exam_name_eng, exam_name_gr, exam_mes_type, exam_chart_visible, exam_suggested_values, exam_values_explain, exam_description, exam_group_sort_order) VALUES (:exam_group_id, :exam_acronym, :exam_name_eng, :exam_name_gr, :exam_mes_type, :exam_chart_visible, :exam_suggested_values, :exam_values_explain, :exam_description, :exam_group_sort_order)";
	$stmt = $db->getConnection()->prepare($sql);
}

$stmt->bindValue(':exam_group_id' , $_POST['exam_group_id']);
$stmt->bindValue(':exam_acronym' , $_POST['exam_acronym']);
$stmt->bindValue(':exam_name_eng' , $_POST['exam_name_eng']);
$stmt->bindValue(':exam_name_gr' , $_POST['exam_name_gr']);
$stmt->bindValue(':exam_mes_type' , $_POST['exam_mes_type']);
$stmt->bindValue(':exam_chart_visible' , $_POST['exam_chart_visible']);
$stmt->bindValue(':exam_suggested_values' , $_POST['exam_suggested_values']);
$stmt->bindValue(':exam_values_explain' , $_POST['exam_values_explain']);
$stmt->bindValue(':exam_description' , $_POST['exam_description']);
$stmt->bindValue(':exam_group_sort_order' , $_POST['exam_group_sort_order']);

$stmt->execute();
 
echo $stmt->errorCode(); 
?>