
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
    $sql = "SELECT * FROM academiccoordinator WHERE acmail = '$username'";
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
        if (isCheckboxFilled($rollnumber, 'Tutor')  && isCheckboxFilled($rollnumber, 'AcademicCoordinator')) {
            // Delete data from the hod table
            $delete_sql = "DELETE FROM academiccoordinator WHERE rollnumber = '$rollnumber'";
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grid Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style9.css">
</head>

<body>
    <div class="layout">
        <header>
            <img src="footer-logo.png" alt="SREC Logo" class="navbar-logo">
        </header>        
        <div class="logout">
            <button onclick="logout()">Logout</button>
        </div>
        <div class="logout1">
            <button onclick="logout()">Home</button>
        </div>
        <main>
            <body class="form-login-body">
                <div class="container-fluid">
                    <br>
                    <br>
                <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Roll Number</th>
                            <th>Course</th>
                            <th>Reason</th>
                            <th>Download</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                        <td><a href="<?php echo $row['IDProof']; ?>" class="btn btn-primary download-btn" download>Download</a></td>
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
                            </div>
                        </td>
            <td>
                <form id="rejectForm_<?php echo $row['rollnumber']; ?>" action="" method="post"
                    style="display: flex;">
                    <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                    <button type="submit" class="btn btn-primary download-btn" name="reject">Reject</button>
                </form>
                <div id="buttonContainer_<?php echo $row['rollnumber']; ?>" style="display: flexs;">
                    <form action="passedbonafide.php" method="post" style="display: flex;">
                        <input type="hidden" name="rollnumber" value="<?php echo $row['rollnumber']; ?>">
                        <button type="submit" class="btn btn-primary download-btn">Generate PDF</button>
                    </form>
                    <form id="approveForm_<?php echo $row['rollnumber']; ?>" action="" method="post"
                        style="display: flex;">
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
        if (xhr.responseText.trim() === 'success') {
            alert('Session destroyed successfully');
            window.location.href = 'aclogin.php';
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
</div>
</body>
</html>
         