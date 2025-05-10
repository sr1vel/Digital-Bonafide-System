<?php
include("sconnect.php");
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetching form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check user credentials and redirect accordingly
    if (is_numeric($username) && strlen($username) == 11 && $password == 'Srec@123') {
        $_SESSION['username'] = $username;
        header("Location: stuafterlogin.php");
        exit;
    } elseif (filter_var(FILTER_VALIDATE_EMAIL)) {
        // Check if user is tutor, academic coordinator, or HOD
        if ( $password == 'admin@123') {
            $_SESSION['username'] = $username;
            // Redirect admin to samplemainadmin.php
            header("Location: adminafterlogin.php");
            exit;
        } elseif ($password == 'Staff@123') {
            $_SESSION['username'] = $username;
            // Redirect tutor to teacher.php
            header("Location: tutorafterlogin.php");
            exit;
        } elseif ($password == 'ac@123') {
            $_SESSION['username'] = $username;
            // Redirect academic coordinator to academic_coordinator_page.php
            header("Location: acafterlogin.php");
            exit;
        } elseif ($username === $password) {
            $_SESSION['username'] = $username;
                header("Location: passedfront.php");
                exit;
        } elseif ($password == 'hod@123') {
            $_SESSION['username'] = $username;
            // Redirect HOD to hod_page.php
            header("Location: hodafterlogin.php");
            exit;
        }elseif ($password === 'Srecpg@123') {
            $_SESSION['username'] = $username;
            header("Location: pgfront.php");
            exit;
    }
    elseif ($password === 'Hosteltutor@123') {
        $_SESSION['username'] = $username;
        header("Location: hostelafterlogin.php");
        exit;
}
}
    // If none of the above conditions match, display an error message
    $error_message = "Invalid username or password. Please try again.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digitalized Bonafide System</title>
    <link rel="shortcut icon" href="assets/images/fav.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/fav.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/plugins/slider/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/plugins/slider/css/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function validateForm() {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;

            // Check if username and password fields are not empty
            if (username.trim() === "" || password.trim() === "") {
                alert("Please fill in all fields.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
      <style>
    .password-container {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
    }
  </style>
</head>
<body class="form-login-body"> 
        <div class="container-fluid">
            <div class="top-menu"> 
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 logo">
                            <a class="navbar-brand text-success logo h1 align-self-center" href="index.html">
                                <img src="footer-logo.png" alt="Srec Logo" class="navbar-logo">
                            </a>
                            
                            <style>
                                .navbar-logo {
                                    width: 900px;
                                    height: 70px;
                                }
                            </style>
                
                        </div>
                        <div class="col-md-9 sup">
                            <ul>
                                <li class="move-right">
                                <button type="button" class="btn btn-top btn-sm" onclick="location.href='front.php'">Back</button>

                                  </li>                                  
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="login-body container-fluid">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-5">
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                            <h2>LOGIN</h2>
                            
                            <br>
                            <div></div>
        <form class="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
            <input id="username" type="text" name="username" placeholder="Username"  class="form-control "/><div></div>
        <br>
        <div class="password-container">
                                <input id="password" type="password" name="password" placeholder="Password" class="form-control" value=""/>
                                <i class="far fa-eye toggle-password" id="togglePassword"></i>
                            </div>
            <br>
            <br>
            <button  class="btn btn-primary" type="submit">Sign In</button>
        </form>
    </div>

                            </div>
                            <div class="col-md-7 align-left"> <!-- Add a class for alignment -->
                                <div class="login-img">
                                    <img src="user-verification-unauthorized-access-prevention-private-account-authentication-cyber-security-people-entering-login-password-safety-measures.png" alt="login-img">
                                </div>
                            </div>
                        </div>
                </div>
        </div>
        
    </body>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/scroll-fixed/jquery-scrolltofixed-min.js"></script>
    <script src="assets/plugins/slider/js/owl.carousel.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function (e) {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the eye / eye slash icon
        this.classList.toggle('fa-eye-slash');
    });
    </script>
</html>