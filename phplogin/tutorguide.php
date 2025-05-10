<?php
 session_start();
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digitalized Bonafide System</title>
    <link rel="icon" type="image/png" sizes="32x32" href="favicon.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
    body {
            background-color: white; /* Set body background to white */
            overflow-x: hidden;
        }
        .navbar {
            background-color: #092a47; /* Set navbar background to blue */
            width: 100%;
            padding:20px; /* Ensure navbar covers full width */
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
            width: 100px;/* Smooth transition for hover effect */
        }
        .back-button:hover {
            background-color:#206aab;
            color:#f8f8f8; /* Darker blue on hover */
        }
        .container {
			max-width: 700px;
			background: #fff;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 6px 10px rgba(0, 0, 0, 0.4);
            margin-top: -2px;
            margin-bottom: 10px;
		}
        
		.btn-custom {
			background-color: #092a47;
			color: white;
			padding: 10px 20px;
			border: none;
			cursor: pointer;
			font-size: 0.9rem;
			border-radius: 9px;
			margin-top: 10px;
		}


		.btn-custom:hover {
			background-color: #0065C3;
            color:#f8f8f8;
		}
        p{
            font-size: 18px;
            font-family: 'Times New Roman';
        }
        .welcome-message {
            text-align: center; /* Center text */
            color: #343a40; /* Dark text color */
            font: 1.5em sans-serif;
            margin-top:-10px;
        }

        @media (max-width: 767px) {
            .navbar {
            background-color: #092a47; /* Set navbar background to blue */
            width: 100%; /* Ensure navbar covers full width */
        }
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
                margin-left: 500px; /* Reset left margin */
                width: 20%;
                background-color: #092a47; /* Blue background color */
                color: white; /* Make button full width on small screens */
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
  <button type="button" class="btn back-button" onclick="window.location.href='tutorafterlogin.php'">Back</button> <!-- Back button inside navbar -->
</div>
<div class="welcome-message">
<?php // Start the session
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
		<div class="container">
		<h1>GUIDELINES</h1>
        <p>1.Tutors must verify all the credentials of the students wisely and then approve.</p>
        <p>2.If the allocated Tutors not verfified the student the alternative tutor can verify them.</p>
        <p>3.Download and verify the id proof of the student and check with details.</p>
		<p>4.The Scholarship avail students cannot apply for the bonafide.</p>
        <p>5.The 7.5 Reservation students cannot apply for the bonafide.</p>
        <p>6.Check with the reasons for the applications for the bonafide certificate</p>
        <p>7.If the allocated Tutors not verfified the student the alternative tutor can verify them.</p>
        <button type="button" class="btn btn-custom" onclick="window.location.href='t.php'">Next</button>
				</div>

</div>
</body>
</html>