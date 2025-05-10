<?php
require('tcpdf/tcpdf.php');

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
        // Fetch the data for the student
        $row = $result->fetch_assoc();
        
        // Initialize TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Head of Institution/School');
        $pdf->SetTitle('Bonafide Certificate');
        $pdf->SetSubject('Bonafide Certificate');
        $pdf->SetKeywords('Bonafide, Certificate, School');

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('times', '', 11);

        // Extract data from the row
        $name = ($row['name']); // Capitalize the name
        $rollnumber = ($row['rollnumber']); // Capitalize the roll number
        $year = ($row['year']);
        $reason = ($row['reason']);
        $course = ($row['course']);
        $hd = $row['hd'];
        $Years = $row['Years'];

        // Add the first image to the top left
        $imagePathLeft = "l.jpg"; // Replace with the actual path to your left image
        $pdf->Image($imagePathLeft, 10, 27, 40, 30); // Adjust the coordinates and dimensions as needed

        // Add the second image to the top right
        $imagePathRight = "r.jpg"; // Replace with the actual path to your right image
        $pdf->Image($imagePathRight, $pdf->getPageWidth() - 36, 27, 20, 20); // Adjust the coordinates and dimensions as needed

        $currentDate = date('d-m-Y');

        // Fetch fee structure details from the gq1st table
        $sql_fee = "SELECT * FROM mqds";
        $result_fee = $conn->query($sql_fee);

        $fee_structure = "";
        $total_fee = 0;
        if ($result_fee->num_rows > 0) {
            while ($row_fee = $result_fee->fetch_assoc()) {
                $fee_structure .= "
                <tr>
                    <td>{$row_fee['Particulars']}</td>
                    <td>{$row_fee['I_Year']}</td>
                    <td>{$row_fee['II_Year']}</td>
                    <td>{$row_fee['III_Year']}</td>
                    <td>{$row_fee['IV_Year']}</td>
                    <td>{$row_fee['Total']}</td>
                </tr>";
            }
        } else {
            $fee_structure = "
            <tr>
                <td colspan='6'>No fee structure data available.</td>
            </tr>";
        }

        $html = "
        <style>
        * {
          text-align: left;
        }
        h1,h5 {
          text-align: center;
          text size: 14px;
          margin-bottom:0.5px;
        }
        h3{
          text-align: center;
          text size: 14px;
          margin-bottom:0.5px;
          text-decoration:underline;
        }
        .logo {
          position: absolute;
          top: 10mm;
        }
        .underline {
          text-decoration: underline;
        }
        h2 {
          text-align: center;
          font-size: 10px;
          font-weight: lighter;
        }
        p {
          font-weight: lighter;
          text-align: left;
          font-size: 13px;
          margin-right:12px;
          margin-left:17px; /* Adjust the margin-bottom as needed */
        }
        h4 {
          text-align: right;
          font-size: 12px;
          margin-right:14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 15px;
            text-align: right;
            font-size:12px;
        }
        </style>
        <div></div>
        <h1>SRI RAMAKRISHNA ENGINEERING COLLEGE</h1>
        <h2>[Autonomous Institution, Reaccredited by NAAC with 'A+' Grade]<div>[Approved by AICTE and Permanently Affiliated to Anna University, Chennai]<div>
        [ISO 9001:2015 Certified and all eligible programmes Accredited by NBA]</div>VATTAMALAIPALAYAM, N.G.G.O. COLONY POST, COIMBATORE - 641 022.</h2>
        <div></div>
        <h4> DATE: $currentDate</h4>
        <div></div>
        <h3><strong>TO WHOMSOEVER IT MAY CONCERN </strong></h3>
        <p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Certified that <strong class='underline'>$name</strong> <strong class='underline'>($rollnumber)</strong> is a bonafide student of Sri Ramakrishna Engineering College and studying<strong class='underline'> $year </strong> $course during the academic year - $Years.</p>
        <div></div>
        <p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;This certificate is issued to enable her to apply for <strong>$reason</strong> on their own request.</p>
        <div></div>
        <h5><div class ='underline'>FEE STRUCTURE</div></h5>
        <table>
            <tr>
                <th>Particulars</th>
                <th>I YEAR (INR)</th>
                <th>II YEAR (INR)</th>
                <th>III YEAR (INR)</th>
                <th>IV YEAR (INR)</th>
                <th>Total (INR)</th>
            </tr>
            $fee_structure
            
        </table>
        <br><br><br>
        ";

        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF as a download
        $pdf->Output('Bonafide_Certificate_' . $row['rollnumber'] . '.pdf', 'D');
    } else {
        echo "No data found for the given roll number.";
    }
} else {
    echo "Roll number not provided.";
}

// Close database connection
$conn->close();
?>
