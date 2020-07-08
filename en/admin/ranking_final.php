<?php

session_start();
include_once 'header.php';
include_once '../../dbcon.php';

echo "<meta charset='utf-8'>";

mysqli_autocommit($db_conx, FALSE);

$research_id = $_SESSION['research_id'];

$sql = "SELECT * from technology where r_id =$research_id order by t_id ASC";
$result = mysqli_query($db_conx, $sql);
$count = 1;
$mycount = 0;

$max = 0;

while ($row = mysqli_fetch_array($result)) {
    $sum = 0;
    $countCriteria = 1;
    $sqlCritirio = "SELECT * from criteria where r_id =$research_id order by criterion_id ASC";
    $resultCritirio = mysqli_query($db_conx, $sqlCritirio);
    while ($rowCritirio = mysqli_fetch_array($resultCritirio)) {
        $countFactor = 1;
        $sqlFactor = "SELECT * from sub_criteria where r_id =$research_id and c_id={$rowCritirio['criterion_id']} order by sub_criteria_id ASC";
        $resultFactor = mysqli_query($db_conx, $sqlFactor);
        while ($rowFactor = mysqli_fetch_array($resultFactor)) {

            $sqlWeights_technology = "SELECT * from avg_weight_technology where c_id={$rowFactor['sub_criteria_id']}";
            $resultWeights_technology = mysqli_query($db_conx, $sqlWeights_technology);
            if ($rowWeights_technology = mysqli_fetch_array($resultWeights_technology)) {

                $weightsTechnology = explode("|", $rowWeights_technology['weight']);
                $sqlQuest_criteria = "SELECT * from quest_criteria where c_id={$rowWeights_technology['c_id']}";
                $resultQuest_criteria = mysqli_query($db_conx, $sqlQuest_criteria);
                if ($rowQuest_criteria = mysqli_fetch_array($resultQuest_criteria)) {

                    $sqlWeights = "SELECT * from avg_weight where q_id={$rowQuest_criteria['q_id']}";
                    $resultWeights = mysqli_query($db_conx, $sqlWeights);
                    if ($rowWeights = mysqli_fetch_array($resultWeights)) {

                        $weights = explode("|", $rowWeights['weight']);
                        $sqlQuest_criteria2 = "SELECT * from quest_criteria where c_id={$rowCritirio['criterion_id']}";
                        $resultQuest_criteria2 = mysqli_query($db_conx, $sqlQuest_criteria2);
                        if ($rowQuest_criteria2 = mysqli_fetch_array($resultQuest_criteria2)) {

                            $sqlWeights2 = "SELECT * from avg_weight where q_id={$rowQuest_criteria2['q_id']}";
                            $resultWeights2 = mysqli_query($db_conx, $sqlWeights2);
                            if ($rowWeights2 = mysqli_fetch_array($resultWeights2)) {

                                $weights2 = explode("|", $rowWeights2['weight']);
                                $sum = $sum + ($weightsTechnology[$count] * $weights[$countFactor] * $weights2[$countCriteria]);
                            }
                        }
                    }
                }
            }
            $countFactor++;
        }
        $countCriteria++;
    }
    $count++;
    echo "<h3>Final Ranking {{$row['t_name']}}: $sum </h3>";
    if($max < $sum){
        $max = $sum;
        $name = $row['t_name'];
    }
    
    $insert = "INSERT INTO ranking_final VALUES ($research_id,{$row['t_id']},$sum);";
    $res=mysqli_query($db_conx, $insert);
}

echo "<h2>Preferred Technology: $name = $max </h2>";


mysqli_commit($db_conx);
mysqli_close($db_conx);
$message = "Technology rankings have been calculated.";
echo $message;

echo "<br/><a href='export_excel.php' style='float:right; margin-right:50px;' class='button icon arrowright'>Export</a><br/><br/>";

include_once 'footer.php';
?>