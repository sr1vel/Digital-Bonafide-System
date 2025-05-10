<?php
session_start(); // Start the session

// Check if the username is set in the session
if(isset($_SESSION['username'])) {
    // Display the username
    echo "Welcome!!! " . $_SESSION['username'];
} else {
    // If username is not set, display a message
    echo "Username is not set in the session.";
}
require 'PHPMailerAutoload.php';
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "hackathon";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Assuming $_SESSION['username'] holds the login username
$username = $_SESSION['username']; // or $_POST['username'] or $_GET['username']

// Prepare and execute the query
$sql = "SELECT ugstudents.rollnumber, ugstudents.name 
        FROM ugstudents 
        INNER JOIN login ON ugstudents.rollnumber = login.username 
        WHERE login.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the result
if ($row = $result->fetch_assoc()) {
    $rollnumber = $row['rollnumber'];
    $name = $row['name'];
} else {
    die("No record found for username: " . htmlspecialchars($username));
}
$stmt->close();
$conn->close();
try 
{
    // Establish a database connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $e) 
{
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and sanitize it
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $rollnumber = filter_input(INPUT_POST, 'rollnumber', FILTER_SANITIZE_STRING);
    $studentmail = filter_input(INPUT_POST, 'studentmail', FILTER_SANITIZE_EMAIL);
    $course = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_STRING);
    $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_STRING);
    $lateral = ''; // Initialize $lateral variable
    if ($year === 'Second Year' || $year === 'Third Year' || $year === 'Fourth Year') {
        $lateral = filter_input(INPUT_POST, 'feesReceipt', FILTER_SANITIZE_STRING);
    }
    $hd = ''; // Placeholder for $hd variable
	$gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_EMAIL);
    $htmail = filter_input(INPUT_POST, 'hostelTutorEmail', FILTER_SANITIZE_EMAIL);
    $tmail = filter_input(INPUT_POST, 'tutorEmail', FILTER_SANITIZE_EMAIL);
    $atmail = filter_input(INPUT_POST, 'alternateTutorEmail', FILTER_SANITIZE_EMAIL);
    $acmail = filter_input(INPUT_POST, 'academicCoordinatorEmail', FILTER_SANITIZE_EMAIL);
    $hmail = filter_input(INPUT_POST, 'hodEmail', FILTER_SANITIZE_EMAIL);
    $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
	$prevdetails = filter_input(INPUT_POST, 'prevdetails', FILTER_SANITIZE_STRING);
    $otherReason = filter_input(INPUT_POST, 'otherReason', FILTER_SANITIZE_STRING);
    $IDProof = '';
    $fees = $_POST['fees'] ?? 'No';  //Assign 'No' if 'fees' field is not provided
    $adtype = filter_input(INPUT_POST, 'adtype', FILTER_SANITIZE_STRING); 
    $fg = filter_input(INPUT_POST, 'firstGraduate', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'cat', FILTER_SANITIZE_STRING);
    $hd = isset($_POST['hosteller']) ? ($_POST['hosteller'] === 'Hosteller' ? 'Hosteller' : 'Day Scholar') : '';
    // Get the value of fees receipt choice

    if ($reason === 'Other') {
        $reason = $otherReason;
    }

