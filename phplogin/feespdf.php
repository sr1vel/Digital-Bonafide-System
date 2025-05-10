<?php

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

// Retrieve roll number from the form submission
if (isset($_POST['rollnumber'])) {
    $rollnumber = $_POST['rollnumber'];

    // Fetch student details from the database based on roll number
    $sql = "SELECT * FROM student WHERE rollnumber = '$rollnumber'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch associative array of the result
        $student = $result->fetch_assoc();
        $adtype = $student['adtype']; // Fetch adtype from the result
        $prevdetails = $student['prevdetails']; // Fetch previousdet from the result
        $course = $student['course']; // Fetch course from the result
    } else {
        echo "No student found with the provided roll number.";
        exit;
    }
}

// Check if the form has been submitted and if adtype is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($adtype)) {
    // Retrieve form data
    $year = $_POST['year'];
    $hd = $_POST['hd'];

    // Determine the file path based on year, residential status, adtype, previousdet, and course
    if ($year == 'First Year') {
        if ($hd == 'Day Scholar') {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('mtech1day.php'); // Include PDF for MTech first-year day scholar with no previous details
                } else {
                    include_once('othercourse1day.php'); // Include PDF for other courses first-year day scholar with no previous details
                }
            } else {
                include_once('pdf1day.php'); // Include general PDF for first-year day scholar
            }
        } else {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('mtech1hostel.php'); // Include PDF for MTech first-year hostel student with no previous details
                } else {
                    include_once('othercourse1hostel.php'); // Include PDF for other courses first-year hostel student with no previous details
                }
            } else {
                include_once('pdf1.php'); // Include general PDF for first-year hostel student
            }
        }
    } else if ($year == 'Second Year') {
        if ($hd == 'Day Scholar') {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('mtech2.php'); // Include PDF for MTech first-year day scholar with no previous details
                } else {
                    include_once('2daypdf.php'); // Include PDF for other courses first-year day scholar with no previous details
                }
            } else {
                include_once('bonafidefees.php'); // Include general PDF for first-year day scholar
            }
        } else {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('mtech2hostel.php'); // Include PDF for MTech first-year hostel student with no previous details
                } else {
                    include_once('2pdf.php'); // Include PDF for other courses first-year hostel student with no previous details
                }
            } else {
                include_once('pdf2.php'); // Include general PDF for first-year hostel student
            }
        }
    } else if ($year == 'Third Year') {
        if ($hd == 'Day Scholar') {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('mtech3.php'); // Include PDF for MTech first-year day scholar with no previous details
                } else {
                    include_once('3daypdf.php'); // Include PDF for other courses first-year day scholar with no previous details
                }
            } else {
                include_once('bonafidefees.php'); // Include general PDF for first-year day scholar
            }
        } else {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('mtech3hostel.php'); // Include PDF for MTech first-year hostel student with no previous details
                } else {
                    include_once('3pdf.php'); // Include PDF for other courses first-year hostel student with no previous details
                }
            } else {
                include_once('pdf3.php'); // Include general PDF for first-year hostel student
            }
        }
    } else if ($year == 'Fourth Year') {
        if ($hd == 'Day Scholar') {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('4daypdf.php'); // Include PDF for MTech first-year day scholar with no previous details
                } else {
                    include_once('bonafidefees.php'); // Include PDF for other courses first-year day scholar with no previous details
                }
            } else {
                include_once('bonafidefees.php'); // Include general PDF for first-year day scholar
            }
        } else {
            if ($prevdetails == 'No') {
                if ($course == 'M.Tech. Computer Science and Engineering') {
                    include_once('4pdf.php'); // Include PDF for MTech first-year hostel student with no previous details
                } else {
                    include_once('pdf4.php'); // Include PDF for other courses first-year hostel student with no previous details
                }
            } else {
                include_once('pdf4.php'); // Include general PDF for first-year hostel student
            }
        }
    } elseif ($year == 'Fifth Year') {
        if ($hd == 'Day Scholar') {
            include_once('bonafidefees.php'); // Include general PDF for fifth-year day scholar
        } else {
            include_once('pdf5.php'); // Include general PDF for fifth-year hostel student
        }
    } else {
        // Invalid year
        echo "Invalid year";
    }
}

// Close the database connection
$conn->close();
?>
