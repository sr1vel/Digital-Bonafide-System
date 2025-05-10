<?php
include("sconn.php");
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $query = "SELECT password, password_updated FROM login WHERE username='$username'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $db_password = $row['password'];
            $password_updated = $row['password_updated'];

            if (password_verify($password, $db_password)) {
                $_SESSION['username'] = $username;

                if ($password_updated == 0) {
                    header("Location: tutorafterlogin.php"); // Redirect to password change page
                } else {
                    header("Location: tutorafterlogin.php"); // Redirect to the main page
                }
                exit;
            } else {
                $error_message = "Incorrect username or password.";
            }
        } else {
            $error_message = "Username not found.";
        }
    } else {
        $error_message = "Both username and password fields are required.";
    }
}

if (isset($conn) && $conn->ping()) {
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="demoo.css" />
    <title>Static Template</title>
    <style>
      *,
*:before,
*:after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Open Sans', Helvetica, Arial, sans-serif;
    background: #ffffff;
}

input,
button {
    border: none;
    outline: none;
    background: none;
    font-family: 'Open Sans', Helvetica, Arial, sans-serif;
}

.tip {
    font-size: 20px;
    margin: 40px auto 50px;
    text-align: center;
}

.cont {
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    width: 900px;
    height: 550px;
    margin: 0 auto 100px;
    background: #fff;
    box-shadow: -10px -10px 15px rgba(255, 255, 255, 0.3), 10px 10px 15px rgba(70, 70, 70, 0.15), inset -10px -10px 15px rgba(255, 255, 255, 0.3), inset 10px 10px 15px rgba(70, 70, 70, 0.15);
}

.form {
    position: relative;
    width: 640px;
    height: 100%;
    -webkit-transition: -webkit-transform 1.2s ease-in-out;
    transition: -webkit-transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out, -webkit-transform 1.2s ease-in-out;
    padding: 50px 30px 0;
}

.sub-cont {
    overflow: hidden;
    position: absolute;
    left: 640px;
    top: 0;
    width: 900px;
    height: 100%;
    padding-left: 260px;
    background: #fff;
    -webkit-transition: -webkit-transform 1.2s ease-in-out;
    transition: -webkit-transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out, -webkit-transform 1.2s ease-in-out;
}

.cont.s--signup .sub-cont {
    -webkit-transform: translate3d(-640px, 0, 0);
    transform: translate3d(-640px, 0, 0);
}

button {
    display: block;
    margin: 0 auto;
    width: 260px;
    height: 36px;
    border-radius: 30px;
    color: #fff;
    font-size: 15px;
    cursor: pointer;
}

.img {
    overflow: hidden;
    z-index: 2;
    position: absolute;
    left: 0;
    top: 0;
    width: 260px;
    height: 100%;
    padding-top: 360px;
}

.img:before {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    width: 900px;
    height: 100%;
    background-image: url("ext.jpg");
    opacity: .8;
    background-size: cover;
    -webkit-transition: -webkit-transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out, -webkit-transform 1.2s ease-in-out;
}

.img:after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(9,42,71,0.9);
}

.cont.s--signup .img:before {
    -webkit-transform: translate3d(640px, 0, 0);
    transform: translate3d(640px, 0, 0);
}

.img__text {
    z-index: 2;
    position: absolute;
    left: 0;
    top: 50px;
    width: 100%;
    padding: 0 20px;
    text-align: center;
    color: #fff;
    -webkit-transition: -webkit-transform 1.2s ease-in-out;
    transition: -webkit-transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out;
    transition: transform 1.2s ease-in-out, -webkit-transform 1.2s ease-in-out;
}

.img__text h2 {
    margin-bottom: 10px;
    font-weight: normal;
}

.img__text p {
    font-size: 14px;
    line-height: 1.5;
}

.cont.s--signup .img__text.m--up {
    -webkit-transform: translateX(520px);
    transform: translateX(520px);
}

.img__text.m--in {
    -webkit-transform: translateX(-520px);
    transform: translateX(-520px);
}

.cont.s--signup .img__text.m--in {
    -webkit-transform: translateX(0);
    transform: translateX(0);
}

.img__btn {
    overflow: hidden;
    z-index: 2;
    position: relative;
    width: 100px;
    height: 36px;
    margin: 0 auto;
    background: transparent;
    color: #fff;
    text-transform: uppercase;
    font-size: 15px;
    cursor: pointer;
}

.img__btn:after {
    content: '';
    z-index: 2;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    border: 2px solid #fff;
    border-radius: 30px;
}

.img__btn span {
    position: absolute;
    left: 0;
    top: 0;
    display: -webkit-box;
    display: flex;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    align-items: center;
    width: 100%;
    height: 100%;
    -webkit-transition: -webkit-transform 1.2s;
    transition: -webkit-transform 1.2s;
    transition: transform 1.2s;
    transition: transform 1.2s, -webkit-transform 1.2s;
}

.img__btn span.m--in {
    -webkit-transform: translateY(-72px);
    transform: translateY(-72px);
}

.cont.s--signup .img__btn span.m--in {
    -webkit-transform: translateY(0);
    transform: translateY(0);
}

