
<?php

require 'vendor/aws-autoloader.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

try {

    // 1. AWS configuration
    $region = getenv('AWS_REGION') ?: 'us-east-1';
    $secret_name = getenv('SECRET_NAME');

    if (empty($secret_name)) {
        throw new Exception("SECRET_NAME environment variable is missing");
    }

    // 2. Initialize Secrets Manager
    $client = new SecretsManagerClient([
        'region' => $region,
        'version' => 'latest'
    ]);

    // 3. Retrieve current secret
    error_log("Retrieving database credentials from Secrets Manager");

    $result = $client->getSecretValue([
        'SecretId' => $secret_name
    ]);

    if (empty($result['SecretString'])) {
        throw new Exception("SecretString is empty");
    }

    $secret = json_decode($result['SecretString']);

    if (
        json_last_error() !== JSON_ERROR_NONE ||
        empty($secret->username) ||
        empty($secret->password) ||
        empty($secret->host) ||
        empty($secret->dbname)
    ) {
        throw new Exception("Invalid database secret JSON format");
    }

    // 4. Read database configuration from secret
    $host = $secret->host;
    $username = $secret->username;
    $password = $secret->password;
    $database = $secret->dbname;
    $port = isset($secret->port) ? (int)$secret->port : 3306;

    error_log("Database credentials retrieved successfully");

    // 5. Enable MySQL error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // 6. Connect to RDS
    $connection = new mysqli(
        $host,
        $username,
        $password,
        $database,
        $port
    );

    $connection->set_charset("utf8mb4");

    error_log("RDS database connection successful");

    // 7. Create table if it does not exist
    $query = "CREATE TABLE IF NOT EXISTS beverage_voting (
        beverage_voting_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL,
        beverage VARCHAR(50) NOT NULL,
        created_on DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $connection->query($query);

    error_log("Beverage voting table verified successfully");

} catch (AwsException $e) {

    error_log("AWS SECRETS MANAGER ERROR: " . $e->getMessage());

    http_response_code(500);
    echo "Unable to retrieve database credentials";

} catch (mysqli_sql_exception $e) {

    error_log("MYSQL DATABASE ERROR: " . $e->getMessage());

    http_response_code(500);
    echo "Database connection failed";

} catch (Throwable $e) {

    error_log("APPLICATION ERROR: " . $e->getMessage());

    http_response_code(500);
    echo "Application initialization failed";
}
?>
