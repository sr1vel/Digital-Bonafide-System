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

// Create a PHPMailer instance
$mail = new PHPMailer(true);

// Set mailer to use SMTP
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
$mail->SMTPAuth = true;
$mail->Username = 'asrec2024@gmail.com'; // SMTP username
$mail->Password = 'lvkd wjxs lelp dgap'; // SMTP password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom('asrec2024@gmail.com', 'Admin@SREC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reject'])) {
        $rollnumber = $_POST['rollnumber'];

        // Fetch student details from the database
        $sql_fetch_details = "SELECT studentmail, course, year, reason FROM student WHERE rollnumber = ?";
        $stmt_fetch_details = $conn->prepare($sql_fetch_details);
        $stmt_fetch_details->bind_param("s", $rollnumber);
        $stmt_fetch_details->execute();
        $result_details = $stmt_fetch_details->get_result();

        if ($result_details && $result_details->num_rows > 0) {
            $row_details = $result_details->fetch_assoc();
            $studentmail = $row_details['studentmail'];
            $course = $row_details['course'];
            $year = $row_details['year'];
            $reason = $row_details['reason'];


            if (!empty($studentmail)) {
                $sql_rejection = "INSERT INTO rejection (rollnumber, course, year, reason) VALUES ('$rollnumber', '$course', '$year', '$reason')";
                if ($conn->query($sql_rejection) === TRUE) {
                    echo "Student with roll number $rollnumber has been rejected.";
                    $sql_delete_verification = "DELETE FROM verfication WHERE rollnumber = '$rollnumber'";
                    if ($conn->query($sql_delete_verification) === TRUE) {
                        echo "Verification entry deleted successfully.";
                    } else {
                        echo "Error deleting verification entry: " . $conn->error;
                    }
                
                    try {
                        $mail->addAddress($studentmail);
                        $mail->isHTML(true);
                        $mail->Subject = 'Bonafide Registration Rejected';
                        $mail->Body = 'Your bonafide form is rejected! Your bonafide registration did not meet our requirements. Kindly make necessary amendments and submit again.';
                        $mail->send();
                        echo '<script>Toastify({
                            text: "Form submitted successfully!",
                            duration: 3000,
                            gravity: "bottom",
                            backgroundColor: "#4CAF50",
                        }).showToast();</script>';
                        echo '<script>setTimeout(function(){ window.location.href = "sampleadmin.php"; }, 3000);</script>';
                    } catch (Exception $e) {
                        echo '<script>Toastify({
                            text: "Error: ' . $e->getMessage() . '",
                            duration: 3000,
                            gravity: "bottom",
                            backgroundColor: "#FF6347",
                        }).showToast();</script>';
                    }

                    $sql_delete_entry = "DELETE FROM student WHERE rollnumber = '$rollnumber'";
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

        $sql_fetch_details = "SELECT rollnumber, course, year, hd, adtype, reason, studentmail FROM student WHERE rollnumber = '$rollnumber'";
        $result_details = $conn->query($sql_fetch_details);

        if ($result_details && $result_details->num_rows > 0) {
            $row_details = $result_details->fetch_assoc();
            $course = $row_details['course'];
            $year = $row_details['year'];
            $reason = $row_details['reason'];
            $studentmail = $row_details['studentmail'];
            $hd = $row_details['hd'];
            $adtype = $row_details['adtype'];

            $sql_approve = "INSERT INTO approvecurrent (rollnumber, course, year, reason) VALUES ('$rollnumber', '$course', '$year', '$reason')";
            if ($conn->query($sql_approve) === TRUE) {
                echo "Student with roll number $rollnumber has been approved.";
                $sql_delete_verification = "DELETE FROM verfication WHERE rollnumber = '$rollnumber'";
                if ($conn->query($sql_delete_verification) === TRUE) {
                    echo "Verification entry deleted successfully.";
                } else {
                    echo "Error deleting verification entry: " . $conn->error;
                }
                try {
                    $mail->addAddress($studentmail);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Bonafide Registration Has Been Approved!';
                    $mail->Body = "Your bonafide registration has been approved! Feel free to swing by the administration office to collect your bonafide certificate. If you have any questions, reach out to us. Thank you, Admin";
                    $mail->send();
                    echo '<script>Toastify({
                        text: "Form submitted successfully!",
                        duration: 3000,
                        gravity: "bottom",
                        backgroundColor: "#4CAF50",
                    }).showToast();</script>';
                    echo '<script>setTimeout(function(){ window.location.href = "sampleadmin.php"; }, 3000);</script>';
                } catch (Exception $e) {
                    echo '<script>Toastify({
                        text: "Error: ' . $e->getMessage() . '",
                        duration: 3000,
                        gravity: "bottom",
                        backgroundColor: "#FF6347",
                    }).showToast();</script>';
                }

                $sql_delete_entry = "DELETE FROM student WHERE rollnumber = '$rollnumber'";
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

