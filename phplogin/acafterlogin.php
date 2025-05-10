



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SREC</title>
  <link rel="stylesheet" href="s2p2.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    .logout {
      position: absolute;
      top: 25px;
      right: 15px;
    }
    .logout button {
      background-color: #ffffff; /* Blue hover color */
      border-color: #ffffff; /* Matching border color */
      color: #000000;/* Text color */
      padding: 8px 16px; /* Adjust padding as needed */
      border-radius: 20px; /* Adjust border radius as needed */
      font-family: "Poppins";
      cursor: pointer;
      transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
    }
    .logout button:hover {
      background-color: #e20d0d !important; /* Darker red hover color */
      border-color: #e20d0d !important; /* Matching hover border color */
      color:#ffffff
    }
  </style>
</head>
<body
   class="form-login-body">
    <div class="container-fluid">
        <div class="top-menu">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-12 logo">
                            <img src="footer-logo.png" alt="Srec Logo" class="navbar-logo">
                        </a>
                    </div>
                    <div class="logout">
                        <button onclick="logout()">Logout</button>
                      </div>
                </div>
            </div>
        </div>
        <!-- Add some content here to show the scroll effect -->
        <div style="height: 2000px;"></div>
    </div>
</div>
<br>
<br>
<br>
<br>
<br>
<?php
session_start(); // Start the session

// Check if the username is set in the session
if(isset($_SESSION['username'])) {
    // Display the username
    echo "Username: " . $_SESSION['username'];
} else {
    // If username is not set, display a message
    echo "Username is not set in the session.";
}
?>

  <div class="card">
    <div class="text">
      <h2 data-splitting=""> View Applicants</h2>
      <button onclick="window.location.href ='ac1.php';">View</button>
    </div>
  </div>

  <script>
function logout() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "logout.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Check if the session is destroyed
      if (xhr.responseText.trim() === 'success') {
        alert('Session destroyed successfully');
        // Redirect to another page
        window.location.href = 'aclogin.php'; // Redirect to login page
      } else {
        alert('Failed to destroy session');
      }
    }
  };
  xhr.send();
}
</script>
</body>
</html>
