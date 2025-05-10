<?php
include("sconnect.php");
session_start(); // Start the session

$error_message = ""; // Initialize error message variable

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetching form data using the new input field IDs
    $username = isset($_POST['username']) ? $_POST['username'] : ''; // Adjusted to make username optional
    $password = $_POST['password']; // Adjusted to match the new ID

    // Check user credentials and redirect accordingly
    if ($password == 'Srec@123') {
        $_SESSION['username'] = $username;
        header("Location: stuafterlogin.php");
        exit;
    } elseif (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        // Check if user is tutor, academic coordinator, or HOD
        if ($password == 'admin@123') {
            $_SESSION['username'] = $username;
            header("Location: adminafterlogin.php");
            exit;
        } elseif ($password == 'Staff@123') {
            $_SESSION['username'] = $username;
            header("Location: tutorafterlogin.php");
            exit;
        } elseif ($password == 'ac@123') {
            $_SESSION['username'] = $username;
            header("Location: acafterlogin.php");
            exit;
        } elseif ($username === $password) {
            $_SESSION['username'] = $username;
            header("Location: passedfront.php");
            exit;
        } elseif ($password == 'hod@123') {
            $_SESSION['username'] = $username;
            header("Location: hodafterlogin.php");
            exit;
        } elseif ($password === 'Srecpg@123') {
            $_SESSION['username'] = $username;
            header("Location: pgfront.php");
            exit;
        } elseif ($password === 'Hosteltutor@123') {
            $_SESSION['username'] = $username;
            header("Location: hostelafterlogin.php");
            exit;
        }
    }
    // If none of the above conditions match, set the error message
    $error_message = "Invalid username or password. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digitalized Bonafide System</title>
    <link type="image/png" sizes="32x32" rel="icon" href="https://img.icons8.com/ultraviolet/40/imac.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleslog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome CDN -->

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
            transition: background-color 0.3s; /* Smooth transition for hover effect */
        }
        .back-button:hover {
            background-color: #e32525; /* Darker blue on hover */
        }
        .form-control{
            width:400px;
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
                color: white; /* Make button full width on small screens */
            }
        }
        .password-container {
            position: relative;
        }
        .password-container i {
            position: absolute;
            right: 10px;
            top: 72%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body class="navbarh">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid"> <!-- Use container-fluid for full width -->
            <div class="d-flex justify-content-between w-100 align-items-center"> <!-- Flexbox for alignment -->
                <div class="logo">
                    <a class="navbar-brand text-white" href="index.html">
                        <img src="https://srec.ac.in/themes/frontend/images/footer-logo.png" alt="Srec Logo" class="navbar-logo">
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <button type="button" class="btn back-button">Back</button> <!-- Back button inside navbar -->
    <div class="container py-2 h-100 login"> <!-- Changed from py-5 to py-3 -->
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-10">
                <div class="card rounded-3 text-black gui">
                    <div class="row g-1">
                        <div class="col-lg-6">
                            <div class="card-body p-md-5 mx-md-4 text-center">
                                <h4 class="mb-4 tit">Guidelines</h4>
                                <p class="mb-1">Students can apply for bonafide once in an academic year.<br> Passed out students can apply <br> </p>
                            </div>
                        </div>
                        <div class="col-lg-6 d-flex align-items-center justify-content-center gradient-custom-2 guidelines">
                            <div class="px-3 py-4 p-md-5 mx-md-4 text-center">
                                <div class="text-center">
                                    <img src="https://srec.ac.in/uploads/resource/src/WOea5eyA6k30062017092236srec-logo.jpg" style="width: 100px;" alt="logo">
                                    <h4 class="mt-1 mb-4 pb-0">Login</h4>
                                </div>
                                <form method="POST" action="">  
                                    <div data-mdb-input-init class="form-outline mb-3">
                                        <label class="form-label" for="form2Example11">Username</label>
                                        <input type="text" id="username" name="username" class="form-control" placeholder="Rollnumber" />
                                    </div>
                                    <div data-mdb-input-init class="form-outline mb-4 position-relative password-container">
                                        <label class="form-label" for="form2Example22">Password</label>
                                        <input type="password" id="password" name="password" class="form-control" />
                                        <i id="togglePassword" class="fas fa-eye"></i>
                                    </div>
                                    <div class="text-center pt-1 mb-5 pb-1">
                                        <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Log in</button>
                                    </div>
                                    <?php
                                    // Display error message if set
                                    if (!empty($error_message)) {
                                        echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                                    }
                                    ?>
                                </form>
                                <script>
                                    const togglePassword = document.getElementById('togglePassword');
                                    const password = document.getElementById('password');

                                    togglePassword.addEventListener('click', function () {
                                        // Toggle the type attribute
                                        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                                        password.setAttribute('type', type);
                                        // Toggle eye icon class
                                        this.classList.toggle('fa-eye');
                                        this.classList.toggle('fa-eye-slash');
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>