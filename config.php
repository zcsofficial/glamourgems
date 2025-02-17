<?php
$host = "localhost";
$user = "adnan";
$password = "Adnan@66202";
$database = "glamourgems";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
