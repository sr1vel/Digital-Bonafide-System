<?php
require('tcpdf/tcpdf.php');
// Custom class extending TCPDF to add a footer
class CustomPDF extends TCPDF {
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Footer text
        $this->Cell(0, 10, 'This is a computer-generated document.', 0, 0, 'C');
    }
}
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
    $sql = "SELECT * FROM passedadmin WHERE rollnumber = '$rollnumber'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the data for the student
        $row = $result->fetch_assoc();
        
        // Initialize TCPDF
        $pdf = new CustomPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Head of Institution/School');
        $pdf->SetTitle('Bonafide Certificate');
        $pdf->SetSubject('Bonafide Certificate');
        $pdf->SetKeywords('Bonafide, Certificate, School');
        $pdf->setPrintHeader(false);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('times', '', 11);

        // Extract data from the row
        $name = ($row['name']); // Capitalize the name
        $rollnumber = ($row['rollnumber']); // Capitalize the roll number
       
        $reason = ($row['reason']);
        $course = ($row['course']);
        $gender = $row['gender'];
        $admission = $row['admission'];
        $completion = $row['completion'];
        if($gender=='Male'){
          $g ='his';
          $m='him';
          $k='His';
      }
      else{
          $g='her';
          $m='her';
          $k='Her';
      }

        


        // Add the first image to the top left
        $imagePathLeft = "left1.png" ;// Replace with the actual path to your left image
        $pdf->Image($imagePathLeft, 10, 27, 25, 28); // Adjust the coordinates and dimensions as needed

        // Add the second image to the top right
        $imagePathRight = "right.png"; // Replace with the actual path to your right image
        $pdf->Image($imagePathRight, $pdf->getPageWidth() - 36, 27, 20, 20); // Adjust the coordinates and dimensions as needed
        $currentDate = date('d-m-Y');
        $html = "
<style>
  * {
    text-align: left;
  }
  h1 {
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
</style>
<div></div>
<h1>SRI RAMAKRISHNA ENGINEERING COLLEGE</h1>
<h2>[Autonomous Institution, Reaccredited by NAAC with 'A+' Grade]<div>[Approved by AICTE and Permanently Affiliated to Anna University, Chennai]<div>
[ISO 9001:2015 Certified and all eligible programmes Accredited by NBA]</div>VATTAMALAIPALAYAM, N.G.G.O. COLONY POST, COIMBATORE - 641 022.</h2>
<div></div>
<h4> DATE: $currentDate</h4>
<div></div>
<h3><strong>TO WHOMSOEVER IT MAY CONCERN </strong></h3>
<p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Certified that <strong class='underline'>$name</strong> <strong class='underline'>($rollnumber)</strong> was a bonafide student of Sri Ramakrishna Engineering College and studied $course during the academic year $admission-$completion.$k conduct and character was <b>GOOD</b>.</p>
<div></div>
<p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;$k medium of instruction is <b>English</b> during the period study </p>
<p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;This certificate is issued to enable $m to apply for <strong>$reason</strong> on $g own request.</p>
<div></div>
<div></div>
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
