<?php
session_start();

// Check if session exists
if (isset($_SESSION['username'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    if (session_destroy()) {
        echo 'success'; // Indicate success
        exit;
    } else {
        echo 'error'; // Indicate failure
        exit;
    }
} else {
    echo 'error'; // Indicate failure
    exit;
}
?>
