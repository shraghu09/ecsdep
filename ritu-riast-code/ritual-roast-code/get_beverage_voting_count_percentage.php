<?php

include('db_config.php');

$query = "SELECT count(*) as `beverage_voting_total_count`,
        count(if(`beverage`='Gingerbread Latte', 1, null)) as gingerbread_latte_count,
        count(if(`beverage`='Orange Chocolate Latte', 1, null)) as orange_chocolate_latte_count,
        count(if(`beverage`='Vanilla Spice Latte', 1, null)) as vanilla_spice_latte_count
        from `beverage_voting`;";

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