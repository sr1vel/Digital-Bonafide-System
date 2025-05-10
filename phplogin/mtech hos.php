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
        $name = strtoupper($row['name']); // Capitalize the name
        $rollnumber = strtoupper($row['rollnumber']); // Capitalize the roll number
        $year = strtoupper($row['year']);
        $reason = strtoupper($row['reason']);
        $course = strtoupper($row['course']);
        $year = $row['year'];
        $hd = $row['hd'];


        // Add the first image to the top left
        $imagePathLeft ="l.jpg"; // Replace with the actual path to your left image
        $pdf->Image($imagePathLeft, 18, 27, 20, 20); // Adjust the coordinates and dimensions as needed

        // Add the second image to the top right
        $imagePathRight = "r.jpg"; // Replace with the actual path to your right image
        $pdf->Image($imagePathRight, $pdf->getPageWidth() - 36, 27, 20, 20); // Adjust the coordinates and dimensions as needed
        $currentDate = date('d-m-Y');
        $html = "
        <style>
        * {
          text-align: left;
        }
        h1, h3,h5 {
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
          text-align: center;
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
      <h4>$currentDate</h4>
      <h3><div class ='underline'>BONAFIDE CERTIFICATE </div></h3>
      <p> Certified that <strong class='underline'>$name</strong> <strong class='underline'>($rollnumber)</strong> is a bonafide student of Sri Ramakrishna Engineering College and studying <strong class='underline'> $year </strong> $course during the academic year 2023-2024.</p>
      <p>This certificate is issued to enable her to apply for $reason on their own request.</p>
      <p>The following expenses will occur in first,second,third,final years of B.E degree course.</p>
      
      <div></div>
      <h5><div class ='underline'>FEE STRUCTURE</div></h5>
      <table style='width:100%; border-collapse: collapse; margin-top: 10px;'>
          <tr>
              <th style='border: 1px solid #000; padding: 8px;'>Particulars</th>
              <th style='border: 1px solid #000; padding: 8px;'>I YEAR (INR)</th>
              <th style='border: 1px solid #000; padding: 8px;'>II YEAR (INR)</th>
              <th style='border: 1px solid #000; padding: 8px;'>III YEAR (INR)</th>
              <th style='border: 1px solid #000; padding: 8px;'>IV YEAR (INR)</th>
              <th style='border: 1px solid #000; padding: 8px;'>V YEAR (INR)</th>
              <th style='border: 1px solid #000; padding: 8px;'>Total (INR)</th>
          </tr>
          <tr>
              <td style='border: 1px solid #000; padding: 8px;'>Tuition Fee</td>
              <td style='border: 1px solid #000; padding: 8px;'>1,40,000</td>
              <td style='border: 1px solid #000; padding: 8px;'>1,40,000</td>
              <td style='border: 1px solid #000; padding: 8px;'>1,40,000</td>
              <td style='border: 1px solid #000; padding: 8px;'>1,40,000</td>
              <td style='border: 1px solid #000; padding: 8px;'>7,00,000</td>
          </tr>
          <tr>
              <td style='border: 1px solid #000; padding: 8px;'>Devlopement Fee</td>
              <td style='border: 1px solid #000; padding: 8px;'>5000</td>
              <td style='border: 1px solid #000; padding: 8px;'>5000</td>
              <td style='border: 1px solid #000; padding: 8px;'>5000</td>
              <td style='border: 1px solid #000; padding: 8px;'>5000</td>
              <td style='border: 1px solid #000; padding: 8px;'>5000</td>
              <td style='border: 1px solid #000; padding: 8px;'>25000</td>
          </tr>
          <tr>
          <td style='border: 1px solid #000; padding: 8px;'>placement&training Fee</td>
          <td style='border: 1px solid #000; padding: 8px;'>15000</td>
          <td style='border: 1px solid #000; padding: 8px;'>15000</td>
          <td style='border: 1px solid #000; padding: 8px;'>15000</td>
          <td style='border: 1px solid #000; padding: 8px;'>15000</td>
          <td style='border: 1px solid #000; padding: 8px;'>15000</td>
          <td style='border: 1px solid #000; padding: 8px;'>75000</td>
      </tr>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'>University&other Fee</td>
      <td style='border: 1px solid #000; padding: 8px;'>2500</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>2500</td>
      </tr>
      <p>------------------------------------------------------------------------------------------------------------------</p>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'></td>
      <td style='border: 1px solid #000; padding: 8px;'><strong>162000</strong></td>
      <td style='border: 1px solid #000; padding: 8px;'><strong>160000</strong></td>
      <td style='border: 1px solid #000; padding: 8px;'><strong>160000</strong></td>
      <td style='border: 1px solid #000; padding: 8px;'><strong>160000</strong></td>s
      <td style='border: 1px solid #000; padding: 8px;'><strong>160000</strong></td>
      <td style='border: 1px solid #000; padding: 8px;'><strong>802500</strong></td>
      </tr>
      <p>------------------------------------------------------------------------------------------------------------------</p>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'><strong>HOSTEL FEE</strong></td>
      <td style='border: 1px solid #000; padding: 8px;'></td>
      <td style='border: 1px solid #000; padding: 8px;'></td>
      <td style='border: 1px solid #000; padding: 8px;'></td>
      <td style='border: 1px solid #000; padding: 8px;'></td>
      <td style='border: 1px solid #000; padding: 8px;'></td>
      <td style='border: 1px solid #000; padding: 8px;'></td>
      </tr>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'>Admission Fee</td>
      <td style='border: 1px solid #000; padding: 8px;'>5000</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>-</td>
      <td style='border: 1px solid #000; padding: 8px;'>5000</td>
      </tr>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'>Room Rent</td>
      <td style='border: 1px solid #000; padding: 8px;'>19000</td>
      <td style='border: 1px solid #000; padding: 8px;'>19000</td>
      <td style='border: 1px solid #000; padding: 8px;'>19000</td>
      <td style='border: 1px solid #000; padding: 8px;'>19000</td>
      <td style='border: 1px solid #000; padding: 8px;'>19000</td>
      <td style='border: 1px solid #000; padding: 8px;'>95000</td>
      </tr>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'>Electricity and water charges</td>
      <td style='border: 1px solid #000; padding: 8px;'>16000</td>
      <td style='border: 1px solid #000; padding: 8px;'>16000</td>
      <td style='border: 1px solid #000; padding: 8px;'>16000</td>
      <td style='border: 1px solid #000; padding: 8px;'>16000</td>
      <td style='border: 1px solid #000; padding: 8px;'>16000</td>
      <td style='border: 1px solid #000; padding: 8px;'>80000</td>
      </tr>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'>Eshtablishment charges</td>
      <td style='border: 1px solid #000; padding: 8px;'>12000</td>
      <td style='border: 1px solid #000; padding: 8px;'>12000</td>
      <td style='border: 1px solid #000; padding: 8px;'>12000</td>
      <td style='border: 1px solid #000; padding: 8px;'>12000</td>
      <td style='border: 1px solid #000; padding: 8px;'>12000</td>
      <td style='border: 1px solid #000; padding: 8px;'>60000</td>
      </tr>
      <tr>
      <td style='border: 1px solid #000; padding: 8px;'>Mess Bill</td>
      <td style='border: 1px solid #000; padding: 8px;'>33000</td>
      <td style='border: 1px solid #000; padding: 8px;'>33000</td>
      <td style='border: 1px solid #000; padding: 8px;'>33000</td>
      <td style='border: 1px solid #000; padding: 8px;'>33000</td>
      <td style='border: 1px solid #000; padding: 8px;'>33000</td>
      <td style='border: 1px solid #000; padding: 8px;'>165000</td>
      </tr>
      <p>------------------------------------------------------------------------------------------------------------------</p>
      <tr>
          <td style='border: 1px solid #000; padding: 8px;'></td>
          <td style='border: 1px solid #000; padding: 8px;'><strong>85000</strong></td>
          <td style='border: 1px solid #000; padding: 8px;'><strong>80000</strong></td>
          <td style='border: 1px solid #000; padding: 8px;'><strong>80000</strong></td>
          <td style='border: 1px solid #000; padding: 8px;'><strong>80000</strong></td>
          <td style='border: 1px solid #000; padding: 8px;'><strong>80000</strong></td>
          <td style='border: 1px solid #000; padding: 8px;'><strong>400000</strong></td>
      </tr>
      
      <p>------------------------------------------------------------------------------------------------------------------</p>
      </table>
      <div></div>
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
 