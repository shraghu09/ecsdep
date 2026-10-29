<?php

include('db_config.php');

$name = htmlentities($_REQUEST['name']);
$email = htmlentities($_REQUEST['email']);
$beverage = htmlentities($_REQUEST['beverage']);

if ($name === '' || $email === '' || $beverage === '') {
    echo json_encode(array('message' => 'Please fill all fields', 'status' => 'error'));
    die();
} else {
    $query = "INSERT INTO `beverage_voting` (name, email, beverage) VALUES ('$name', '$email', '$beverage');";
    if (!mysqli_query($connection, $query)) {
        echo json_encode(array('message' => 'Error on submit data', 'status' => 'error', 'sql_error' => mysqli_error($connection)));
        die();
    } else {
        echo json_encode(array('message' => 'Thank you for voting!', 'status' => 'success'));
        die();
    }
}