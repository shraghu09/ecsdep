<?php


/**
 * Include the aws php sdk
 * Follow the below instructions for deep dive
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html
 */
require 'vendor/aws-autoloader.php';

/**
 * Import SecretsManagerClient and AwsException to be used
 */
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

/**
 * Create a SecretsManagerClient object
 */
$client = new SecretsManagerClient([
    'region' => $_ENV["AWS_REGION"],
    'version' => 'latest',
]);


$secret_name = $_ENV["SECRET_NAME"];
$db_server = $_ENV["DB_SERVER"];
$database_name = $_ENV["DB_DATABASE"];


/**
 * Get the Secret value from AWS Secret Manager
 * Note: Ensure proper IAM role is attached to ECS Task
 */
$result = $client->getSecretValue([
    'SecretId' => $secret_name,
]);


/**
 * result contains a few key-value about that secret.
 * The key named SecretString has the username and password encoded as a plain json string
 * Decode the json using json_decode function to get username and password
 */
$myJSON = json_decode($result['SecretString']);

$username = $myJSON->username;
$password = $myJSON->password;
$host = $db_server;
$database = $database_name;

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    echo "error in connection";
} else {
    $table = mysqli_real_escape_string($connection, "beverage_voting");

    $checktable = mysqli_query($connection, "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$table' AND TABLE_SCHEMA = '$database'");

    if (mysqli_num_rows($checktable) > 0) {
        
    } else {
        $query = "CREATE TABLE `beverage_voting` (
            `beverage_voting_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(50) NOT NULL,
            `email` varchar(50) NOT NULL,
            `beverage` varchar(50) NOT NULL,
            `created_on` datetime NOT NULL DEFAULT current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

        if ($connection->query($query) === TRUE) {
            
        } else {
            echo json_encode(array('message' => 'Error on submit data', 'status' => 'error', 'sql_error' => mysqli_error($connection)));
//            echo "Error creating table: " . $connection->error;
            die();
        }
    }
}
?>
