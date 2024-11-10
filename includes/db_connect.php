<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookexchangecentral";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
