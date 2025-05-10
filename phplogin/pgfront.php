<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digitalized Bonafide System</title>
    <link rel="icon" type="image/png" sizes="32x32" href="favicon.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="stuafter.css"/>
    <style>
    body {
            background-color: white; /* Set body background to white */
            overflow-x: hidden;
        }
        .navbar {
            background-color: #092a47; /* Set navbar background to blue */
            width: 100%; /* Ensure navbar covers full width */
        }
        .navbar-logo {
         max-width: 50%; /* Increase the maximum width to 80% of its container */
            height: auto; /* Maintain aspect ratio */
         max-height: 100px; /* Set a maximum height to keep it from being too large */
}
        .back-button {
            position: relative;
            top: -60px;
            left: 93%;
            z-index: 10;
            background-color: #f8f8f8; /* Blue background color */
            color: rgb(0, 0, 0); /* White text color */
            border: none; /* Remove border */
            padding: 10px 20px; /* Add padding */
            font-size: 16px; /* Font size */
            border-radius: 25px; /* Rounded corners */
            transition: background-color 0.3s;
            margin-right:4px;
            margin-left: -70px; /* Smooth transition for hover effect */
        }
        .back-button:hover {
            background-color: #206aab;
            color:#f8f8f8; /* Darker blue on hover */
        }
        .welcome-message {
            text-align: center; /* Center text */
            color: #343a40; /* Dark text color */
            font: 1.8em sans-serif;
            margin-top: 15px;
        }

        @media (max-width: 767px) {
            .logo {
                text-align: center; /* Center the logo */
                width: 100%; /* Ensure navbar covers full width */
            } 

            .navbar-logo {
                max-width: 100%; /* Allow the logo to take full width */
                max-height: 70px; /* Reduce maximum height for smaller screens */
            }
            .sup {
                text-align: center;
            }
            .back-button {
                position: static; /* Change to static for normal flow */
                margin-top: 10px; /* Add some spacing from the navbar */
                margin-left: 300px; /* Reset left margin */
                width: 20%;
                background-color: #092a47; /* Blue background color */
                color: white;
                /* Make button full width on small screens */
            }
        }
    </style>
</head>
<body class="navbarh">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid"> <!-- Use container-fluid for full width -->
            <div class="d-flex justify-content-between w-100 align-items-center"> <!-- Flexbox for alignment -->
                <div class="logo">
                    <a class="navbar-brand text-white" href="index.html">
                        <img src="footer-logo.png" alt="Srec Logo" class="navbar-logo">
                    </a>
        </div>
    </nav>
  </div>

  <button type="button" class="btn back-button" onclick="window.location.href='slogin.php'">Back</button> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<!-- Back button inside navbar -->
  <button type ="button" class="btn back-button" onclick="logout()">Logout</button>
          </div>
<div class="welcome-message">
<?php
  session_start(); // Start the session
  
  // Check if the username is set in the session
  if (isset($_SESSION['username'])) {
    // Display the username
    echo "Welcome!! " . $_SESSION['username'];
  } else {
    // If username is not set, display a message
    echo "Username is not set in the session.";
  }
  ?>


</div>
</body>
<div class="container">
    <div class="card">
        <div class="text">
            <h2 data-splitting="">Apply Bonafide Certificate</h2>
            <button onclick="window.location.href ='pgguide.php';">Apply Now</button>
        </div>
    </div>

    <div class="card">
        <div class="text">
            <h2 data-splitting="">Track Your Bonafide Certificate Status</h2>
            <button onclick="window.location.href = 'pgretrive.php';">Track Status</button>
        </div>
    </div>
</div>
<script>
    function logout() {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "logout.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // Check if the session is destroyed
          if (xhr.responseText.trim() === 'success') {
            alert('Session destroyed successfully');
            // Redirect to another page
            window.location.href = 'slogin.php'; // Redirect to login page
          } else {
            alert('Failed to destroy session');
          }
        }
      };
      xhr.send();
    }
  </script>

</html>