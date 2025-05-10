<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>SREC</title>
  <link rel="stylesheet" href="hhh.css" />
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
      background-color: #ffffff;
      /* Blue hover color */
      border-color: #ffffff;
      /* Matching border color */
      color: #000000;
      /* Text color */
      padding: 8px 16px;
      /* Adjust padding as needed */
      border-radius: 20px;
      /* Adjust border radius as needed */
      font-family: "Poppins";
      cursor: pointer;
      transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
    }

    .logout button:hover {
      background-color: #206aab !important;
      /* Darker red hover color */
      border-color: #206aab !important;
      /* Matching hover border color */
      color: #ffffff;
    }
    .guidecontainer{
      max-width: 700px;
			background: #fff;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 6px 10px rgba(0, 0, 0, 0.4);
            margin-top: -2px;
            margin-bottom: 10px;
    }
    p{
            font-size: 18px;
            font-family: 'Times New Roman';
                  }
h1{
  font-size: 18px;
            font-family: 'Times New Roman';
            text-align: center;
}
  </style>
</head>

<body class="form-login-body">
  <div class="container-fluid">
    <div class="top-menu">
      <div class="container">
        <div class="row">
          <div class="col-md-3 col-12 logo">
            <img src="footer-logo.png" alt="Srec Logo" class="navbar-logo">
          </div>
          <div class="logout">
            <button onclick="logout()">Logout</button>
          </div>
        </div>
      </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <?php
    session_start(); // Start the session
    
    // Check if the username is set in the session
    if (isset($_SESSION['username'])) {
      // Display the username
      echo "Welcome!!! " . $_SESSION['username'];
    } else {
      // If username is not set, display a message
      echo "Username is not set in the session.";
    }
    ?>
    <!-- Card Container -->
    <div class="card-container">
      <div class="card">
        <div class="text">
          <h2 data-splitting="">UG Applicants</h2>
          <button onclick="window.location.href ='hod1.php';">View</button>
        </div>
      </div>
      <div class="card">
        <div class="text">
          <h2 data-splitting="">PG Applicants</h2>
          <button onclick="window.location.href ='hodpg.php';">View</button>
        </div>
      </div>
      <div class="card">
        <div class="text">
          <h2 data-splitting="">Passedout Applicants</h2>
          <button onclick="window.location.href ='hodpassed.php';">View</button>
        </div>
      </div>
    <div class="guidecontainer">
      <h1><b>GUIDELINES</b></h1><br>
<p>1.HOD should approve ONLY WHEN THE TUTORS AND AC APPROVES.</p>
<p>2.The ug students should have all the verifications.</p>
<p>3.The PG students will the hod verification only so the id proofs are to be checked wisely.</p>
<p>4.The passed students approval also must be checked with the proves submitted and the verify the students.</p>
    </div>
   <script>
    function goBack() {
      window.history.back();
    }
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
            window.location.href = 'hodlogin.php'; // Redirect to login page
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