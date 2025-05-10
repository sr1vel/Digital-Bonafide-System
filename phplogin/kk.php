
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
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="demoo.css" />
    <title>Static Template</title>
    <style>
        * {
    box-sizing: border-box;
  }
  html {
    font-family: "Rubik", sans-serif;
    font-size: 16px;
  }
  
  body {
    padding: 0;
    margin: 0;
background-color: #092a47;
    background-repeat: no-repeat;
background-image:url("g.png") ;
    color: white;
  }
  
  .container {
    display: flex;
    height: 100vh;
  }
  
  .left {
    display: flex;
    animation-name: left;
    animation-duration: 1s;
    animation-fill-mode: both;
    animation-delay: 1s;
  
    flex-wrap: wrap;
    flex-direction: column;
    justify-content: center;
  
    overflow: hidden;
  }
  
  .right {
    flex: 1;
    transition: 1s;
    background-color: #ffff;
    background-image: url("bg.png");
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
  }
  
  header h2 {
    margin: 0;
  }
  
  header h4 {
    color: #fff;
    opacity: 0.4;
    font-size: 1rem;
    font-weight: normal;
  }
  
  form {
    display: flex;
    flex-direction: column;
  }
  
  form p {
    text-align: right;
  }
  form p a {
    color: #fff;
    font-size: 0.875rem;
  }
  
  .input-field {
    height: 46px;
    padding: 0 1rem;
    border: 2px solid #ddd;
    border-radius: 20px;
    outline: none;
    margin-top: 1.25rem;
    transition: 0.2s;
    font-family: "Rubik", sans-serif;
    width:350px;
  }
  
  .input-field:focus {
    border-color: #414141;
  }
  
  form button {
    padding: 0.75rem 0.625rem;
    border: 0;
    background: linear-gradient(to right, #9bbedd 0%, #2881cf 100%);
    border-radius: 40px;
    margin-top: 0.625;
    color: #fff;
    text-transform: uppercase;
    font-weight: 500;
    font-family: "Rubik", sans-serif;
    width:350px;
  }
  
  .animation {
    animation-name: move;
    animation-duration: 0.4s;
    animation-fill-mode: both;
    animation-delay: 2s;
  }
  
  .a1 {
    animation-delay: 2s;
  }
  
  .a2 {
    animation-delay: 2.2;
  }
  
  .a3 {
    animation-delay: 2.4s;
  }
  
  .a4 {
    animation-delay: 2.6s;
  }
  
  .a5 {
    animation-delay: 2.8s;
  }
  
  .a6 {
    animation-delay: 3s;
  }
  
  @keyframes move {
    0% {
      opacity: 0;
      visibility: hidden;
      transform: translateY(-40px);
    }
    100% {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
  }
  
  @keyframes left {
    0% {
      opacity: 0;
      width: 0%;
    }
    100% {
      opacity: 1;
      width: 520px;
      padding: 3rem;
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
  <body>
    <div class="container">
      <div class="left">
        <div class="login-section">
          <header>
            <h2 class="animation a1">Login</h2>
          </header>
          <br>
          <form method="POST" action="">  
                                    <div data-mdb-input-init class="form-outline mb-3">
                                    <form class="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
                                        <label class="form-label" for="form2Example11">Username</label>
                                        <br>
                                        <input type="text" id="username" name="username" class="input-field animation a3" placeholder="Rollnumber" />
                                    </div>
                                    <br>
                                    <div data-mdb-input-init class="form-outline mb-4 position-relative password-container">
                                        <label class="form-label" for="form2Example22">Password</label>
                                        <br>
                                        <input type="password" id="password" name="password" class="input-field animation a4" />
                    
                                        <i id="togglePassword" class="fas fa-eye"></i>
                                    </div>
                                    <br>
                                    <p class="animation a5"><a href="#">Forgot password?</a></p>
                                    <br>
                                    <div class="text-center pt-1 mb-5 pb-1">
                                        <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type = "submit" >Log in</button>
                                    </div>
                                    <p class="animation a5"><a href="#">Dont have account?Sign up</a></p>
                                    <?php
                                    // Display error message if set
                                    if (!empty($error_message)) {
                                        echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                                    }
                                    ?>
                                </form>
                               
        </div>
      </div>
      <div class="right"></div>
    </div>
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
  </body>
</html>