// Prepare the INSERT statement for ug table


    // ID Proof File Upload Handling
    $IDProof = $_FILES['IDProof'];
    if ($IDProof['error'] == 0) {
        // Handle file upload
        $target_dir = "uploads/"; // Directory where uploaded files will be stored
        $target_file = $target_dir . basename($IDProof["name"]); // Get the file name
        if (move_uploaded_file($IDProof["tmp_name"], $target_file)) {
            // File uploaded successfully
            echo "File uploaded: " . htmlspecialchars(basename($IDProof["name"]));
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Error uploading file.";
    }
    
    if ($IDProof['error'] == 0) {
        // Implement file upload security measures here
        try {
            $pdo->beginTransaction();

            // Prepare the INSERT statement for ug table
            $stmt_ug = $pdo->prepare("INSERT INTO student (name, rollnumber, studentmail, course, year, lateral, hd, htmail, tmail, atmail, acmail, hmail, reason, IDProof, fees, adtype, fg, category,gender,prevdetails) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

            $stmt_ug->execute([$name, $rollnumber, $studentmail, $course, $year, $lateral, $hd, $htmail, $tmail, $atmail, $acmail, $hmail, $reason, $target_file, $fees, $adtype, $fg, $category,$gender,$prevdetails]);
            
            // Prepare the INSERT statement for tutor table
            $stmt_tutor = $pdo->prepare("INSERT INTO tutor (name, rollnumber, studentmail, course, year, lateral, hd, htmail, tmail, atmail, acmail, hmail, reason, IDProof, fees, adtype, fg, category,gender,prevdetails) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

            // Execute the INSERT statement for tutor table
            $stmt_tutor->execute([$name, $rollnumber, $studentmail, $course, $year, $lateral, $hd, $htmail, $tmail, $atmail, $acmail, $hmail, $reason, $target_file, $fees, $adtype, $fg, $category,$gender,$prevdetails]);

            // Prepare the INSERT statement for academiccoordinator table
            $stmt_academiccoordinator = $pdo->prepare("INSERT INTO academiccoordinator (name, rollnumber, studentmail, course, year, lateral, hd, htmail, tmail, atmail, acmail, hmail, reason, IDProof, fees, adtype, fg, category,gender,prevdetails) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

            // Execute the INSERT statement for academiccoordinator table
            $stmt_academiccoordinator->execute([$name, $rollnumber, $studentmail, $course, $year, $lateral, $hd, $htmail, $tmail, $atmail, $acmail, $hmail, $reason, $target_file, $fees, $adtype, $fg, $category,$gender,$prevdetails]);

            // Prepare the INSERT statement for hod table
            $stmt_hod = $pdo->prepare("INSERT INTO hod (name, rollnumber, studentmail, course, year, lateral, hd, htmail, tmail, atmail, acmail, hmail, reason, IDProof, fees, adtype, fg, category,gender,prevdetails) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

            // Execute the INSERT statement for hod table
            $stmt_hod->execute([$name, $rollnumber, $studentmail, $course, $year, $lateral, $hd, $htmail, $tmail, $atmail, $acmail, $hmail, $reason, $target_file, $fees, $adtype, $fg, $category,$gender,$prevdetails]);
            
			$stmt_analysis = $pdo->prepare("INSERT INTO uganalysis (name, rollnumber, studentmail, course, year, lateral, hd, htmail, tmail, atmail, acmail, hmail, reason, IDProof, fees, adtype, fg, category,gender,prevdetails) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

            // Execute the INSERT statement for hod table
            $stmt_analysis->execute([$name, $rollnumber, $studentmail, $course, $year, $lateral, $hd, $htmail, $tmail, $atmail, $acmail, $hmail, $reason, $target_file, $fees, $adtype, $fg, $category,$gender,$prevdetails]);
			 // Prepare the INSERT statement for ug table
			 $stmt_htutor = $pdo->prepare("INSERT INTO htutor (name, rollnumber, studentmail, course, year, lateral, hd, htmail, tmail, atmail, acmail, hmail, reason, IDProof, fees, adtype, fg, category,gender,prevdetails) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

			 $stmt_htutor->execute([$name, $rollnumber, $studentmail, $course, $year, $lateral, $hd, $htmail, $tmail, $atmail, $acmail, $hmail, $reason, $target_file, $fees, $adtype, $fg, $category,$gender,$prevdetails]);
            $pdo->commit();

            // Prepare the recipients array based on provided email fields
            $recipients = array_filter([$htmail, $tmail, $atmail, $acmail, $hmail]);

            // Call the sendEmail function
            sendEmail($recipients, $name, $rollnumber, $studentmail, $course, $reason, $target_file);
        } catch (PDOException $e) {
            // Handle database transaction errors
            echo "Database error: " . $e->getMessage();
        }
    }
}

function sendEmail($recipients, $name, $rollnumber, $studentmail, $course, $reason, $target_file) {
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

        //Recipients
        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient);
        }
    
        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'New Student Bonafide Registration';
        $mail->Body    = "A new student with name $name, roll number $rollnumber, and email $studentmail has registered for the course $course. Reason: $reason"; // Email body content

        $mail->send();
        echo '<script>Toastify({
            text: "Form submitted successfully!",
            duration: 0.5, // Toast duration in milliseconds
            gravity: "bottom", // Toast position
            backgroundColor: "#4CAF50", // Toast background color
        }).showToast();</script>';
        echo '<script>setTimeout(function(){ window.location.href = "stuafterlogin.php"; }, 0.5);</script>';
    } catch (Exception $e) {
        // Show error message
        echo '<script>Toastify({
            text: "Error: ' . $e->getMessage() . '",
            duration: 0.5, // Toast duration in milliseconds
            gravity: "bottom", // Toast position
            backgroundColor: "#FF6347", // Toast background color
        }).showToast();</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>SREC</title>
	<link rel="stylesheet" href="base.css" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet" />
	<title>Student Details Form</title>
	<style>
		/* CSS File */
		body {
			background: #f0f0f0;
			font-family: "Roboto", sans-serif;
			height: 100%;
		}

		.container-fluid {
			position: absolute;
		}

		.logout {
			position: absolute;
			top: 15px;
			/* Adjust as needed */
			right: 10px;
			/* Adjust as needed */
		}

		.logout button {
			background-color: #ffffff;
			/* Red background */
			color: rgb(6, 2, 2);
			/* White text */
			border: none;
			/* No border */
			padding: 10px 20px;
			/* Adjust padding as needed */
			cursor: pointer;
			/* Pointer cursor on hover */
			border-radius: 25px;
			/* Rounded corners */
			font-size: 1.0rem;
			font-family: "Roboto", sans-serif;
		}

		.top-menu {
			background-color: #fff;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			z-index: 1000;
			position: fixed;
			width: 100%;
		}

		.navbar-logo {
			width: 100px;
		}

		.step {
			display: none;
		}

		.step.active {
			display: block;
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
		}

		.container.mt-5 {
			max-width: 700px;
			margin: 150px auto 0;
			background: #fff;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
		}

		h2 {
			text-align: center;
		}


		.mb-3 {
			margin-bottom: 1rem;

		}

		.form-label {
			display: block;
			margin-bottom: 0.5rem;
		}

		.form-control,
		.form-select {
			display: block;
			width: 100%;
			padding: 0.5rem;
			font-size: 1rem;
			line-height: 1.5;
			color: #495057;
			background-color: #fff;
			background-clip: padding-box;
			border: 1px solid #ced4da;
			border-radius: 0.25rem;
			transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		}

		.form-control:focus,
		.form-select:focus {
			border-color: #80bdff;
			outline: 0;
			box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
		}

		/* Universal Styles */
		* {
			margin: 0;
			padding: 0;
			list-style: none;
			box-sizing: border-box;
		}

		/* Body and HTML full height */
		html,
		body {
			height: 100%;
		}

		/* Alignment */
		.align-left {
			text-align: left;
		}

		/* Login Image Styles */
		.login-img img {
			margin-left: 200px;
			/* Adjust as needed */
		}

		/* Form Login Body Top Menu */
		.form-login-body .top-menu {
			box-shadow: 0 0 10px 0 #00000026;
			background-color: #092a47;
			position: fixed;
			top: 0;
			width: 100%;
			z-index: 1000;
			padding: 20px 0;
		}

		.form-login-body .top-menu .logo img {
			max-width: 500px;
			width: 100%;
			height: auto;
			margin-top: 7px;
			margin-left: auto;
			margin-right: auto;
			display: block;
		}

		/* Navigation Bar Styles */
		.navbar {
			background-color: #ffffff;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		}

		.navbar .navbar-brand {
			font-weight: bold;
			font-size: 1.5rem;
			color: #0065C3;
		}

		.navbar .navbar-brand:hover {
			color: #004a99;
		}

		.navbar-nav .nav-item .nav-link {
			font-family: 'Roboto', sans-serif;
			font-weight: 500;
			color: #333333;
			padding: 0.5rem 1rem;
		}

		.navbar-nav .nav-item .nav-link:hover {
			color: #0065C3;
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

		/* Media Queries */
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

		.logout {
			position: absolute;
			top: 20px;
			right: 10px;
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
			background-color: #092a47 !important;
			/* Darker red hover color */
			border-color: #092a47 !important;
			/* Matching hover border color */
			color: #ffffff
		}
	</style>
</head>

<body class="form-login-body">
	<div class="container-fluid">
		<div class="top-menu">
			<div class="container">
				<div class="row">
					<div class="col-md-3 col-12 logo">
						<img src="footer-logo.png" alt="Srec Logo"
							class="navbar-logo">
						</a>
					</div>
					<div class="logout">
						<button onclick="window.location.href='stuafterlogin.php'">Home</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Add some content here to show the scroll effect -->
		<div style="height: 2000px;"></div>
	</div>
	</div>

	<body>
		<div class="container mt-5">
			<form id="studentForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"
				enctype="multipart/form-data" onsubmit="return validateForm()">
				<h2 class="mb-4">Student Details Form</h2>
				<!-- Step 1: Personal Information -->
				<div class="step active" id="step1">
					<div class="mb-3">
						<label for="name" class="form-label">Name *</label>
						<input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($name); ?>" readonly>
					</div>
					<div class="mb-3">
						<label for="rollnumber" class="form-label">Roll Number *</label>
						<input type="text" id="rollnumber" name="rollnumber" class="form-control" required  value="<?php echo htmlspecialchars($rollnumber); ?>" readonly>
						
					</div>
					<div class="mb-3">
						<label for="studentmail" class="form-label">Student Email *</label>
						<input type="email" id="studentmail" name="studentmail" class="form-control" required>
					</div>
					<div class="mb-3" id="courseContainer">
						<label for="course" class="form-label">Course *</label>
						<select id="course" name="course" class="form-select" required>
							<option value="">Select Course</option>
							<option value="B.E. Computer Science and Engineering">B.E. Computer Science and Engineering
							</option>
							<option value="B.E. Electrical and Communication Engineering">B.E. Electrical and
								Communication
								Engineering</option>
							<option value="B.E. Electrical and Electronics Engineering">B.E. Electrical and Electronics
								Engineering</option>
							<option value="B.E. Mechanical Engineering">B.E. Mechanical Engineering</option>
							<option value="B.E. Electronics and Instrumentation Engineering">B.E. Electronics and
								Instrumentation Engineering</option>
							<option value="B.E. Biomedical Engineering">B.E. Biomedical Engineering</option>
							<option value="B.E. Aeronautical Engineering">B.E. Aeronautical Engineering</option>
							<option value="B.E. Civil Engineering">B.E. Civil Engineering</option>
							<option value="B.Tech. Information Technology">B.Tech. Information Technology</option>
							<option value="B.E. Robotics and Automation">B.E. Robotics and Automation</option>
							<option value="B.E. Artificial Intelligence and Data Science">B.E. Artificial Intelligence
								and
								Data Science</option>
							<option value="M.Tech. Computer Science and Engineering">M.Tech. Computer Science and
								Engineering</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="year" class="form-label">Year *</label>
						<select id="year" name="year" class="form-select" required onchange="toggleYearOptions()">
							<option value="">Select Year</option>
							<option value="First Year">First Year</option>
							<option value="Second Year">Second Year</option>
							<option value="Third Year">Third Year</option>
							<option value="Fourth Year">Fourth Year</option>
							<option value="Fifth Year">Fifth Year</option>
						</select>
					</div>
					<button type="button" class="btn btn-custom" onclick="nextStep('step2')">Next</button>
				</div>
				<!-- Step 2: Additional Information -->
				<div class="step" id="step2">
				<div>
					<label for="gender" class="form-label">Gender *</label>
						<select id="gender" name="gender" class="form-select" required onchange="toggleYearOptions()">
							<option value="">Select Gender</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
							</select>
					</div>
					<br>
					<div>
					<label for="prevdetails" class="form-label">Have you applied bonafide with fees before *</label>
						<select id="prevdetails" name="prevdetails" class="form-select" required onchange="toggleYearOptions()">
							<option value="">Select</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
							</select>
					</div>
					<br>
					
					<div class="mb-3" id="lateralStudentSection" style="display: none;">
						<label for="Lateral">Are you a lateral student?</label><br>
						<input type="radio" id="yes" name="feesReceipt" value="Yes">
						<label for="yes">Yes</label><br>
						<input type="radio" id="no" name="feesReceipt" value="No">
						<label for="no">No</label><br>
					</div>
					<div class="mb-3">
						<label for="hosteller">Are you a Hosteller or a Day Scholar?</label><br>
						<input type="radio" id="hosteller" name="hosteller" value="Hosteller" required
							onclick="toggleHostelTutorEmailField()">
						<label for="hosteller">Hosteller</label><br>
						<input type="radio" id="dayScholar" name="hosteller" value="Day Scholar" required
							onclick="toggleHostelTutorEmailField()">
						<label for="dayScholar">Day Scholar</label><br>
					</div>
					<div class="mb-3" id="hostelTutorEmailField" style="display: none;">
						<label for="hostelTutorEmail" class="form-label">Hostel Tutor's Email *</label>
						<input type="email" id="hostelTutorEmail" name="hostelTutorEmail" class="form-control">
					</div>
					<div class="mb-3">
						<label for="tutorEmail" class="form-label">Tutor's Email *</label>
						<input type="email" id="tutorEmail" name="tutorEmail" class="form-control" required>
					</div>
					<div class="mb-3">
						<label for="alternateTutorEmail" class="form-label">Alternate Tutor's Email *</label>
						<input type="email" id="alternateTutorEmail" name="alternateTutorEmail" class="form-control"
							required>
					</div>
					<div class="mb-3">
						<label for="academicCoordinatorEmail" class="form-label">Academic Coordinator's Email *</label>
						<input type="email" id="academicCoordinatorEmail" name="academicCoordinatorEmail"
							class="form-control" required>
					</div>
					<div class="mb-3">
						<label for="hodEmail" class="form-label">Head of Department's Email *</label>
						<input type="email" id="hodEmail" name="hodEmail" class="form-control" required>
					</div>
					<button type="button" class="btn btn-custom" onclick="prevStep('step1')">Previous</button>
					<button type="button" class="btn btn-custom" onclick="nextStep('step3')">Next</button>
				</div>
				<!-- Step 3: Confirmation -->
				<div class="step" id="step3">
					<div class="mb-3">
						<label for="FeesReceipt">Do you want the bonafide with Fees Structure?</label><br>
						<input type="radio" id="yesFeesReceipt" name="fees" value="Yes" required
							onclick="toggleAdmissionFields()">
						<label for="yesFeesReceipt">Yes</label><br>
						<input type="radio" id="noFeesReceipt" name="fees" value="No" required
							onclick="toggleAdmissionFields()">
						<label for="noFeesReceipt">No</label><br>
					</div>
					<div>
						<label for="info">These details need to be filled only if you need your bonafide with the fees
						Structure</label><br>
					</div>
					<div class="mb-3 hidden" id="adtypeField">
						<label>Choose your admission type:</label><br>
						<input type="radio" id="governmentQuota" name="adtype" value="Government Quota">
						<label for="governmentQuota">Government Quota</label><br>
						<input type="radio" id="managementQuota" name="adtype" value="Management Quota">
						<label for="managementQuota">Management Quota</label><br>
						<input type="radio" id="7.5Reservation" name="adtype" value="7.5 Reservation">
						<label for="7.5Reservation">7.5 Reservation</label><br>
					</div>
					<div class="mb-3 hidden" id="firstGraduateField">
						<label for="Graduate">Are you a First Graduate?</label><br>
						<input type="radio" id="Yes" name="firstGraduate" value="Yes">
						<label for="Yes">Yes</label><br>
						<input type="radio" id="No" name="firstGraduate" value="No">
						<label for="No">No</label><br>
					</div>
					<div class="mb-3 hidden" id="categoryField">
						<label for="Category">Choose your category:</label><br>
						<input type="radio" id="oc" name="cat" value="OC">
						<label for="oc">ST</label><br>
						<input type="radio" id="sc" name="cat" value="SC">
						<label for="sc">SC</label><br>
						<input type="radio" id="obc" name="cat" value="OBC">
						<label for="sc">OBC</label><br>
					</div>
					<div class="mb-3">
						<label for="reason" class="form-label">Reason *</label>
						<select id="reason" name="reason" class="form-select" required onchange="toggleReasonField()">
							<option value="">Select Reason</option>
							<option value="Passport verification">Passport verification</option>
							<option value="Educational loan">Educational loan</option>
							<option value="Tamilnadu Labour Welfare Scheme Scholarship">Tamilnadu Labour Welfare Scheme
								Scholarship</option>
							<option value="Internship">Internship</option>
							<option value="Government Scholarship">Government Scholarship</option>
							<option value="Insurance purposes">Insurance purposes</option>
							<option value="Identity verification">Identity verification</option>
							<option value="Government Exams">Government Exams</option>
							<option value="Higher Studies">Higher Studies</option>
							<option value="Other">Other</option>
						</select>
					</div>
					<div class="mb-3 hidden" id="otherReasonField">
						<label for="otherReason" class="form-label">Other Reason *</label>
						<input type="text" id="otherReason" name="otherReason" class="form-control">
					</div>
					<div class="mb-3">
						<label for="IDProof" class="form-label">Upload ID Proof </label>
						<input type="file" id="IDProof" name="IDProof" class="form-control"
							accept=".pdf, .jpeg, .png, .jpg" required>
					</div>
					<button type="button" class="btn btn-custom" onclick="prevStep('step2')">Previous</button>
					<button type="submit" class="btn btn-custom">Submit</button>
			</form>
		</div>
		<script>
			var currentStep = 1;

			function nextStep(stepId) {
				if (validateForm()) {
					document.getElementById('step' + currentStep).classList.remove('active');
					currentStep++;
					document.getElementById(stepId).classList.add('active');
				} else {
					alert("Please fill in all required fields correctly.");
				}
			}

			function prevStep(stepId) {
				document.getElementById('step' + currentStep).classList.remove('active');
				currentStep--;
				document.getElementById(stepId).classList.add('active');
			}

			function toggleAdmissionFields() {
				var feesReceiptChoice = document.querySelector('input[name="fees"]:checked').value;
				var adtypeField = document.getElementById('adtypeField');
				var firstGraduateField = document.getElementById('firstGraduateField');
				var categoryField = document.getElementById('categoryField');

				if (feesReceiptChoice === 'Yes') {
					adtypeField.classList.remove('hidden');
					firstGraduateField.classList.remove('hidden');
					categoryField.classList.remove('hidden');
					enableFields(adtypeField);
					enableFields(firstGraduateField);
					enableFields(categoryField);
				} else {
					adtypeField.classList.add('hidden');
					firstGraduateField.classList.add('hidden');
					categoryField.classList.add('hidden');
					disableFields(adtypeField);
					disableFields(firstGraduateField);
					disableFields(categoryField);
				}
			}

			function enableFields(container) {
				var inputs = container.querySelectorAll('input');
				inputs.forEach(function (input) {
					input.disabled = false;
				});
			}

			function disableFields(container) {
				var inputs = container.querySelectorAll('input');
				inputs.forEach(function (input) {
					input.disabled = true;
				});
			}

			// Call the function initially to set the state based on the default selected radio button
			toggleAdmissionFields();

			function toggleYearOptions() {
				var yearSelect = document.getElementById('year');
				var lateralStudentSection = document.getElementById('lateralStudentSection');

				if (yearSelect.value === 'Second Year' || yearSelect.value === 'Third Year' || yearSelect.value === 'Fourth Year') {
					lateralStudentSection.style.display = 'block';
				} else {
					lateralStudentSection.style.display = 'none';
				}
			}

			function toggleHostelTutorEmailField() {
				var hostellerRadio = document.getElementById('hosteller');
				var hostelTutorEmailField = document.getElementById('hostelTutorEmailField');

				if (hostellerRadio.checked) {
					hostelTutorEmailField.style.display = 'block';
				} else {
					hostelTutorEmailField.style.display = 'none';
				}
			}

			function toggleReasonField() {
				var reasonSelect = document.getElementById('reason');
				var otherReasonField = document.getElementById('otherReasonField');

				if (reasonSelect.value === 'Other') {
					otherReasonField.style.display = 'block';
				} else {
					otherReasonField.style.display = 'none';
				}
			}

			function validateForm() {
				var rollnumber = document.getElementById('rollnumber').value;
				var studentmail = document.getElementById('studentmail').value;

				var rollnumberPattern = /^[0-9]+$/;
				var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

				if (!rollnumberPattern.test(rollnumber)) {
					return false;
				}

				if (!emailPattern.test(studentmail)) {
					return false;

				}

				return true;
			}
			// Function to show a toast notification and redirect
			function showNotificationAndRedirect() {
				toastr.success('Form submitted successfully!');
				setTimeout(function () {
					window.location.href = 'secondpageug.php'; // Redirect after 2 seconds (adjust as needed)
				}, 0.5);
			}

			// Call the function when the form is submitted
			document.getElementById('studentForm').addEventListener('submit', function (event) {
				event.preventDefault(); // Prevent the default form submission
				if (validateForm()) {
					showNotificationAndRedirect();
				} else {
					alert("Please fill in all required fields correctly.");
				}
			});

			function goBack() {
				window.history.back();
			}
		</script>

	</body>

</html>