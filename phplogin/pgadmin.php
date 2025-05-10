<?php
require 'PHPMailerAutoload.php';
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "hackathon";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$mail = new PHPMailer(true);

// Set mailer to use SMTP
$mail->isSMTP();

// Specify SMTP server details
$mail->SMTPDebug = 2;
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
$mail->SMTPAuth = true;
$mail->Username = 'asrec2024@gmail.com'; // SMTP username
$mail->Password = 'lvkd wjxs lelp dgap'; // SMTP password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// Set sender email address
$mail->setFrom('asrec2024@gmail.com', 'Admin@SREC');

// Retrieve data from the database
$sql = "SELECT name, rollnumber, year, hd, course, fees, reason, IDProof, studentmail FROM pgadmin";
$result = $conn->query($sql);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form was submitted
    if (isset($_POST['submit_verification'])) {
        foreach ($result as $row) {
            $rollnumber = $row['rollnumber'];
            // Check if Head of the Department checkbox is selected
            if (isset($POST["HeadOfDepartment$rollnumber"]) && !isCheckboxFilled($rollnumber, 'hod')) {
                $sql = "INSERT INTO pgtrack (rollnumber, hod) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE hod = 'verified'";
                $conn->query($sql);
            }

        }

        echo "Verification status updated successfully.";
    }
    if (isset($_POST['reject'])) {
        $rollnumber = $_POST['rollnumber'];

        // Fetch student email from the database
        $sql_fetch_email = "SELECT studentmail FROM pgadmin WHERE rollnumber = '$rollnumber'";
        $result_email = $conn->query($sql_fetch_email);

        if ($result_email && $result_email->num_rows > 0) {
            $row_email = $result_email->fetch_assoc();
            $studentmail = $row_email['studentmail'];

            // Check if the fetched email is not empty
            if (!empty($studentmail)) {
                // Delete the entry from the frontend
                // Store the entry in the rejection table
                $sql_rejection = "INSERT INTO rejection (rollnumber) VALUES ('$rollnumber')";
                if ($conn->query($sql_rejection) === TRUE) {
                    echo "Student with roll number $rollnumber has been rejected.";

                    // Send email to the student
                    try {
                        // Content
                        $mail->addAddress($studentmail); // Add recipient's email fetched from the form
                        $mail->isHTML(true);
                        $mail->Subject = 'Bonafide Registration Rejected';
                        $mail->Body = 'Your bonafide form is rejected!! Your bonafide registration did not meet our requirements. Kindly make necessary amendments and submit again.'; // Set email format to HTML
                        $mail->send();
                        echo '<script>Toastify({
                            text: "Form submitted successfully!",
                            duration: 0.5, // Toast duration in milliseconds
                            gravity: "bottom", // Toast position
                            backgroundColor: "#4CAF50", // Toast background color
                        }).showToast();</script>';
                        echo '<script>setTimeout(function(){ window.location.href = "adminpgmail.php"; }, 0.5);</script>';
                    } catch (Exception $e) {
                        // Show error message
                        echo '<script>Toastify({
                            text: "Error: ' . $mail->ErrorInfo . '",
                            duration: 0.5, // Toast duration in milliseconds
                            gravity: "bottom", // Toast position
                            backgroundColor: "#FF6347", // Toast background color
                        }).showToast();</script>';
                    }

                    // After email is sent, delete the entry from the ug table
                    $sql_delete_entry = "DELETE FROM pgadmin WHERE rollnumber = '$rollnumber'";
                    if ($conn->query($sql_delete_entry) === TRUE) {
                        echo "Entry deleted successfully.";
                    } else {
                        echo "Error deleting entry: " . $conn->error;
                    }
                } else {
                    echo "Error: " . $sql_rejection . "<br>" . $conn->error;
                }
            } else {
                echo "Error: Student email is empty.";
            }
        } else {
            echo "Error: No rows returned for the student email.";
        }
    }

    if (isset($_POST['approve'])) {
        $rollnumber = $_POST['rollnumber'];

        // Fetch details from the ug table
        $sql_fetch_details = "SELECT rollnumber, course, year, fees, reason, studentmail FROM pgadmin WHERE rollnumber = '$rollnumber'";
        $result_details = $conn->query($sql_fetch_details);

        if ($result_details && $result_details->num_rows > 0) {
            $row_details = $result_details->fetch_assoc();
            $course = $row_details['course'];
            $year = $row_details['year'];
            $reason = $row_details['reason'];
            $studentmail = $row_details['studentmail'];  // Assuming studentmail is also in the ug table

            // Proceed with approval
            $sql_approve = "INSERT INTO approvepg (rollnumber, course, year, reason) VALUES ('$rollnumber', '$course', '$year', '$reason')";
            if ($conn->query($sql_approve) === TRUE) {
                echo "Student with roll number $rollnumber has been approved.";
                $sql_delete_verification = "DELETE FROM verfication WHERE rollnumber = '$rollnumber'";
                if ($conn->query($sql_delete_verification) === TRUE) {
                    echo "Verification entry deleted successfully.";
                } else {
                    echo "Error deleting verification entry: " . $conn->error;
                }
                // Send email to the student
                try {
                    $mail->addAddress($studentmail);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Bonafide Registration Has Been Approved!';
                    $mail->Body = "Your bonafide registration has been approved! Feel free to swing by the administration office to collect your bonafide certificate. If you have any questions, reach out to us.";
                    $mail->send();
                    echo '<script>Toastify({
                        text: "Form submitted successfully!",
                        duration: 0.5, // Toast duration in milliseconds
                        gravity: "bottom", // Toast position
                        backgroundColor: "#4CAF50", // Toast background color
                    }).showToast();</script>';
                    echo '<script>setTimeout(function(){ window.location.href = "adminpgmail.php"; }, 0.5);</script>';
                } catch (Exception $e) {
                    // Show error message
                    echo '<script>Toastify({
                        text: "Error: ' . $mail->ErrorInfo . '",
                        duration: 0.5, // Toast duration in milliseconds
                        gravity: "bottom", // Toast position
                        backgroundColor: "#FF6347", // Toast background color
                    }).showToast();</script>';
                }

                // Delete the entry from the ug table
                $sql_delete_entry = "DELETE FROM pgadmin WHERE rollnumber = '$rollnumber'";
                if ($conn->query($sql_delete_entry) === TRUE) {
                    echo "Entry deleted successfully.";
                } else {
                    echo "Error deleting entry: " . $conn->error;
                }
            } else {
                echo "Error approving student: " . $conn->error;
            }
        } else {
            echo "Error: No matching entry found for roll number $rollnumber.";
        }
    }

}