.cont.s--signup .img__btn span.m--up {
    -webkit-transform: translateY(72px);
    transform: translateY(72px);
}

h2 {
    width: 100%;
    font-size: 26px;
    text-align: center;
}

label {
    display: block;
    width: 260px;
    margin: 25px auto 0;
    text-align: center;
}

label span {
    font-size: 12px;
    color: #cfcfcf;
    text-transform: uppercase;
}

input {
    display: block;
    width: 100%;
    margin-top: 5px;
    padding-bottom: 5px;
    font-size: 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.4);
    text-align: center;
}

.forgot-pass {
    margin-top: 15px;
    text-align: center;
    font-size: 12px;
    color: #cfcfcf;
}

.submit {
    margin-top: 40px;
    margin-bottom: 20px;
    background: #092a47;
    text-transform: uppercase;
}

.fb-btn {
    border: 2px solid #ca341a;
    color: #0850ecef;
}

.fb-btn span {
    font-weight: bold;
    color: #de360c;
}

.sign-in {
    -webkit-transition-timing-function: ease-out;
    transition-timing-function: ease-out;
}

.cont.s--signup .sign-in {
    -webkit-transition-timing-function: ease-in-out;
    transition-timing-function: ease-in-out;
    -webkit-transition-duration: 1.2s;
    transition-duration: 1.2s;
    -webkit-transform: translate3d(640px, 0, 0);
    transform: translate3d(640px, 0, 0);
}

.sign-up {
    -webkit-transform: translate3d(-900px, 0, 0);
    transform: translate3d(-900px, 0, 0);
}

.cont.s--signup .sign-up {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
}
.password-wrapper {
            position: relative;
            width: 100%;
            margin-top: 5px;
        }

        input[type="password"] {
            width: 100%;
            padding-right: 40px; /* Add padding to make room for the eye icon */
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .loginbutton {
    position: absolute; /* Positioning it absolutely */
    top: 20px; /* Distance from the top */
    right: 20px; /* Distance from the right */
    background-color: #092a47;
    color: white;
    width: 100px;
    height: 50px;
    border-radius: 30px;
    z-index: 2; /* Ensure it stays on top of other elements */
}
.loginbutton:hover{
background-color:#a2a5a8;
color:black;
}
    </style>
   <body>
   <div class="login-section">
   <button class="loginbutton" onclick="window.location.href='front.php'">Home</button>
</div>
    <br><br>
    <div class="cont">
      <!-- Sign-In Form -->
      <div class="form sign-in">
        <h2>Welcome</h2>
        <form action="" method="post">
          <label>
            <span>Email</span>
            <input type="email" name="username" required />
          </label>
          <label>
            <span>Password</span>
            <div class="password-wrapper">
              <input type="password" name="password" id="signin-password" required />
              <i class="toggle-password fa fa-eye" id="toggleSigninPassword"></i>
            </div>
            <p class="forgot-pass"><a href="forgetpass.php">Forgot password?</a></p>
          </label>
          <button type="submit" class="submit">Sign In</button>
        </form>
        <?php if (!empty($error_message)) { echo '<p class="msg">'.$error_message.'</p>'; } ?>
      </div>

      <!-- Sign-Up Form -->
      <div class="sub-cont">
        <div class="img">
          <div class="img__text m--up">
            <h3>Don't have an account? Please Sign up!</h3>
          </div>
          <div class="img__text m--in">
            <h3>If you already have an account, just sign in.</h3>
          </div>
          <div class="img__btn">
            <span class="m--up">Sign Up</span>
            <span class="m--in">Sign In</span>
          </div>
        </div>

        <div class="form sign-up">
          <h2>Create your Account</h2>
          <form action="signup.php" method="post">
            <label>
              <span>Email</span>
              <input type="email" name="username" required />
            </label>
            <label>
              <span>Password</span>
              <div class="password-wrapper">
                <input type="password" name="password" id="signup-password" required />
                <i class="toggle-password fa fa-eye" id="toggleSignupPassword"></i>
              </div>
            </label>
            <label>
              <span>Confirm Password</span>
              <div class="password-wrapper">
                <input type="password" name="confirm_password" id="confirm-password" required />
                <i class="toggle-password fa fa-eye" id="toggleConfirmPassword"></i>
              </div>
            </label>
            <button type="submit" class="submit">Sign Up</button>
          </form>
          <?php if (!empty($signup_error_message)) { echo '<p class="msg">'.$signup_error_message.'</p>'; } ?>
        </div>
      </div>
    </div>

    <script>
      document.querySelector('.img__btn').addEventListener('click', function() {
        document.querySelector('.cont').classList.toggle('s--signup');
      });

      function togglePasswordVisibility(inputId, toggleIconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(toggleIconId);

        toggleIcon.addEventListener('click', function () {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          toggleIcon.classList.toggle('fa-eye');
          toggleIcon.classList.toggle('fa-eye-slash');
        });
      }

      togglePasswordVisibility('signin-password', 'toggleSigninPassword');
      togglePasswordVisibility('signup-password', 'toggleSignupPassword');
      togglePasswordVisibility('confirm-password', 'toggleConfirmPassword');
    </script>
  </body>
</html>