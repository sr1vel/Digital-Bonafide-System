<?php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "hackathon";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve roll number from the form submission
if (isset($_POST['rollnumber'])) {
    $rollnumber = $_POST['rollnumber'];

    // Fetch student details from the database based on roll number
    $sql = "SELECT * FROM pgadmin WHERE rollnumber = '$rollnumber'";
    $result = $conn->query($sql);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $rollnumber = $_POST['rollnumber'];
    $year = $_POST['year'];
    $hd = $_POST['hd'];

    // Determine the file path based on year, residential status, and adtype
    if ($year == 'First Year') {
        if ($hd == 'Day Scholar') {
            include_once('pg1.php');
        } else {
            include_once('pghos1.php');
        }
    } elseif ($year == 'Second Year') {
        if ($hd == 'Day Scholar') {
            include_once('pgday2.php');
        } else {
            include_once('pghos2.php');
        }
    }  else {
        // Invalid year
        echo "Invalid year";
    }
}    
$conn->close();
?>