function isCheckboxFilled($rollnumber, $type)
{
    global $conn;
    $sql = "SELECT $type FROM pgtrack WHERE rollnumber = '$rollnumber'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return ($row && $row[$type] === 'verified');
}

function disableAllCheckboxes($rollnumber)
{
    echo "<script>
            document.getElementById('HeadOfDepartment_$rollnumber').disabled = true;
          </script>";
}

function rejectStudent($rollnumber)
{
    global $conn;
    $conn->begin_transaction();
    $sql_rejection = "INSERT INTO rejection SELECT * FROM pgadmin WHERE rollnumber = '$rollnumber'";
    $conn->query($sql_rejection);
    $sql_delete = "DELETE FROM pgadmin WHERE rollnumber = '$rollnumber'";
    $conn->query($sql_delete);
    $conn->commit();
}
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SREC</title>
    <link rel="stylesheet" href="tab1.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .btn {
            padding: 5px 10px;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .checkbox-container {
            display: flex;
            justify-content: center;
            gap: 2px;
        }
    </style>
</head>

<body class="form-login-body">
    <div class="container-fluid">
        <div class="top-menu">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-12 logo">
                        <a class="navbar-brand text-success logo h1 align-self-center">
                            <img src="footer-logo.png" alt="Srec Logo" class="navbar-logo">
                        </a>
                    </div>
                    <div class="goBack">
                        <button onclick="goBack()">Home</button>
                    </div>
                    <div class="logout">
                        <button onclick="logout()">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Roll Number</th>
                <th>Year</th>
                <th>Course</th>
                <th>Hostel/Dayscholar</th>
                <th>Fees</th>
                <th>Reason</th>
                <th>Download</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['rollnumber']; ?></td>
                        <td><?php echo $row['year']; ?></td>
                        <td><?php echo $row['course']; ?></td>
                        <td><?php echo $row['hd']; ?></td>
                        <td><?php echo $row['fees']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td><a href="<?php echo $row['IDProof']; ?>" class="btn btn-primary download-btn" download>Download</a></td>
                        <td>
                            <div class="checkbox-container">
                                <input type="checkbox" id="HeadOfDepartment_<?php echo $row['rollnumber']; ?>"
                                       name="HeadOfDepartment_<?php echo $row['rollnumber']; ?>" value="HeadOfDepartment"
                                    <?php echo (isCheckboxFilled($row['rollnumber'], 'hod')) ? 'disabled' : ''; ?>> Head of the Department
                            </div>
                        </td>
                        <td>
                           <form id="rejectForm_<?php echo $row['rollnumber']; ?>" action="" method="post"
                                style="display: inline-block;">
                                <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                <button type="submit" class="btn btn-primary download-btn" name="reject">Reject</button>
                            </form>
                            <div id="buttonContainer_<?php echo $row['rollnumber']; ?>" style="display: flex;">
                                <form action="samplepdf.php" method="post" style="display: flex;">
                                    <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                    <button type="submit" class="btn btn-primary download-btn">Generate PDF</button>
                                </form>
                                <form action="feespdf.php" method="post" style="display: flex;">
                                    <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                    <input type="hidden" name="year" value="<?php echo $row['year']; ?>">
                                    <input type="hidden" name="hd" value="<?php echo $row['hd']; ?>">
                                    <button type="submit" class="btn btn-primary download-btn">GeneratePDFfees</button>
                                </form>
                                <form id="approveForm_<?php echo $row['rollnumber']; ?>" action="" method="post"
                                    style="display: inline-block;">
                                    <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                    <button type="submit" class="btn btn-primary download-btn" name="approve">Approve</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="10">No files uploaded yet.</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <script>
        function goBack() {
            window.history.back();
        }
        function logout() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "logout.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        alert('Session destroyed successfully');
                        window.location.href = 'slogin.php';
                    } else {
                        alert('Failed to destroy session');
                    }
                }
            };
            xhr.send();
        }
    </script>
</body>

</html>