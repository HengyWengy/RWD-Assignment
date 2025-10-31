<?php
$db_servername = "localhost";
$db_username = "root";      // default phpMyAdmin username
$db_password = "";          // leave blank if you didn't set one
$db_name = "dashboard"; // your actual database name

// Create connection
$dbconn = mysqli_connect($db_servername, $db_username, $db_password, $db_name);

// Check connection
if (!$dbconn) {
    die('<script>alert("connection failed: please check your SQL connection!");</script>');
}

echo "<script>alert('Successfully connect!');</script>";

?>
