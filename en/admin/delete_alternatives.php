<?php

include_once "../../dbcon.php";
session_start();
mysqli_autocommit($db_conx, FALSE);

$criteria=$_GET['criteria_id'];

$sql = "DELETE FROM technology WHERE t_id = $criteria";
if (!mysqli_query($db_conx,$sql)) {
    mysqli_rollback($db_conx);
    $_SESSION['error']='all ok';
    echo "<meta charset='utf-8'>";
    $message = "Η διαγραφή κριτηρίων απέτυχε. Error: 1";
    echo "<script type='text/javascript'>alert('$message'); history.go(-1);</script>";
    die('Error: ' . mysqli_error($db_conx));
}


$sql = "DELETE FROM quest_alternatives where t_id1 = $criteria";
if (!mysqli_query($db_conx,$sql)) {
    mysqli_rollback($db_conx);
    $_SESSION['error']='all ok';
    echo "<meta charset='utf-8'>";
    $message = "Η διαγραφή υποκριτηρίων απέτυχε. Error: 3";
    echo "<script type='text/javascript'>alert('$message'); history.go(-1);;</script>";
    die('Error: ' . mysqli_error($db_conx));
}

mysqli_commit($db_conx);
mysqli_close($db_conx);
echo "<meta charset='utf-8'>";
$message = "Διαγραφή επιτυχής";
echo "<script type='text/javascript'>alert('$message'); history.go(-1);</script>";
?>