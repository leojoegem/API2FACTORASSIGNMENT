<?php
$servername = "localhost:3308";
$username = "root";
$password = "";
$dbname = "two_step_verification";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
