<?php

include_once "../../dbcon.php";
session_start();
mysqli_autocommit($db_conx, FALSE);
$research_id = $_SESSION['research_id'];
$c_id = $_SESSION['c_id'];

//check if fields are filled in
$count = 1;
while (isset($_POST['textbox' . $count])) {

    if ($_POST['textbox' . $count] == '') {
        $message = "Enter name for factor #$count";
        echo "<script type='text/javascript'>alert('$message'); history.go(-1);</script>";
        return false;
        echo "<script>e.preventDefault();</script>";
    }

    if ($_POST['description' . $count] == '') {
        $message = "Enter description for factor #$count";
        echo "<script type='text/javascript'>alert('$message'); history.go(-1);</script>";
        return false;
        echo "<script>e.preventDefault();</script>";
    }

    $factor_name = rtrim($_POST['textbox'.$count]);
    $factor_description = rtrim($_POST['description'.$count]);

    $sql = "INSERT INTO sub_criteria VALUES (0,$c_id,'$factor_name',$research_id,'$factor_description')";
    $result = mysqli_query($db_conx, $sql) or trigger_error("Query Failed! SQL: $sql - Error: ".mysqli_error($db_conx), E_USER_ERROR);

    $count++;

    $sql1 = "UPDATE criteria set sub_criteria=2 where criterion_id=$c_id";
    $result = mysqli_query($db_conx, $sql1) or trigger_error("Query Failed! SQL: $sql1 - Error: ".mysqli_error($db_conx), E_USER_ERROR);

}

//insert into database
$date2 = date('Y/m/d H:i:s', strtotime('+1 hours'));
$description1 = "Please rate the factors, one compared to the other";

$query = "SELECT * from criteria where criterion_id=$c_id";
$result = mysqli_query($db_conx, $query);
$row = mysqli_fetch_array($result);


$query1 = "INSERT INTO quest VALUES (0,$research_id, 'Factor Pairwise Comparison of ".$row['c_name']."', '$date2','$description1',$c_id,1,1,0)";
$result = mysqli_query($db_conx, $query1) or trigger_error("Query Failed! SQL: $query1 - Error: ".mysqli_error($db_conx), E_USER_ERROR);

$quest_id = mysqli_insert_id($db_conx);

$query2 = "SELECT * FROM sub_criteria where c_id=$c_id";
$result1 = mysqli_query($db_conx, $query2);
while ($row = mysqli_fetch_array($result1)) {
    $sql2 = "INSERT INTO quest_criteria VALUES ( $quest_id,".$row['sub_criteria_id'].",$research_id, 1)";
    $result2 = mysqli_query($db_conx, $sql2) or trigger_error("Query Failed! SQL: $sql2 - Error: ".mysqli_error($db_conx), E_USER_ERROR);
}

$sql = "SELECT * from quest_criteria where q_id=$quest_id order by c_id ASC";
$sql2 = "SELECT * from quest_criteria where q_id=$quest_id order by c_id ASC";
$result3 = mysqli_query($db_conx, $sql);

while ($row1 = mysqli_fetch_array($result1)) {
    $result3 = mysqli_query($db_conx, $sql2);
    while ($row2 = mysqli_fetch_array($result3)) {
        if ($row1['c_id'] != $row2['c_id']) {
            $sql3 = "SELECT * from quest1 where c1_id=".$row1['c_id']." and c2_id=".$row2['c_id']." and q_id=$quest_id";
            $result4 = mysqli_query($db_conx, $sql3);
            if (mysqli_num_rows($result4) == 0) {
                $sql4 = "SELECT * from quest1 where c1_id=".$row2['c_id']." and c2_id=".$row1['c_id']." and q_id=$quest_id";
                $result5 = mysqli_query($db_conx, $sql4);
                if (mysqli_num_rows($result5) == 0) {
                    $sql5 = "INSERT into quest1 values ($quest_id,".$row1['c_id'].",".$row2['c_id'].",$research_id,'','',1);";
                    $result = mysqli_query($db_conx, $sql5) or trigger_error("Query Failed! SQL: $sql5 - Error: ".mysqli_error($db_conx), E_USER_ERROR);
                }
            }
        }
    }
}
mysqli_commit($db_conx);
mysqli_close($db_conx);
$message = "Data sent successfully.";
echo "<script type='text/javascript'>alert('$message'); window.location = 'create_factors.php';</script>";
?>