<?php
// Database configuration
$db_host = "localhost";
$db_user = "root"; // Change to your database username
$db_pass = ""; // Change to your database password
$db_name = "health_tracker";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

