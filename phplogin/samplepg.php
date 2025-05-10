<?php
require('tcpdf/tcpdf.php');
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
    $sql = "SELECT * FROM pgadmin WHERE rollnumber = '$rollnumber'";
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
        $pdf->SetMargins(18,5, 18); 
        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('times', '', 11);

        // Extract data from the row
        $name = strtoupper($row['name']); // Capitalize the name
        $rollnumber = strtoupper($row['rollnumber']); // Capitalize the roll number
        $year = strtoupper($row['year']);
        $reason = strtoupper($row['reason']);
        $course = strtoupper($row['course']);
        $year = $row['year'];
        $hd = $row['hd'];
        $Years = ($row['Years']);
        $increment=$Years+1;
        $gender = $row['gender'];
        if($gender=='Male'){
            $g ='his';
            $m='him';
        }
        else{
            $g='her';
            $m='her';
        }


        // Add the first image to the top left
        $imagePathLeft = "https://srec.ac.in/uploads/resource/src/8yeEAIUofd01022018043456srec-logo.jpg" ;// Replace with the actual path to your left image
        $pdf->Image($imagePathLeft, 18, 27, 20, 20); // Adjust the coordinates and dimensions as needed

        // Add the second image to the top right
        $imagePathRight = "https://srec.ac.in/ABMDB2023/SNR.jpg"; // Replace with the actual path to your right image
        $pdf->Image($imagePathRight, $pdf->getPageWidth() - 36, 27, 20, 20); // Adjust the coordinates and dimensions as needed
        $currentDate = date('d-m-Y');
        $html = "
<style>
  * {
    text-align: left;
  }
  h1, h3 {
    text-align: center;
    text size: 14px;
    margin-bottom:0.5px;
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
    text-align: justify;
    font-size: 13px;
    margin-right:12px;
    margin-left:12px; /* Adjust the margin-bottom as needed */
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
<h3><div class ='underline'>BONAFIDE CERTIFICATE </div></h3>
<p> Certified that <strong class='underline'>$name</strong> <strong class='underline'>($rollnumber)</strong> is a bonafide student of Sri Ramakrishna Engineering College and studying<strong class='underline'> $year </strong> $course during the academic year - 2024.</p>
<div></div>
<p>This certificate is issued to enable her to apply for <strong>$reason</strong> on their own request.</p>
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
