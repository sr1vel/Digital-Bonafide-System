<?php
session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SREC</title>

    <style>
        /* Your CSS styles */
        * {
            margin: 0;
            padding: 0;
            list-style: none;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
        }
        .align-left {
            text-align: left;
        }
        .login-img img {
            margin-left: 100px;
        }
        .form-login-body .top-menu {
            box-shadow: 0 0 10px 0 #00000026;
            background-color: #092a47;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 10px 0;
        }
        .form-login-body .top-menu .logo img {
            max-width: 400px;
            width: 100%;
            height: auto;
            margin-top: 7px;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #092a47;
        }
        .navbar .navbar-brand:hover {
            color: #092a47;
        }
        .navbar-nav .nav-item .nav-link {
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            color: #333333;
            padding: 0.5rem 1rem;
        }
        .navbar-nav .nav-item .nav-link:hover {
            color: #092a47;
        }
        .navbar-toggler {
            border: none;
            outline: none;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,<svg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'><path stroke='rgba(0, 0, 0, 0.5)' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/></svg>");
        }
        @media (max-width: 992px) {
            .navbar-nav {
                flex-direction: column;
            }
            .navbar-nav .nav-item {
                width: 100%;
            }
            .navbar-nav .nav-item .nav-link {
                text-align: center;
                padding: 1rem;
            }
        }
        @media (max-width: 748px) {
            .form-login-body .top-menu .logo img {
                max-width: 300px;
            }
        }
        @media (min-width: 749px) {
            .form-login-body .top-menu .logo img {
                margin-left: 60px;
                max-width: 400px;
            }
        }
        body {
            font-family: "Roboto", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .subtitle {
            color: #333;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .container {
            max-width: 1000px;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: "Roboto", sans-serif;
            border-radius: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #092a47;
            color: #fff;
        }
        .verified {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .logout {
            position: absolute;
            top: 15px;
            right: 10px;
        }
        .logout button {
            background-color: #ffffff;
            color: rgb(6, 2, 2);
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 25px;
            font-size: 1.0rem;
            font-family: "Roboto", sans-serif;
        }
        .logout button:hover {
            background-color: #092a47 !important;
            border-color: #092a47 !important;
            color: #ffffff;
        }
    </style>
</head>
<body class="form-login-body">
<div class="title">
            <h1>Sri Ramakrishna Engineering College</h1>
        </div>
        <p class="subtitle">Bonafide Tracking Status</p>
    </div>
    <div class="container-fluid">
        <div class="top-menu">
            <div class="row">
                <div class="col-md-3 col-12 logo">
                    <img src="footer-logo.png" alt="Srec Logo" class="navbar-logo">
                </div>
                <div class="logout">
                    <button onclick="goBack()">Back</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php
        // Database connection details
        $db_host = "localhost";
        $db_user = "root";
        $db_pass = "";
        $db_name = "hackathon";

        // Create connection
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Fetch data from the verification table for the logged-in user
        $username = $_SESSION['username'];
        $sql = "SELECT * FROM verfication WHERE rollnumber = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
         
            echo "<th>Tutor</th>";
            echo "<th>Academic Coordinator</th>";
            echo "<th>Head of Department (HOD)</th>";
            echo "<th>Hostel Tutor</th>";
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
              
                echo "<td>" . htmlspecialchars($row['Tutor']) . "</td>";
                echo "<td>" . htmlspecialchars($row['AcademicCoordinator']) . "</td>";
                echo "<td>" . htmlspecialchars($row['HeadOfDepartment']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Hosteltutor']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "No verification data found for the user.";
        }

        // Close the database connection
        $conn->close();
        ?>
        
       
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
