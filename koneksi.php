<?php 
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_kp";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");
?>