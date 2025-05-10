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
    $sql = "SELECT * FROM student WHERE rollnumber = '$rollnumber'";
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
        $name = $row['name']; 
        $rollnumber = $row['rollnumber']; 
        $year = $row['year'];
        $reason = $row['reason'];
        $course = $row['course'];
        $hd = $row['hd'];
        $Years = $row['Years'];
        $gender = $row['gender'];
        if($gender=='Male'){
            $g ='his';
            $m='him';
        }
        else{
            $g='her';
            $m='her';
        }

        $YearsIncremented = $Years + 1;
        if($course=='B.Tech. Information Technology'){
            $b='B.Tech';
        }else if($course=='M.Tech. Computer Science and Engineering'){
            $b='M.Tech';
        }
        else{
            $b='B.E';
        }

        // Add the first image to the top left
        $imagePathLeft = "l.jpg"; 
        $pdf->Image($imagePathLeft, 10, 27, 40, 30); 

        // Add the second image to the top right
        $imagePathRight = "r.jpg"; 
        $pdf->Image($imagePathRight, $pdf->getPageWidth() - 36, 27, 20, 20); 

        $currentDate = date('d-m-Y');

        // Fetch fee structure details from the ugstudents table
        $sql_fee = "SELECT `Tuition_Fee`, `Miscellaneous_Fee`, `Total_fee_to_be_paid` FROM ugstudents WHERE rollnumber = '$rollnumber'";
        $result_fee = $conn->query($sql_fee);

        $fee_structure = "";
        if ($result_fee->num_rows > 0) {
            $row_fee = $result_fee->fetch_assoc();
            $fee_structure = "
            <tr>
                <td>Tuition Fee</td>
                <td style='text-align: right;'>{$row_fee['Tuition_Fee']}</td>
                 <td style='text-align: right;'>{$row_fee['Tuition_Fee']}</td>
                  <td style='text-align: right;'>{$row_fee['Tuition_Fee']}</td>
                  <td style='text-align: right;'>{$row_fee['Tuition_Fee']}</td>

            </tr>
            <tr>
                <td>Miscellaneous Fee</td>
                <td style='text-align: right;'>{$row_fee['Miscellaneous_Fee']}</td>
                        <td style='text-align: right;'>{$row_fee['Miscellaneous_Fee']}</td>
                                <td style='text-align: right;'>{$row_fee['Miscellaneous_Fee']}</td>
                                <td style='text-align: right;'>{$row_fee['Miscellaneous_Fee']}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td style='text-align: right;'><strong>{$row_fee['Total_fee_to_be_paid']}</strong></td>
                    <td style='text-align: right;'><strong>{$row_fee['Total_fee_to_be_paid']}</strong></td>
                        <td style='text-align: right;'><strong>{$row_fee['Total_fee_to_be_paid']}</strong></td>
                         <td style='text-align: right;'><strong>{$row_fee['Total_fee_to_be_paid']}</strong></td>
            </tr>";
        } else {
            $fee_structure = "
            <tr>
                <td colspan='2'>No fee structure data available.</td>
            </tr>";
        }

        // Fetch hostel fee structure details from the hostel table
        $fee_sql = "SELECT * FROM hostel";
        $fee_results = $conn->query($fee_sql);
    
        $fee_datas = [];
        if ($fee_results->num_rows > 0) {
            while ($fee_rows = $fee_results->fetch_assoc()) {
                $fee_datas[] = $fee_rows;
            }
        } else {
            echo "No hostel fee structure data found.";
            exit;
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
            width: 60%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-left: 40px;
        }
        th {
            border: 1px solid #000;
            padding: 15px;
            text-align: left;
            font-size:12px;
            margin-left: 40px;
        }
        td {
            border: 1px solid #000;
            padding: 15px;
            font-size:12px;
            text-align:left;
            margin-left: 40px;
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
        <p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Certified that <strong class='underline'>$name</strong> <strong class='underline'>($rollnumber)</strong> is a bonafide student of Sri Ramakrishna Engineering College and studying<strong class='underline'> $year </strong> $course during the academic year $Years - $YearsIncremented.</p>
        <div></div>
        <p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;The following expenses will be incurred during $year $b degree course($Years - $YearsIncremented)</p>
        <h3></h3>
       &nbsp; &nbsp; &nbsp; &nbsp;<table>
            &nbsp; &nbsp; &nbsp; &nbsp;<tr>
                &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>Particulars</strong></th>
                &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>II year</strong></th>
                  &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>III year</strong></th>
                    &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>IV year</strong></th>
                     &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>V year</strong></th>
           &nbsp; &nbsp; &nbsp; &nbsp;</tr>
            $fee_structure
        &nbsp; &nbsp; &nbsp; &nbsp;</table>
        <h3></h3>
        &nbsp; &nbsp; &nbsp; &nbsp;<table>
        &nbsp; &nbsp; &nbsp; &nbsp;<tr>
            &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>Hostel Fee</strong></th>
            &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>II year</strong></th>
              &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>III year</strong></th>
                &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>IV year</strong></th>
                 &nbsp; &nbsp; &nbsp; &nbsp;<th><strong>V year</strong></th>
        &nbsp; &nbsp; &nbsp; &nbsp;</tr>";

        // Add the hostel fee structure data
        $last_row_index = count($fee_datas) - 1;
        foreach ($fee_datas as $index => $fee_rows) {
            if ($index == $last_row_index) {
                $html .= "
                <tr>
                    <td><strong>{$fee_rows['Particulars']}</strong></td>
                    <td><strong>{$fee_rows['II_year']}</strong></td>
                    <td><strong>{$fee_rows['II_year']}</strong></td>
                    <td><strong>{$fee_rows['II_year']}</strong></td>
                     <td><strong>{$fee_rows['II_year']}</strong></td>
                </tr>";
            } else {
                $html .= "
                <tr>
                    <td>{$fee_rows['Particulars']}</td>
                    <td>{$fee_rows['II_year']}</td>
                     <td>{$fee_rows['II_year']}</td>
                      <td>{$fee_rows['II_year']}</td>
                       <td>{$fee_rows['II_year']}</td>


                </tr>";
            }
        }

        $html .= "</table>";
        $html.="<p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;This certificate is issued to enable $m to apply for <strong>$reason</strong> on their $g request.</p>";

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
