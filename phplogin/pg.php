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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $rollnumber = filter_input(INPUT_POST, 'rollnumber', FILTER_SANITIZE_STRING);
    $studentmail = filter_input(INPUT_POST, 'studentmail', FILTER_SANITIZE_EMAIL);
    $course = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_STRING);
    $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_STRING);
    $hd = '';
    $htmail = filter_input(INPUT_POST, 'hostelTutorEmail', FILTER_SANITIZE_EMAIL);
    $hmail = filter_input(INPUT_POST, 'hodEmail', FILTER_SANITIZE_EMAIL);
    $fees = filter_input(INPUT_POST, 'fees', FILTER_SANITIZE_STRING);
    $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
    $IDProof = '';
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
    // Check if there's no error with file upload
    if ($IDProof['error'] == 0) {
        try {
            $pdo->beginTransaction(); // Start a transaction

            $stmtPg = $pdo->prepare("INSERT INTO pg (name, rollnumber, studentmail, course, year, hd, htmail, hmail, fees, reason, IDProof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtPg->execute([$name, $rollnumber, $studentmail, $course, $year, $hd, $htmail, $hmail, $fees, $reason, $target_file]);
            $stmtPgAdmin = $pdo->prepare("INSERT INTO pgadmin (name, rollnumber, studentmail, course, year, hd, htmail, hmail, fees, reason, IDProof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtPgAdmin->execute([$name, $rollnumber, $studentmail, $course, $year, $hd, $htmail, $hmail, $fees, $reason, $target_file]);

            // Commit transaction
            $pdo->commit();
            $recipients = array_filter([$htmail, $hmail]);
            sendEmail($recipients, $name, $rollnumber, $studentmail, $course, $reason, $target_file);
        } catch (PDOException $ex) {
            // Roll back transaction if something went wrong
            $pdo->rollBack();
            echo "Database error: " . $ex->getMessage();
        }
    } else {
        echo "Error with file upload.";
    }
}

function sendEmail($recipients, $name, $rollnumber, $studentmail, $course, $reason, $target_file)
{
    $mail = new PHPMailer(true); // Passing `true` enables exceptions
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
        $mail->Body = "A new student with name $name, roll number $rollnumber, and email $studentmail has registered for the course $course. Reason: $reason"; // Email body content

        $mail->send();
        echo '<script>Toastify({
            text: "Form submitted successfully!",
            duration: 0.5, // Toast duration in milliseconds
            gravity: "bottom", // Toast position
            backgroundColor: "#4CAF50", // Toast background color
        }).showToast();</script>';
        echo '<script>setTimeout(function(){ window.location.href = "secondpagepg.php"; }, 0.5);</script>';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Student Details Form</title>
    <style>
        /* Your CSS styles */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            height: 100vh;
            background: url("https://img.freepik.com/free-vector/realistic-white-golden-geometric-background_79603-2032.jpg?t=st=1711358975~exp=1711362575~hmac=f4ad1cdcc90fe5a26b93ecabe945300e7930e773fb7ff1587c371c259aeed228&w=996") center/cover no-repeat;
            font-family: "Euclid Circular A", "Poppins";
        }

        .container {
            background-color: #f8f8f8;
            /* Background color with opacity */
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 700px;
            /* Adjust width as needed */
            max-width: 100%;
            position: relative;
            /* Ensure the container is a positioned parent */
        }

        /* Center the form heading */
        .container h2 {
            text-align: center;
        }

        /* Style the text box border */
        input[type="text"],
        input[type="email"],
        input[type="file"],
        select {
            border: 1px solid #ccc;
            /* Add a 1px solid border with color #ccc */
            border-radius: 4px;
            /* Add border-radius for rounded corners */
            padding: 8px;
            /* Add padding for better visual appearance */
            width: 100%;
            /* Make the text boxes 100% width */
            box-sizing: border-box;
            /* Include padding and border in the element's total width and height */
            margin-bottom: 10px;
            /* Add margin to separate text boxes */
        }

        /* Change the border color */
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="file"]:focus,
        select:focus {
            border-color: #e27509;
            /* Change the border color on focus */
        }

        label[for="tutorEmail"],
        label[for="alternateTutorEmail"],
        label[for="academicCoordinatorEmail"] {
            display: block;
            /* Initially hide the labels */
        }

        .step {
            display: none;
            /* Hide all steps by default */
        }

        .step.active {
            display: block;
            /* Show the active step */
        }

        .btn-custom {
            /* Your custom styles here */
            background-color: #e27509;
            /* Blue background color */
            color: #fff;
            /* White text color */
            border: none;
            /* No border */
            padding: 10px 20px;
            /* Padding for better appearance */
            border-radius: 25px;
            /* Rounded corners */
            cursor: pointer;
            /* Change cursor to pointer on hover */
        }

        .btn-custom:hover {
            background-color: #86add5;
            /* Darker blue on hover */
        }

        /* Custom CSS for the button */
    </style>

