<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "bookexchange_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'header.php'; ?>
<?php include 'footer.php'; ?>
