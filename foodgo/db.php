<?php
// Read connection settings from environment variables in Docker/CI environments.
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$database = getenv('DB_NAME') ?: 'foodgo';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    error_log('FoodGo database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Database connection unavailable. Please try again later.');
}
$conn->set_charset('utf8mb4');
?>
