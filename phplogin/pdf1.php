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

        // Fetch fee structure details from the gq1st table
        $fee_sql = "SELECT * FROM gq1st";
        $fee_result = $conn->query($fee_sql);

        $fee_data = [];
        if ($fee_result->num_rows > 0) {
            while ($fee_row = $fee_result->fetch_assoc()) {
                $fee_data[] = $fee_row;
            }
        } else {
            echo "No fee structure data found.";
            exit;
        }
        
        // Fetch fee structure details from the gq1st table
        $fee_sql = "SELECT * FROM hostel";
        $fee_results = $conn->query($fee_sql);

        $fee_datas = [];
        if ($fee_results->num_rows > 0) {
            while ($fee_rows = $fee_results->fetch_assoc()) {
                $fee_datas[] = $fee_rows;
            }
        } else {
            echo "No fee structure data found.";
            exit;
        }


        // Initialize TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Head of Institution/School');
        $pdf->SetTitle('Bonafide Certificate');
        $pdf->SetSubject('Bonafide Certificate');
        $pdf->SetKeywords('Bonafide, Certificate, School');
        $pdf->setPrintHeader(false);
        $pdf->SetMargins(18,5, 18); 
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
        $Years = $row['Years'];
        $hd = $row['hd'];
        $yearincrement = $Years + 1;

        // Add the first image to the top left
        $imagePathLeft = "l.jpg"; // Replace with the actual path to your left image
        $pdf->Image($imagePathLeft, 10, 27, 40, 30); // Adjust the coordinates and dimensions as needed

        // Add the second image to the top right
        $imagePathRight = "r.jpg"; // Replace with the actual path to your right image
        $pdf->Image($imagePathRight, $pdf->getPageWidth() - 36, 27, 20, 20); // Adjust the coordinates and dimensions as needed
        $currentDate = date('d-m-Y');
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
          h3 {
          text-align: center;
          text size: 14px;
          margin-bottom:0.5px;
          text-decoration: underline;
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
          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 15px;
            text-align: left;
            font-size:12px;
        }
      </style>
      <div></div>
      <h1>SRI RAMAKRISHNA ENGINEERING COLLEGE</h1>
      <h2>[Autonomous Institution, Reaccredited by NAAC with 'A+' Grade]<div>[Approved by AICTE and Permanently Affiliated to Anna University, Chennai]<div>
      [ISO 9001:2015 Certified and all eligible programmes Accredited by NBA]</div>VATTAMALAIPALAYAM, N.G.G.O. COLONY POST, COIMBATORE - 641 022.</h2>
      <h4>DATE:$currentDate</h4>
      <h3>TO WHOMSOEVER IT MAY CONCERN</h3>
      <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Certified that <strong class='underline'>$name</strong> <strong class='underline'>($rollnumber)</strong> is a bonafide student of Sri Ramakrishna Engineering College and studying <strong class='underline'> $year</strong> $course during the academic  $Years-$yearincrement</p>
    
      <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;The following expenses will occur in first, second, third, and final years of the B.E degree course.</p>
   
      <h3>FEE STRUCTURE</h3>
      <table>
        <tr>
            <th><b>Particulars</b></th>
            <th><b>I YEAR (INR)</b></th>
            <th><b>II YEAR (INR)</b></th>
            <th><b>III YEAR (INR)</b></th>
            <th><b>IV YEAR (INR)</b></th>
            <th><b>Total (INR)</b></th>
        </tr>";
        
        // Add the fee structure data
        foreach ($fee_data as $fee_row) {
            $html .= "
            <tr>
                <td>{$fee_row['Particulars']}</td>
                <td>{$fee_row['I_Year']}</td>
                <td>{$fee_row['II_Year']}</td>
                <td>{$fee_row['III_Year']}</td>
                <td>{$fee_row['IV_Year']}</td>
                <td>{$fee_row['Total']}</td>
            </tr>";
        }

        $html .= "</table>";

        $html .= "<h3> HOSTEL FEE STRUCTURE</h3>
        <table>
        <tr>
            <th><b>Particulars</b></th>
            <th><b>I YEAR (INR)</b></th>
            <th><b>II YEAR (INR)</b></th>
            <th><b>III YEAR (INR)</b></th>
            <th><b>IV YEAR (INR)</b></th>
            <th><b>Total (INR)</b></th>
        </tr>";

        // Add the hostel fee structure data
      // Add the hostel fee structure data
      $last_row_index = count($fee_datas) - 1;
      foreach ($fee_datas as $index => $fee_rows) {
          if ($index == $last_row_index) {
            $html .= "
           <tr>
               <td><b>{$fee_rows['Particulars']}</b></td>
                <td><b>{$fee_rows['I_year']}</b></td>
                <td><strong>{$fee_rows['II_year']}</strong></td>
                <td><strong>{$fee_rows['III_year']}</strong></td>
                <td><strong>{$fee_rows['IV_year']}</strong></td>
                <td><strong>{$fee_rows['Total']}</strong></td>
            </tr>";
        } else {
          $html .= "
          <tr>
           <td>{$fee_rows['Particulars']}</td>
                <td>{$fee_rows['I_year']}</td>
                <td>{$fee_rows['II_year']}</td>
                <td>{$fee_rows['III_year']}</td>
                <td>{$fee_rows['IV_year']}</td>
                <td>{$fee_rows['Total']}</td>
        
                </tr>";
        }
      }

        $html .= "</table>";

        $html .= "
        <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;This certificate is issued to enable her to apply for $reason on their own request.</p>
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
