<?php

//Step1: get information from the form
$task = $_POST['task'];
$owner = $_POST['owner'];
$timeline = $_POST['timeline'];
$status = $_POST['status'];
$tracking = $_POST['tracking'];
$progress = $_POST['progress'];

//Step2: connect to database
include ("db_connect.php");

//Step3: Create the SQL statement
$sql = "INSERT INTO tasks (task, owner, timeline, status, tracking, progress) VALUES ('$task', '$owner', '$timeline', '$status', '$tracking', '$progress')";

//For debug
//echo $sql;

//step4: Execute the SQL statement
mysqli_query($dbconn, $sql);

//Step5: Check the query executed or not
if(mysqli_affected_rows($dbconn) <=0){
    die("<script>alert('Error: Cannot insert data'); window.location.href='Task.php';</script>");
}

echo "<script>alert('Successfully insert data'); window.location.href='Task.php';</script>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Task</title>
</head>
<body>
    <h2>Add a New Task</h2>
    <form method="POST" action="">
        <label>Task:</label><input type="text" name="task" required><br>
        <label>Owner:</label><input type="text" name="owner" required><br>
        <label>Timeline:</label><input type="text" name="timeline" required><br>
        <label>Status:</label><input type="text" name="status" required><br>
        <label>Tracking:</label><input type="text" name="tracking" required><br>
        <button type="submit">Add Task</button>
    </form>

</body>
</html>