<?php
@session_start();

if (!isset($_SESSION["id"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_GET['blood_test_id'])){
	echo "error010101010";
	return;
}
 
$blood_test_id = intval($_GET['blood_test_id']);

if ($blood_test_id == 0 ){
	echo "error020101010";
	return;
}

//DB
require_once ('general.php');

$db = new dbase();
$db->connect_sqlite();

$validation = $db->getScalar("select count(blood_test_exam_id) from blood_test_exams where blood_test_id=?", array($blood_test_id) );

if ($validation > 0)
{
     echo "For this blood test entries exist.";
     exit;
}

?>


<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>
			PipisCrew
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="assets/bootstrap.min.css">
    </head>
<body>

<?php

$blood_test = $db->getRow('select blood_date, blood_where from blood_tests where blood_id=?',array($blood_test_id));

if (!$blood_test)
{
    echo "Blood test doesnt exist.";
    exit;
}
?>



<div class="container">
<div class="page-header">
  <h1><?= $blood_test['blood_where']?> <small><?= $blood_test['blood_date']?></small></h1>
</div>
<form onsubmit="return validations()" method="post" action="tab_blood_test_exams_mass_save.php">
<?php
//
$item = <<<EOT
<div class='form-group'>
<label>{{exam_type_name}} :</label>
<input name='{{exam_type_id}}' class='form-control' placeholder='examination value'>
</div>
EOT;


//select 
$sql = "select * from exam_types
left join exam_groups on exam_groups.examgrp_id = exam_types.exam_group_id
order by exam_group_id,exam_type_id";

$exam_grps = $db->getSet($sql, null);

$prev_groupid=0;
foreach($exam_grps as $row)
{
    if ($prev_groupid!=$row['exam_group_id']) //add group
    {   
        if ($prev_groupid!=0)
            echo "</div></div>";

        echo '<div class="panel panel-primary"><div class="panel-heading">  <h3 class="panel-title">'.$row['examgrp_name'].'</h3> </div><div class="panel-body">';
    }

    $searchReplaceArray = array(
        '{{exam_type_name}}' => $row['exam_acronym'] . ' (' .  $row['exam_name_eng'] . ' / ' . $row['exam_name_gr'] . ')' . $row['exam_mes_type'] , 
        '{{exam_type_id}}' => $row['exam_type_id']
        );

    echo str_replace(
        array_keys($searchReplaceArray), 
        array_values($searchReplaceArray), 
        $item
        );



     $prev_groupid=$row['exam_group_id'];
    
        
}

echo "</div></div>"; //last group
?>

    <input name="blood_test_id" id="blood_test_id" value="<?=$blood_test_id ?>" style="display:none;">
    <button  class="btn btn-primary" type="submit" name="submit">save</button>
</form>
</div>  <!-- container -->


<script>
    function validatedecimal(s) {
        var rgx = /^[0-9]*\.?[0-9]*$/;
        return s.match(rgx);
    }

    function validations(){
        var inp = list = document.getElementsByTagName("input");

        for (i = 0; i < inp.length; i++) {
            if (!validatedecimal(inp[i].value))
            {   
                alert("Value : " + inp[i].value + "\r\nis not valid. Use dot versous comma");
                return false;
            }
        }
    }
</script>