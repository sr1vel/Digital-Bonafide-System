<?php
require 'PHPMailerAutoload.php';
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "hackathon";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $verification_code = $_POST['verification_code'];
    $new_password = $_POST['new_password'];

    if (!empty($username) && !empty($verification_code) && !empty($new_password)) {
        $query = "SELECT * FROM verification_codes WHERE email='$username' AND code='$verification_code'";
        $result = $pdo->query($query);

        if ($result && $result->rowCount() > 0) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            // Reset password query
            $update_query = "UPDATE login SET password='$hashed_password', password_updated=1 WHERE username='$username'";
            if ($pdo->query($update_query)) {
                $success_message = 'Your password has been successfully reset. You can now <a href="front.php">log in</a> with your new password.';
            } else {
                $error_message = "Error: Could not update the password. Please try again.";
            }
        } else {
            $error_message = "Invalid verification code.";
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}

if (isset($pdo) && $pdo->query("SELECT 1")) {
    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="demoo.css">
    <style>
    body {
        font-family: "Open Sans", Helvetica, Arial, sans-serif;
        background: #fff;
    }

    .cont {
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        width: 900px;
        height: 550px;
        margin: 0 auto;
        background: #fff;
        box-shadow: -10px -10px 15px rgba(255, 255, 255, 0.3),
            10px 10px 15px rgba(70, 70, 70, 0.15),
            inset -10px -10px 15px rgba(255, 255, 255, 0.3),
            inset 10px 10px 15px rgba(70, 70, 70, 0.15);
    }

    .form {
        position: relative;
        width: 640px;
        height: 100%;
        padding: 50px 30px 0;
    }

    .form h2 {
        text-align: center;
    }

    .form input {
        display: block;
        width: calc(100% - 90px);
        margin-top: 5px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid rgba(0, 0, 0, 0.4);
        border-radius: 20px;
        text-align: center;
        margin-left: 20px;
    }

    .form button {
        display: block;
        margin: 40px auto 20px;
        width: 260px;
        height: 36px;
        border-radius: 30px;
        color: #fff;
        background: #092a47;
        font-size: 15px;
        cursor: pointer;
        border: none;
        transition: background 0.3s, box-shadow 0.3s;
        outline: none; /* Remove default focus outline */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form button:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        background-color: #092a47;
    }

    .form button:active {
        background: #092a47; /* Ensure the background color stays the same when clicked */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Slightly smaller shadow when clicked */
    }

    .form button:focus {
        outline: none; /* Ensure no outline appears on focus */
    }

    .form .alert {
        padding: 15px;
        border-radius: 10px;
        margin-top: 20px;
        text-align: center;
        font-size: 16px;
        line-height: 1.4;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .forgot-password {
        display: block;
        margin-top: 10px;
        text-align: center;
        font-size: 14px;
        color: darkgray;
    }
    a{
        color: red;
    }

    .img {
        background: #092a47;
        position: absolute;
        top: 0;
        right: 0;
        width: 260px;
        height: 100%;
        background-color: #092a47;
        background-size: cover;
    }

    .img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

</head>
<body>
    <div class="cont">
        <!-- Reset Password Form -->
        <div class="form">
            <h2>Reset Password</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
                <input id="username" type="text" name="username" placeholder="Email" required />
                <input id="verification_code" type="text" name="verification_code" placeholder="Verification Code" required />
                <input id="new_password" type="password" name="new_password" placeholder="New Password" required />
                <button type="submit">Reset Password</button>
                <a href="slogin.php" class="forgot-password">Remember your password? Sign In</a>
            </form>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success mt-3"><?php echo $success_message; ?></div>
            <?php endif; ?>
        </div>
        <div class="img">
            <img src="C:\Users\POORNA\Downloads\Login off\free-latest-login-page-template\assets\images\pwblue-removebg-preview.jpg" alt="">
        </div>
    </div>

    <script>
        function validateForm() {
            var username = document.getElementById("username").value;
            var verificationCode = document.getElementById("verification_code").value;
            var newPassword = document.getElementById("new_password").value;

            if (username.trim() === "" || verificationCode.trim() === "" || newPassword.trim() === "") {
                alert("Please fill in all fields.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