// Retrieve data from the database
$sql = "SELECT name, rollnumber, year, hd, adtype, course, fees, reason, IDProof, studentmail FROM student";
$result = $conn->query($sql);

// Process verification form submission
if (isset($_POST['submit_verification'])) {
    foreach ($result as $row) {
        $rollnumber = $row['rollnumber'];

        // Check if all checkboxes are filled, then disable all
        if (isCheckboxFilled($rollnumber, 'Tutor') && isCheckboxFilled($rollnumber, 'AcademicCoordinator') && isCheckboxFilled($rollnumber, 'HeadOfDepartment')) {
            disableAllCheckboxes($rollnumber);
        }

        // Check if Tutor checkbox is selected
        if (isset($POST["Tutor$rollnumber"]) && !isCheckboxFilled($rollnumber, 'Tutor')) {
            $sql = "INSERT INTO verfication (rollnumber, Tutor) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE Tutor = 'verified'";
            $conn->query($sql);
        }

        // Check if Academic Coordinator checkbox is selected
        if (isset($POST["AcademicCoordinator$rollnumber"]) && !isCheckboxFilled($rollnumber, 'AcademicCoordinator')) {
            $sql = "INSERT INTO verfication (rollnumber, AcademicCoordinator) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE AcademicCoordinator = 'verified'";
            $conn->query($sql);
        }

        // Check if Head of the Department checkbox is selected
        if (isset($POST["HeadOfDepartment$rollnumber"]) && !isCheckboxFilled($rollnumber, 'HeadOfDepartment')) {
            $sql = "INSERT INTO verfication (rollnumber, HeadOfDepartment) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE HeadOfDepartment = 'verified'";
            $conn->query($sql);
        }

        // Check if Hostel Tutor checkbox is selected
        if (isset($POST["Hosteltutor$rollnumber"]) && !isCheckboxFilled($rollnumber, 'Hosteltutor')) {
            $sql = "INSERT INTO verfication (rollnumber, Hosteltutor) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE Hosteltutor = 'verified'";
            $conn->query($sql);
        }
    }

    echo "Verification status updated successfully.";
}

function isCheckboxFilled($rollnumber, $type)
{
    global $conn;
    $sql = "SELECT $type FROM verfication WHERE rollnumber = '$rollnumber'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return ($row && $row[$type] === 'verified');
}

