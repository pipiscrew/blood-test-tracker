<?php
@session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}


// include your code to connect to DB.
require ('general.php');

$table_columns = array(
'exam_type_id',
'exam_group_id',
'exam_acronym',
'exam_name_eng',
'exam_name_gr',
'exam_chart_visible',
'exam_suggested_values',
'exam_values_explain',
'exam_description',
'exam_group_sort_order',

);


if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$conn = new dbase();
$conn->connect_sqlite();



$limit = $_GET["limit"];
$offset= $_GET["offset"];



$sql="select exam_type_id, exam_groups.examgrp_name as exam_group_id, exam_acronym, exam_name_eng, exam_name_gr, exam_mes_type, exam_chart_visible, exam_suggested_values, exam_group_sort_order from exam_types 
LEFT JOIN exam_groups ON exam_groups.examgrp_id = exam_types.exam_group_id";
$count_query_sql = "select count(*) from exam_types";



//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED
if (isset($_GET["search"]) && !empty($_GET["search"]))
{
	$like_str = " or #field# like :searchTerm";
	$where = " 0=1 ";

	foreach($table_columns as $col)
	{
		$where.= str_replace("#field#",$col, $like_str);
	}

	$sql.= " where ". $where;
	$count_query_sql.= " where ". $where;
}

//////////////////////////////////////WHEN SORT COLUMN NAME SPECIFIED
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$name= $_GET["name"];
	$order= $_GET["order"];

//bug on sqlite, when trying to use bind on orderby
	if (strpos($name, " ")==0 && strpos($name, "'")==0)
		$sql.= " order by $name $order ";
}


//////////////////////////////////////PREPARE
$stmt = $conn->getConnection()->prepare($sql." LIMIT :limit OFFSET :offset");

//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED *BIND*
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$stmt->bindValue(':searchTerm', '%'.$_GET["search"].'%');

//////////////////////////////////////PAGINATION SETTINGS
$stmt->bindValue(':offset' , intval($offset), PDO::PARAM_INT);
$stmt->bindValue(':limit' , intval($limit), PDO::PARAM_INT);

	
//////////////////////////////////////FETCH ROWS
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


//////////////////////////////////////COUNT TOTAL 
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$count_recs = $conn->getScalar($count_query_sql, array(':searchTerm' => '%'.$_GET["search"].'%'));
else
	$count_recs = $conn->getScalar($count_query_sql, null);

//////////////////////////////////////JSON ENCODE
$arr = array('total'=> $count_recs,'rows' => $rows);

header("Content-Type: application/json", true);

echo json_encode($arr);

?>