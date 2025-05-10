
<?php
session_start(); // Start the session

// Check if the username is set in the session
if(isset($_SESSION['username'])) {
    // Display the username
    echo "Username: " . $_SESSION['username'];
} else {
    // If username is not set, display a message
    echo "Username is not set in the session.";
}


// Database connection details
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "hackathon";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the uploaded files from the database if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM hod WHERE hmail = '$username'";
    $result = $conn->query($sql);
} else {
    // Redirect to login page if not logged in
    header("Location: slogin.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_verification'])) {
    // Check if the form was submitted
    foreach ($result as $row) {
        $rollnumber = $row['rollnumber'];

        // Check if Tutor checkbox is selected
        if (isset($_POST["Tutor_$rollnumber"]) && !isCheckboxFilled($rollnumber, 'Tutor')) {
            // Update Tutor status
            $sql = "INSERT INTO verfication (rollnumber, Tutor) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE Tutor = 'verified'";
            $conn->query($sql);
        }

        // Check if Academic Coordinator checkbox is selected
        if (isset($_POST["AcademicCoordinator_$rollnumber"]) && !isCheckboxFilled($rollnumber, 'AcademicCoordinator')) {
            // Update Academic Coordinator status in the same row
            $sql = "INSERT INTO verfication (rollnumber, AcademicCoordinator) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE AcademicCoordinator = 'verified'";
            $conn->query($sql);
        }
// Check if Academic Coordinator checkbox is selected
if (isset($_POST["Hosteltutor_$rollnumber"]) && !isCheckboxFilled($rollnumber, 'Hosteltutor')) {
    // Update Academic Coordinator status in the same row
    $sql = "INSERT INTO verfication (rollnumber, Hosteltutor) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE Hosteltutor = 'verified'";
    $conn->query($sql);
}
        // Check if Head of the Department checkbox is selected
        if (isset($_POST["HeadOfDepartment_$rollnumber"]) && !isCheckboxFilled($rollnumber, 'HeadOfDepartment')) {
            // Update Head of Department status in the same row
            $sql = "INSERT INTO verfication (rollnumber, HeadOfDepartment) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE HeadOfDepartment = 'verified'";
            $conn->query($sql);
        }

        // Check if Tutor, Academic Coordinator, and Head of Department checkboxes are filled, then delete data from the hod table
        if (isCheckboxFilled($rollnumber, 'Tutor')  || isCheckboxFilled($rollnumber, 'AcademicCoordinator') || isCheckboxFilled($rollnumber, 'HeadOfDepartment')) {
            // Delete data from the hod table
            $delete_sql = "DELETE FROM hod WHERE rollnumber = '$rollnumber'";
            $conn->query($delete_sql);
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
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SREC</title>
    <link rel="stylesheet" href="tab1.css">
    <style>
    .checkbox-container {
      display: flex;
      justify-content: center;
      gap: 10px; /* Adjust gap as needed */
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
                        <button onclick="window.location.href='hodafterlogin.php'">Home</button>
                    </div>
                    <div class="logout">
                        <button onclick="logout()">logout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5 card">
        <h2>Uploaded Files</h2>
        <form method="post" action="" class="form">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll Number</th>
                        <th>Course</th>
                        <th>Reason</th>
                        <th>ID Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            // Display the student details
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['rollnumber']; ?></td>
                        <td><?php echo $row['course']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td>
                            <div class="download-btn-container">
                            <a href="<?php echo htmlspecialchars($row['IDProof']); ?>" class="btn btn-primary download-btn" download>Download</a>

                            </div>
                        </td>

                        <!-- Checkbox column -->
                        <td>
                        <div class="checkbox-container">
                            <!-- Checkbox for Tutor -->
                            <input type="checkbox" name="Tutor_<?php echo $row['rollnumber']; ?>" value="Tutor"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'Tutor')) ? 'checked disabled' : ''; ?>> Tutor <br>
                            <!-- Checkbox for Academic Coordinator -->
                            <input type="checkbox" name="AcademicCoordinator_<?php echo $row['rollnumber']; ?>" value="AcademicCoordinator"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'AcademicCoordinator')) ? 'checked disabled' : ''; ?>> Academic Coordinator <br>
                            <!-- Checkbox for Head of the Department -->
                            <input type="checkbox" name="HeadOfDepartment_<?php echo $row['rollnumber']; ?>" value="HeadOfDepartment"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'HeadOfDepartment')) ? 'checked disabled' : ''; ?>> Head of the Department<br>
                            <input type="checkbox" name="Hosteltutor_<?php echo $row['rollnumber']; ?>" value="Hosteltutor"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'hosteltutor')) ? 'checked disabled' : ''; ?>> Hosteltutor<br>
                        
                                
                            </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="6">No files uploaded yet.</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <button type="submit" name="submit_verification" class="custom-btn">Submit Verification</button>
    </form>
</div>
</body>
</html>
            <?php
            // Display the student details
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['rollnumber']; ?></td>
                        <td><?php echo $row['course']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td><a href="<?php echo htmlspecialchars($row['IDProof']); ?>" class="btn btn-primary" download>Download</a></td>

                        <!-- Checkbox column -->
                        <td>
                            <!-- Checkbox for Tutor -->
                            <input type="checkbox" name="Tutor_<?php echo $row['rollnumber']; ?>" value="Tutor"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'Tutor')) ? 'checked disabled' : ''; ?>> Tutor <br>
                            <!-- Checkbox for Academic Coordinator -->
                            <input type="checkbox" name="AcademicCoordinator_<?php echo $row['rollnumber']; ?>" value="AcademicCoordinator"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'AcademicCoordinator')) ? 'checked disabled' : ''; ?>> Academic Coordinator <br>
                            <!-- Checkbox for Head of the Department -->
                            <input type="checkbox" name="HeadOfDepartment_<?php echo $row['rollnumber']; ?>" value="HeadOfDepartment"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'HeadOfDepartment')) ? 'checked disabled' : ''; ?>> Head of the Department<br>
                           <input type="checkbox" name="Hosteltutor_<?php echo $row['rollnumber']; ?>" value="Hosteltutor"
                                   <?php echo (isCheckboxFilled($row['rollnumber'], 'hosteltutor')) ? 'checked disabled' : ''; ?>> Hosteltutor<br>
                         
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="6">No files uploaded yet.</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </form>
</div>
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
        window.location.href = 'hodlogin.php'; // Redirect to login page
      } else {
        alert('Failed to destroy session');
      }
    }
  };
  xhr.send();
}
</script>
<?php
$conn->close();
?>
</body>
</html>
