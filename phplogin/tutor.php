<?php
session_start();
// Check if the session is started and if the username is set
if (!isset($_SESSION['username'])) {
    echo "Session not started or username not set!";
    exit;
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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming $_SESSION['username'] holds the login username
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM tutor
            INNER JOIN login ON tutor.tmail = login.username
            WHERE login.username = '$username'";
    $result = $conn->query($sql);

    // Check if the form was submitted
    if (isset($_POST['submit_verification'])) {
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

            // Check if Head of the Department checkbox is selected
            if (isset($_POST["HeadOfDepartment_$rollnumber"]) && !isCheckboxFilled($rollnumber, 'HeadOfDepartment')) {
                // Update Head of Department status
                $sql = "INSERT INTO verfication (rollnumber, HeadOfDepartment) VALUES ('$rollnumber', 'verified') ON DUPLICATE KEY UPDATE HeadOfDepartment = 'verified'";
                $conn->query($sql);
            }

            // Check if Tutor checkbox is filled, then delete data from the verification table
            if (isset($_POST["Tutor_$rollnumber"]) && isCheckboxFilled($rollnumber, 'Tutor')) {
                // Delete data from the verification table
                $delete_sql = "DELETE FROM tutor WHERE rollnumber = '$rollnumber'";
                $conn->query($delete_sql);
            }
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
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Uploaded Files</h2>
    <button class="btn btn-danger logout-btn" onclick="logout()">Logout</button> <!-- Logout button -->
    <form method="post" action="">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Roll Number</th>
                <th>Course</th>
                <th>Reason</th>
                <th>ID Proof</th>
                <th>Action</th> <!-- Added column for checkboxes -->
            </tr>
            </thead>
            <tbody>
            <?php
            // Assuming $_SESSION['username'] holds the login username
            $username = $_SESSION['username'];
            $sql = "SELECT * FROM tutor
                    INNER JOIN login ON tutor.tmail = login.username
                    WHERE login.username = '$username'";
            $result = $conn->query($sql);

            // Display the student details
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['rollnumber']; ?></td>
                        <td><?php echo $row['course']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td><a href="<?php echo htmlspecialchars($row['IDproof']); ?>" class="btn btn-primary" download>Download</a></td>

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
        <button type="submit" name="submit_verification" class="btn btn-primary">Submit Verification</button>
    </form>
</div>

<script>
    function logout() {
        // Redirect to the logout page
        window.location.href = 'logout.php';
    }
</script>

<?php
$conn->close();
?>
</body>
</html>
