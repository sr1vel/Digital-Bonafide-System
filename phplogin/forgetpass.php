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
    // Establish a database connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    if (!empty($username)) {
        // Check if the username exists in the database
        $query = "SELECT username FROM login WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Generate a verification code
            $verification_code = rand(100000, 999999);

            // Store the verification code in the database (assuming you have a table `verification_codes`)
            $insert_query = "INSERT INTO verification_codes (email, code) VALUES (:username, :verification_code) 
                             ON DUPLICATE KEY UPDATE code = :verification_code";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $insert_stmt->bindParam(':verification_code', $verification_code, PDO::PARAM_INT);

            if ($insert_stmt->execute()) {
                // Prepare the recipients array (in this case, the user email)
                $recipients = [$username];

                // Call the sendEmail function
                sendEmail($recipients, $verification_code);

                $success_message = "A verification code has been sent to your registered email. Please check your email to reset your password.";
            } else {
                $error_message = "Error: " . $pdo->errorInfo()[2];
            }
        } else {
            $error_message = "No account found with that username.";
        }
    } else {
        $error_message = "Please enter your username.";
    }
}


function sendEmail($recipients, $verification_code)
{
    $mail = new PHPMailer(true); // Passing true enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;
        $mail->Username = 'asrec2024@gmail.com'; // SMTP username
        $mail->Password = 'vjby abvt itud gyhk'; // SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Set sender email address
        $mail->setFrom('asrec2024@gmail.com', 'Admin@SREC');

        // Recipients
        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient);
        }

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Password Reset Verification Code';
        $mail->Body = "Your password reset verification code is: $verification_code";
        $mail->send();
        echo '<script>Toastify({
            text: "Form submitted successfully!",
            duration: 0.1, // Toast duration in milliseconds
            gravity: "bottom", // Toast position
            backgroundColor: "#4CAF50", // Toast background color
        }).showToast();</script>';
        echo '<script>setTimeout(function(){ window.location.href = "resetpassword.php"; }, 0.5);</script>';
    } catch (Exception $e) {
        // Show error message
        echo '<script>Toastify({
            text: "Error: ' . $e->getMessage() . '",
            duration: 0.1, // Toast duration in milliseconds
            gravity: "bottom", // Toast position
            backgroundColor: "#FF6347", // Toast background color
        }).showToast();</script>';
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
    <title>Forgot Password</title>
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
        width: calc(100% - 90px); /* Adjust the width for the padding */
        margin-top: 5px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid rgba(0, 0, 0, 0.4);
        border-radius: 20px; /* Apply border-radius */
        text-align: center;
        margin-left: 12px;
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

    .forgot-password {
        display: block;
        margin-top: 10px;
        text-align: center;
        font-size: 14px;
        color: darkgray;
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
        <!-- Forgot Password Form -->
        <div class="form">
            <h2>Forgot Password</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
                <label>
                    <input id="username" type="text" name="username" placeholder="Email" required />
                </label>
                <button type="submit">Send Verification Code</button>
                <a href="front.php" class="forgot-password">Remember your password? Sign In</a>
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
            if (username.trim() === "") {
                alert("Please enter your username.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
