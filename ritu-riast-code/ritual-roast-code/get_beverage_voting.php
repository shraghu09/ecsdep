<?php

include('db_config.php');

$query = "SELECT * FROM `beverage_voting`
ORDER BY `beverage_voting_id` DESC
LIMIT 10";
$query_run = mysqli_query($connection, $query);
$result_array = [];

if (mysqli_num_rows($query_run) > 0) {
    foreach ($query_run as $row) {
        array_push($result_array, $row);
    }
    header('Content-type: application/json');
    echo json_encode($result_array);
} else {
    echo json_encode($result_array);
//    echo $return = "No Record Found";
}