function disableAllCheckboxes($rollnumber)
{
    echo "<script>
            document.getElementById('Tutor_$rollnumber').disabled = true;
            document.getElementById('AcademicCoordinator_$rollnumber').disabled = true;
            document.getElementById('HeadOfDepartment_$rollnumber').disabled = true;
          </script>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SREC</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        * {
    margin: 0;
    padding: 0;
    list-style: none;
    box-sizing: border-box; /* Add this to include padding and border in element's total width and height */
  }
  
  html, body {
    height: 100%; /* Ensure the body takes up the full height */
  }
  
  .align-left {
    text-align: left;
  }
  
  .login-img img {
    margin-left: 0px; /* Adjust the value as needed to move the image to the left */
  }
  
  .form-login-body .top-menu {
    box-shadow: 0 0 10px 0 #00000026;
    background-color: #092a47;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    padding: 10px 0; /* Maintain a reasonable padding for the header */
  }
  
  .form-login-body .top-menu .logo img {
    max-width: 400px; /* Increase the size of the logo */
    width: 100%;
    height: auto;
    margin-top: 7px;
    margin-left: auto;
    margin-right: auto;
    display: block;
  }
  /* Navigation Bar Styles */
  .navbar {
    background-color: #ffffff; /* White background for the navbar */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-left: -50px;
    margin-right: 10px; /* Subtle shadow for elevation */
  }
  
  .navbar .navbar-brand {
    font-weight: bold;
    font-size: 1.5rem;
    color: #092a47; /* Logo text color */
  }
  
  .navbar .navbar-brand:hover {
    color: #092a47; /* Darker color on hover */
  }
  
  .navbar-nav .nav-item .nav-link {
    font-family: 'Roboto', sans-serif;
    font-weight: 500;
    color: #333333; /* Link color */
    padding: 0rem 1rem;
  }
  
  .navbar-nav .nav-item .nav-link:hover {
    color: #092a47; /* Link color on hover */
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
  
  /* Media queries for responsiveness */
  @media (max-width: 748px) {
    .form-login-body .top-menu .logo img {
        max-width: 350px; /* Adjust size for small screens */
    }
  }
  
  @media (min-width: 749px) {
    .form-login-body .top-menu .logo img {
        margin-left: -100px; /* Original margin-left for larger screens */
        max-width: 1000px; /* Ensuring the logo is appropriately sized */
    }
  }



.background {
    position: fixed;
    top: -50vmin;
    left: -50vmin;
    width: 100vmin;
    height: 100vmin;
    border-radius: 50%;
}

.mt-5 {
    margin-top: 85px; /* Adjust the margin-top to ensure it's right under the navbar */
}


.card {
    border: none;
    border-radius: 25px;
    background: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.card > h2 {
    font-size: 20px;
    font-weight: bold;
    font-family: "Roboto", sans-serif;
    margin: 0 0 20px;
    color: rgb(0, 0, 0);
}

.form {
    margin: 0 0 20px;
    display: grid;
    font-family: "Roboto", sans-serif;
    gap: 8px;
    width:100%;
}

.form :is(input, button) {
    width: 100%;
    height: 40px;
    border-radius: 20px;
    font-size: 12px;
    font-family: inherit;
}

.form > input {
    border: 0;
    padding: 0 10px;
    color: #222222;
    background: #ededed;
}

.form > input::placeholder {
    color: rgba(0, 0, 0, 0.28);
}

.form > button {
    border: 0;
    color: #f9f9f9;
    background:#092a47 ;
    display: center;
    font-family: "Roboto", sans-serif;
    place-items: center;
    font-weight: 500;
    cursor: pointer;
    font-size: 14px;
    width:40px;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}

.table th,
.table td {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}

.table tbody + tbody {
    border-top: 2px solid #dee2e6;
}

.table .table {
    background-color: #fff;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

.table-bordered thead th,
.table-bordered thead td {
    border-bottom-width: 2px;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}

.logout-btn {
    position: absolute;
    top: 10px;
    right: 10px;
}

.logout-btn,
.goBack button,
.logout button {
    background-color: #ffffff;
    border-color: #ffffff;
    color: #000000;
    padding: 8px 16px;
    border-radius: 20px;
    font-family: "Poppins";
    cursor: pointer;
    transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
}

.logout-btn:hover,
.goBack button:hover,
.logout button:hover {
    background-color: #206aab !important;
    border-color: #206aab !important;
    color: #ffffff;
}
.custom-btn {
    background-color: #ffffff; /* Red background */
    color: rgb(6, 2, 2); /* White text */
    border: none; /* No border */
    padding: 10px 20px; /* Adjust padding as needed */
    cursor: pointer; /* Pointer cursor on hover */
    border-radius: 20px;
    background-color: #092a47;
    border-color: #092a47;
    color: #fff; /* Rounded corners */
    font-size: 1.0rem;
    font-family: "Roboto", sans-serif;
  }
  
.custom-btn:hover {
    background-color: #206aab !important;
    border-color: #206aab !important;
    color: #ffffff !important;
}


.download-btn-container {
    display: flex;
    justify-content: center;
    margin-top: 60px;
}

.btn-primary.download-btn {
    background-color: #092a47;
    border-color:#092a47;
    color: #fff;
    padding: 8px 16px;
    border-radius: 15px;
    text-decoration: none;
    height:fit-content;
    width:150px;
}

.btn-primary.download-btn:hover {
    background-color: #206aab;
    border-color: #206aab;
    color: #fff;
}

.form input[type="checkbox"] {
    transform: scale(0.4);
}

.btn-primary:active,
.btn-primary:focus,
.btn-primary:hover {
    background-color: #092a47 !important;
    border-color: #ffffff !important;
    color: #ffffff !important;
}

.goBack {
    position: absolute;
    top: 10px;
    right: 100px;
}

.logout {
    position: absolute;
    top: 10px;
    right: 10px;
}
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        th,
        td {
            padding: 10px;
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

        .action-buttons form {
            display: inline-block;
            margin-right: 5px;
        }

        .checkbox-container {
            display: flex;
            justify-content: center;
            gap: 5px; /* Adjust gap as needed */
        }

        .btn-container {
            display: flex;
        }

        .btn-column {
            display: flex;
            flex-direction: column;
            margin-right: 10px; /* Adjust spacing between columns as needed */
        }

        .btn-column form, .btn-column button {
            margin-bottom: 10px; /* Adjust spacing between buttons as needed */
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
                            <img src="https://srec.ac.in/themes/frontend/images/footer-logo.png" alt="Srec Logo" class="navbar-logo">
                        </a>
                    </div>
                    <div class="goBack">
                        <button onclick="goBack()">Home</button>
                    </div>
                    <div class="logout">
                        <button onclick="logout()">logout</button>
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
                <th>Admission type</th>
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
                        <td><?php echo $row['adtype']; ?></td>
                        <td><?php echo $row['fees']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td><a href="<?php echo $row['IDProof']; ?>" class="btn btn-primary download-btn" download>Download</a></td>
                        <td>
                            <div class="checkbox-container">
                                <div style="display: inline-block; margin-right: 20px;">
                                    <label for="Tutor_<?php echo $row['rollnumber']; ?>">Tutor/Alternate tutor</label><br>
                                    <input type="checkbox" id="Tutor_<?php echo $row['rollnumber']; ?>"
                                           name="Tutor_<?php echo $row['rollnumber']; ?>" value="Tutor"
                                        <?php echo (isCheckboxFilled($row['rollnumber'], 'Tutor')) ? ' checked disabled' : ''; ?>>
                                </div>
                                <div style="display: inline-block; margin-right: 40px;">
                                    <label for="AcademicCoordinator_<?php echo $row['rollnumber']; ?>">Academic Coordinator</label><br>
                                    <input type="checkbox" id="AcademicCoordinator_<?php echo $row['rollnumber']; ?>"
                                           name="AcademicCoordinator_<?php echo $row['rollnumber']; ?>" value="AcademicCoordinator"
                                        <?php echo (isCheckboxFilled($row['rollnumber'], 'AcademicCoordinator')) ? ' checked disabled' : ''; ?>>
                                </div>
                                <div style="display: inline-block; margin-right: 60px;">
                                    <label for="HeadOfDepartment_<?php echo $row['rollnumber']; ?>">Head of the Department</label><br>
                                    <input type="checkbox" id="HeadOfDepartment_<?php echo $row['rollnumber']; ?>"
                                           name="HeadOfDepartment_<?php echo $row['rollnumber']; ?>" value="HeadOfDepartment"
                                        <?php echo (isCheckboxFilled($row['rollnumber'], 'HeadOfDepartment')) ? ' checked disabled' : ''; ?>>
                                </div>
                                <div style="display: inline-block; margin-right:80px;">
                                    <label for="Hosteltutor_<?php echo $row['rollnumber']; ?>">Hostel Tutor</label><br>
                                    <input type="checkbox" id="Hosteltutor_<?php echo $row['rollnumber']; ?>"
                                           name="Hosteltutor_<?php echo $row['rollnumber']; ?>" value="Hosteltutor"
                                        <?php echo (isCheckboxFilled($row['rollnumber'], 'hosteltutor')) ? ' checked disabled' : ''; ?>>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form id="rejectForm_<?php echo $row['rollnumber']; ?>" action="" method="post"
                                style="display: inline-block;">
                                <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                <button type="submit" class="btn btn-primary download-btn" name="reject">Reject</button>
                            </form>
                            <br>
                            <div id="buttonContainer_<?php echo $row['rollnumber']; ?>" style="display: inline-block;">
                                <form action="mypdf.php" method="post" style="display: inline-block;">
                                    <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                    <button type="submit" class="btn btn-primary download-btn">Generate PDF</button><br>
                                </form>
                                <br>
                                <form action="2daypdf.php" method="post" style="display: inline-block;">
                                    <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                    <input type="hidden" name="year" value="<?php echo $row['year']; ?>">
                                    <input type="hidden" name="hd" value="<?php echo $row['hd']; ?>">
                                    <button type="submit" class="btn btn-primary download-btn">Generate PDF fees</button>
                                </form>
                                <br>
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
            <tbody>
                <?php
                // Display the student details
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['rollnumber']; ?></td>
                            <td><?php echo $row['year']; ?></td>
                            <td><?php echo $row['course']; ?></td>
                            <td><?php echo $row['hd']; ?></td>
                            <td><?php echo $row['adtype']; ?></td>
                            <td><?php echo $row['fees']; ?></td>
                            <td><?php echo $row['reason']; ?></td>
                            <td><a href="<?php echo $row['IDProof']; ?>" class="btn btn-primary download-btn" download>Download</a></td>
                            <td>
                                <div class="checkbox-container">
                                    <div style="display: inline-block; margin-right: 20px;">
                                        <label for="Tutor_<?php echo $row['rollnumber']; ?>">Tutor</label><br>
                                        <input type="checkbox" id="Tutor_<?php echo $row['rollnumber']; ?>"
                                               name="Tutor_<?php echo $row['rollnumber']; ?>" value="Tutor"
                                            <?php echo (isCheckboxFilled($row['rollnumber'], 'Tutor')) ? ' checked disabled' : ''; ?>>
                                    </div>
                                    <div style="display: inline-block; margin-right: 40px;">
                                        <label for="AcademicCoordinator_<?php echo $row['rollnumber']; ?>">Academic Coordinator</label><br>
                                        <input type="checkbox" id="AcademicCoordinator_<?php echo $row['rollnumber']; ?>"
                                               name="AcademicCoordinator_<?php echo $row['rollnumber']; ?>" value="AcademicCoordinator"
                                            <?php echo (isCheckboxFilled($row['rollnumber'], 'AcademicCoordinator')) ? ' checked disabled' : ''; ?>>
                                    </div>
                                    <div style="display: inline-block; margin-right: 60px;">
                                        <label for="HeadOfDepartment_<?php echo $row['rollnumber']; ?>">Head of the Department</label><br>
                                        <input type="checkbox" id="HeadOfDepartment_<?php echo $row['rollnumber']; ?>"
                                               name="HeadOfDepartment_<?php echo $row['rollnumber']; ?>" value="HeadOfDepartment"
                                            <?php echo (isCheckboxFilled($row['rollnumber'], 'HeadOfDepartment')) ? ' checked disabled' : ''; ?>>
                                    </div>
                                    <div style="display: inline-block; margin-right:80px;">
                                        <label for="Hosteltutor_<?php echo $row['rollnumber']; ?>">Hostel Tutor</label><br>
                                        <input type="checkbox" id="Hosteltutor_<?php echo $row['rollnumber']; ?>"
                                               name="Hosteltutor_<?php echo $row['rollnumber']; ?>" value="Hosteltutor"
                                            <?php echo (isCheckboxFilled($row['rollnumber'], 'hosteltutor')) ? ' checked disabled' : ''; ?>>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form id="rejectForm_<?php echo $row['rollnumber']; ?>" action="" method="post"
                                    style="display: inline-block;">
                                    <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                    <button type="submit" class="btn btn-danger" name="reject">Reject</button>
                                </form>
                                <div id="buttonContainer_<?php echo $row['rollnumber']; ?>" style="display: inline-block;">
                                    <form action="mypdf.php" method="post" style="display: inline-block;">
                                        <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                        <button type="submit" class="btn btn-success">Generate PDF</button>
                                    </form>
                                    <form action="2daypdf.php" method="post" style="display: inline-block;">
                                        <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                        <input type="hidden" name="year" value="<?php echo $row['year']; ?>">
                                        <input type="hidden" name="hd" value="<?php echo $row['hd']; ?>">
                                        <button type="submit" class="btn btn-success">Generate PDF fees</button>
                                    </form>
                                    <form id="approveForm_<?php echo $row['rollnumber']; ?>" action="" method="post"
                                        style="display: inline-block;">
                                        <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                                        <button type="submit" class="btn btn-success" name="approve">Approve</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </form>

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
                    // Check if the session is destroyed
                    if (xhr.responseText.trim() === 'success') {
                        alert('Session destroyed successfully');
                        // Redirect to another page
                        window.location.href = 'slogin.php'; // Redirect to login page
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