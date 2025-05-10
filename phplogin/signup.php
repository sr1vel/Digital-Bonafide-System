<?php
include("sconn.php");

$signup_error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            // Check if the email already exists
            $check_query = "SELECT * FROM login WHERE username='$username'";
            $check_result = $conn->query($check_query);

            if ($check_result && $check_result->num_rows == 0) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert new user into the login table
                $insert_query = "INSERT INTO login (username, password, password_updated) VALUES ('$username', '$hashed_password', 0)";
                if ($conn->query($insert_query) === TRUE) {
                    header("Location: front.php"); // Redirect to login page
                    exit;
                } else {
                    $signup_error_message = "Error: " . $conn->error;
                }
            } else {
                $signup_error_message = "Email already exists.";
            }
        } else {
            $signup_error_message = "Passwords do not match.";
        }
    } else {
        $signup_error_message = "All fields are required.";
    }
}

if (isset($conn) && $conn->ping()) {
    $conn->close();
}
?>