</head>

<body>
    <div class="container mt-5">
        <form id="studentForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data"
            onsubmit="return validateForm()">
            <h2 class="mb-4">Student Details Form</h2>
            <!-- Step 1: Personal Information -->
            <div class="step active" id="step1">
                <div class="mb-3">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="rollnumber" class="form-label">Roll Number *</label>
                    <input type="text" id="rollnumber" name="rollnumber" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="studentmail" class="form-label">Student Email *</label>
                    <input type="email" id="studentmail" name="studentmail" class="form-control" required>
                </div>
                <div class="mb-3" id="courseContainer">
                    <label for="course" class="form-label">Course *</label>
                    <select id="course" name="course" class="form-select" required>
                        <option value="">Select Course</option>
                        <option value="M.E. Artificial Intelligence and Data Science">M.E. Artificial Intelligence and
                            Data Science</option>
                        <option value="M.Tech. Robotics and Artificial Intelligence">M.Tech. Robotics and Artificial
                            Intelligence</option>
                        <option value="M.E. VLSI Design">M.E. VLSI Design</option>
                        <option value="M.Tech. Nanoscience and Technology">M.Tech. Nanoscience and Technology</option>
                        <option value="M.E. Control and Instrumentation Engineering">M.E. Control and Instrumentation
                            Engineering</option>
                        <option value="M.E. Embedded System Technologies">M.E. Embedded System Technologies</option>
                        <option value="MBA Master of Business Administration">MBA Master of Business Administration
                        </option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year *</label>
                    <select id="year" name="year" class="form-select" required onchange="toggleYearOptions()">
                        <option value="">Select Year</option>
                        <option value="First Year">First Year</option>
                        <option value="Second Year">Second Year</option>
                    </select>
                </div>
                <button type="button" class="btn btn-custom" onclick="nextStep('step2')">Next</button>
            </div>
            <!-- Step 2: Additional Information -->
            <div class="step" id="step2">
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
                    <label for="hodEmail" class="form-label">Head of Department's Email *</label>
                    <input type="email" id="hodEmail" name="hodEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="FeesReceipt">Do you want the bonafide with Fees Receipt?</label><br>
                    <input type="radio" id="yesFeesReceipt" name="fees" value="Yes" required>
                    <label for="yesFeesReceipt">Yes</label><br>
                    <input type="radio" id="noFeesReceipt" name="fees" value="No" required>
                    <label for="noFeesReceipt">No</label><br>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason *</label>
                    <select id="reason" name="reason" class="form-select" required onchange="toggleReasonField()">
                        <option value="">Select Reason</option>
                        <option value="Passport verification">Passport verification</option>
                        <option value="Educational loan">Educational loan</option>
                        <option value="Tamilnadu Labour Welfare Scheme Scholarship">Tamilnadu Labour Welfare Scheme
                            Scholarship</option>
                        <option value="Internship">Intership</option>
                        <option value="Government Scholarship">Government Scholarship</option>
                        <option value="Insurance purposes">Insurance purposes</option>
                        <option value="Identity verification">Identity verification</option>
                        <option value="Government Exams">Government Exams</option>
                        <option value="Higher Studies">Higher Studies</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3" id="otherReasonField" style="display: none;">
                    <label for="otherReason" class="form-label">Other Reason *</label>
                    <input type="text" id="otherReason" name="otherReason" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="IDProof" class="form-label">Upload ID Proof </label>
                    <input type="file" id="IDProof" name="IDProof" class="form-control" accept=".pdf, .jpeg, .png, .jpg"
                        required>
                </div>
                <button type="button" class="btn btn-custom" onclick="prevStep('step1')">Previous</button>
                <button type="submit" class="btn btn-custom">Submit</button>
            </div>
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
            var admissionFields = document.querySelectorAll('#adtypeField input[type="radio"], #yesFirstGraduate, #noFirstGraduate, #oc, #sc');

            if (feesReceiptChoice === 'No') {
                admissionFields.forEach(function (element) {
                    element.disabled = true;
                });
            } else {
                admissionFields.forEach(function (element) {
                    element.disabled = false;
                });
            }
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
            var studentmail = document.getElementById('studentmail').value;

            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if (!emailPattern.test(studentmail)) {
                return false;

            }

            return true;
        }

        // Function to show a toast notification and redirect
        function showNotificationAndRedirect() {
            toastr.success('Form submitted successfully!');
            setTimeout(function () {
                window.location.href = 'pgfront.php'; // Redirect after 2 seconds (adjust as needed)
            }, 0.5);
        }


    </script>

</body>

</html>